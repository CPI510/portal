<?php
global $wpdb;
$_POST = json_decode(file_get_contents("php://input"), true);
//printAll($_POST);
$grinf = groupInfo($_GET['id']);

$name_var = translateDir($_GET['id']);
?>

<?php if( ($grinf->program_id == 6 || $grinf->program_id == 16) && getAccess(get_current_user_id())->access == 1 ){

    $data = $wpdb->get_results($s=$wpdb->prepare("SELECT a.id, a.date_create, a.date_update, a.create_user_id, a.listener_id, a.section_a_grade, a.section_a_description, a.section_b_grade, a.section_b_description, a.section_c_grade, a.section_c_description, a.review, a.grading_solution, 
b.trener_id, c.expert_id, d.moderator_id, e.teamleader_id, 
case 
when b.trener_id is NOT null then 1 
when c.expert_id is NOT null then 2 
when d.moderator_id is NOT null then 3
when f.independent_trainer_id is NOT null then 4
when e.teamleader_id is NOT null then 5 else 0 
end AS SORTALL
FROM p_assessment_rubric a
LEFT JOIN p_groups b ON b.id = a.group_id AND b.trener_id = a.create_user_id
LEFT JOIN p_groups c ON c.id = a.group_id AND c.expert_id = a.create_user_id
LEFT JOIN p_groups d ON d.id = a.group_id AND d.moderator_id = a.create_user_id
LEFT JOIN p_groups f ON f.id = a.group_id AND f.independent_trainer_id = a.create_user_id
LEFT JOIN p_groups e ON e.id = a.group_id AND e.teamleader_id = a.create_user_id
WHERE a.group_id = %d AND a.listener_id = %d
ORDER BY SORTALL", $_GET['id'], $_POST['fileuserdata'] ));
}elseif( ($grinf->program_id == 6 || $grinf->program_id == 16) && $grinf->expert_id == get_current_user_id() ){
    $data = $wpdb->get_results($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d ", $_GET['id'], $_POST['fileuserdata'], $grinf->trener_id ));
}
//printAll($data);
//echo $s;
?>

<div class="boxcoding"></div>

<h3><?= nameUser($_POST['fileuserdata'], 5) ?></h3>
<?php

$grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");
foreach ($grades as $grade){
    $arrGrade[$grade->id] = $grade->$name_var;
}

?>
<?php $r=0; foreach ($data as $res): $r++; ?>
    <?php

    if($r == 1 ){
        $activeclass = 'class="active"';
        $active = 'active';
    }else{
        $activeclass = '';
        $active = '';
    }

    $download_tm2 = '';
    if($grinf->trener_id == $res->create_user_id){
        $position = "Тренер";
    } elseif ($grinf->independent_trainer_id == $res->create_user_id){
        $position = "Независимый тренер";
    } elseif ($grinf->moderator_id == $res->create_user_id){
        $position = "Модератор";
    } elseif ($grinf->teamleader_id == $res->create_user_id && ($res->grading_solution == 1 || $res->grading_solution == 2) ){
        $position = "Тимлидер";
        $download_tm2 = '<a href="/export_to_word/?form=assessment_p_6&create_user_id='.$res->create_user_id.'&listener_id='.$res->listener_id.'&group='.$_GET['id'].'&ver=2" class="btn btn-primary">'.  DOWNLOAD_JUSTIFICATION .'</a>';
    } elseif ($grinf->teamleader_id == $res->create_user_id){
        $position = "Тимлидер";
    }  elseif ($grinf->expert_id == $res->create_user_id){
        $position = "Эксперт";
    }

    $allName = nameUser($res->create_user_id, 5);

    /* Этот блок решили убрать так как перестал быть нужным */
//    if($rubric_file = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_assessment_attached_file WHERE deleted = 0 AND rubric_user_id = %d AND user_id = %d AND group_id = %d AND category_id = %d",
//        $res->listener_id, $res->create_user_id, $_GET['id'], 2)) ){
//        $rubric_file_text = "<a href=\"/server_file/?assessment_sheet_file={$rubric_file->id}&assessment_sheet=1\" class=\"btn btn-info\">Скачать файл {$rubric_file->file_name} </a>";
//    }else{
//        $rubric_file_text = FILE_NOT_DOWNLOAD;
//    }

    ${'tablink_'.$r} = '<li role="presentation" '.$activeclass.'><a href="#tabdata_'.$r.'" aria-controls="tabdata_'.$r.'" role="tab" data-toggle="tab">'.$position.': '.$allName.'</a></li>';
    ${'tabdata_'.$r} = '<div role="tabpanel" class="tab-pane '.$active.'" id="tabdata_'.$r.'"><div class="card">
        <a href="/export_to_word/?form=assessment_p_6&create_user_id='.$res->create_user_id.'&listener_id='.$res->listener_id.'&group='.$_GET['id'].'" class="btn btn-primary">' . ASSESSMENT_SECOND[0] . '</a>
        '.$download_tm2.'
       '.$rubric_file_text.'
	<div class="card-body">
		<div class="panel-group" id="accordion1">
			<div class="card panel">
				<div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1'.$r.'" aria-expanded="false">
					<header>' .ASSESSMENT_SHEET[9] . ' A</header>
						<div class="tools">
							<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
						</div>
				</div>
				<div id="accordion1-1'.$r.'" class="collapse" aria-expanded="false" style="height: 0px;">
					<div class="card-body">
						' . ASSESSMENT_RUBRIC_14[1] . '
					</div>
				</div>
			</div><!--end .panel -->
		</div>
		<div class="form-group">
			<label for="select1">' . ASSESSMENT_SECOND[6] . '</label><br>' . $arrGrade[$res->section_a_grade];
    ${'tabdata_'.$r} .= ' 
		</div>
		<div class="form-group">
			<label for="textarea3">' . ASSESSMENT_SECOND[7] . '</label><br>'. $res->section_a_description . '
        </div>
	</div><!--end .card-body -->
</div><!--end .card -->

<div class="card">
	<div class="card-body">
		<div class="panel-group" id="accordion1">
			<div class="card panel">
				<div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-2'.$r.'" aria-expanded="false">
					<header>' . ASSESSMENT_SHEET[9] . ' B</header>
						<div class="tools">
							<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
						</div>
				</div>
				<div id="accordion1-2'.$r.'" class="collapse" aria-expanded="false" style="height: 0px;">
					<div class="card-body">
						' . ASSESSMENT_RUBRIC_14[3] . '
					</div>
				</div>
			</div><!--end .panel -->
		</div>
		<div class="form-group">
										<label for="select1">' . ASSESSMENT_SECOND[6] . '</label><br>' . $arrGrade[$res->section_b_grade];

    ${'tabdata_'.$r} .= ' 
		</div>
		<div class="form-group">
			<label for="textarea3">' . ASSESSMENT_SECOND[7] . '</label><br>
			'. $res->section_b_description . '
		</div>
	</div><!--end .card-body -->
</div><!--end .card -->

<div class="card">
	<div class="card-body">
		<div class="panel-group" id="accordion1">
			<div class="card panel">
				<div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-3'.$r.'" aria-expanded="false">
					<header>' . ASSESSMENT_SHEET[9] . ' C</header>
					<div class="tools">
						<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
					</div>
				</div>
				<div id="accordion1-3'.$r.'" class="collapse" aria-expanded="false" style="height: 0px;">
					<div class="card-body">

						' . ASSESSMENT_RUBRIC_14[5] . '
					</div>
				</div>
			</div><!--end .panel -->
		</div>
		<div class="form-group">
			<label for="select1">' . ASSESSMENT_SECOND[6] . '</label><br>' . $arrGrade[$res->section_c_grade];

    ${'tabdata_'.$r} .= ' 
		</div>
		<div class="form-group">
			<label for="textarea3">' . ASSESSMENT_SECOND[7] . '</label><br>
			'. $res->section_c_description .'
		</div>

	</div><!--end .card-body -->

</div><!--end .card -->
 
 <div class="card">
	<div class="card-body">
	<b>' . ASSESSMENT_SECOND[3] . ' </b>
		<div class="form-group">
			<label for="select1">' . ASSESSMENT_SECOND[4] . '</label><br>';
    foreach ($grades as $grade):
        if( $grade->id == $res->grading_solution ):
            ${'tabdata_'.$r} .=   $grade->$name_var;
        endif;
    endforeach;

    ${'tabdata_'.$r} .= ' 
		</div>
		<div class="form-group">
			<label for="textarea3">' . ASSESSMENT_SECOND[5] . '</label><br>
			'. $res->review .'
		</div>

	</div><!--end .card-body -->

</div><!--end .card -->
</div>';

    ?>

<?php endforeach; ?>

<div class="modal-body">
    <ul class="nav nav-tabs" role="tablist">
        <?php $r=0; foreach ($data as $res){ $r++;
            echo ${'tablink_'.$r};
        } ?>
    </ul>
    <div class="tab-content">
        <?php $r=0; foreach ($data as $res){ $r++;
            echo ${'tabdata_'.$r};
        } ?>
    </div>
</div>


