<?php
global $wpdb;
$_POST = json_decode(file_get_contents("php://input"), true);
//printAll($_POST);
$grinf = groupInfo($_GET['id']);

$name_var = translateDir($_GET['id']);
    ?>

    <?php if( $grinf->program_id == 7 && getAccess(get_current_user_id())->access == 1 ){
        $data = $wpdb->get_results($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d", $_GET['id'], $_POST['fileuserdata'] ));
    } elseif( $grinf->program_id == 7 && $grinf->expert_id == get_current_user_id() ){
        $data = $wpdb->get_results($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d ", $_GET['id'], $_POST['fileuserdata'], $grinf->trener_id ));
    } elseif( $grinf->program_id == 7 && $grinf->teamleader_id == get_current_user_id() ){
        $data = $wpdb->get_results($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d", $_GET['id'], $_POST['fileuserdata'] ));
    }   ?>

    <div class="boxcoding"></div>


    <h3><?= nameUser($_POST['fileuserdata'], 5) ?></h3>

    <?php $r=0; foreach ($data as $res): $r++; ?>
    <?php

        if($r == 1 ){
            $activeclass = 'class="active"';
            $active = 'active';
        }else{
            $activeclass = '';
            $active = '';
        }


        if($grinf->trener_id == $res->create_user_id){
            $position = "Тренер";
        } elseif ($grinf->independent_trainer_id == $res->create_user_id){
            $position = "Независимый тренер";

        } elseif ($grinf->moderator_id == $res->create_user_id){
            $position = "Модератор";

        } elseif ($grinf->teamleader_id == $res->create_user_id){
            $position = "Тимлидер";

        } elseif ($grinf->expert_id == $res->create_user_id){
            $position = "Эксперт";

        }

        $allName = nameUser($res->create_user_id, 5);
        $grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");
        foreach ($grades as $grade){
            $arrGrade[$grade->id] = $grade->$name_var;
        }
        if($rubric_file = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_assessment_attached_file WHERE deleted = 0 AND rubric_user_id = %d AND user_id = %d AND group_id = %d AND category_id = %d",
            $res->listener_id, $res->create_user_id, $_GET['id'], 2)) ){
            $rubric_file_text = "<a href=\"/server_file/?assessment_sheet_file={$rubric_file->id}&assessment_sheet=1\" class=\"btn btn-info\">Скачать файл {$rubric_file->file_name} </a>";
        }else{
            $rubric_file_text = FILE_NOT_DOWNLOAD;
        }

        if($grinf->moderator_id == $res->create_user_id && $grinf->program_id == 7 && $grinf->teamleader_id == get_current_user_id()) $addp =  '<br><a href="/groups/?z=group&id='.$_GET['id'].'&tmModal=1&userid='.$_POST['fileuserdata'].'&moderatorid='.$grinf->moderator_id.'" class="btn btn-success">Внести коррективы в обоснование модератора</a>';
        else $addp =  '';

        ${'tablink_'.$r} = '<li role="presentation" '.$activeclass.'><a href="#tabdata_'.$r.'" aria-controls="tabdata_'.$r.'" role="tab" data-toggle="tab">'.$position.': '.$allName.'</a></li>';
        ${'tabdata_'.$r} = '<div role="tabpanel" class="tab-pane '.$active.'" id="tabdata_'.$r.'"><div class="card">
        <a href="/export_to_word/?form=assessment&create_user_id='.$res->create_user_id.'&listener_id='.$res->listener_id.'&group='.$_GET['id'].'" class="btn btn-primary">' . ASSESSMENT_SECOND[0] . '</a>
       '.$rubric_file_text.' '.$addp.'
	<div class="card-body">
		<div class="panel-group" id="accordion1">
			<div class="card panel">
			<table class="table table-bordered no-margin">
                <tr>
                    <td>' . ASSESSMENT_RUBRIC[1] . '</td>
                    <td>
                        ' . $arrGrade[$res->section_a_grade] . ' 
                    </td>
                </tr>
                <tr>
                    <td>' . ASSESSMENT_RUBRIC[3] . '</td>
                    <td>
                        ' . $arrGrade[$res->section_b_grade] . ' 
                    </td>
                </tr>
                <tr>
                    <td>' . ASSESSMENT_RUBRIC[5] . '</td>
                    <td>
                        ' . $arrGrade[$res->section_c_grade] . ' 
                    </td>
                </tr>
            </table>
			
			
			</div><!--end .panel -->
		</div>

	</div><!--end .card-body -->
</div><!--end .card -->

 
 <div class="card">
	<div class="card-body">
	<b>' . ASSESSMENT_SECOND[3] . ' </b>
	    <div class="form-group">
			<label for="textarea3">';
        ${'tabdata_'.$r} .= ( $grinf->moderator_id == $res->create_user_id || $grinf->teamleader_id == $res->create_user_id || $grinf->expert_id == $res->create_user_id) ? DECISION_MODERATION : ASSESSMENT_SECOND[5];
		${'tabdata_'.$r} .= '</label><br>
			'. $res->review .'
		</div>
		<div class="form-group">
			<label for="select1">';
		${'tabdata_'.$r} .= ( $grinf->moderator_id == $res->create_user_id || $grinf->teamleader_id == $res->create_user_id || $grinf->expert_id == $res->create_user_id) ? MODERATION_DECISION : ASSESSMENT_SECOND[4];
        ${'tabdata_'.$r} .= '</label><br>';
foreach ($grades as $grade):
if( $grade->id == $res->grading_solution ):
${'tabdata_'.$r} .=   $grade->$name_var;
endif;
endforeach;

${'tabdata_'.$r} .= ' 
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


