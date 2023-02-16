<?php
get_header();
global $wpdb;
?>
<br>
    <div class="col-lg-12">
        <div class="row">
            <div class="card">
                <div class="card-body">
        <?php
        $grinf = groupInfo($_GET['group']);
 echo '<script>console.log('. $grinf->program_id .') </script>';
        if($name_var = translateDir($_GET['group']) == 'name'){
            $p_name = "p_name";
            $name = 'name';
            $lang_name = 'lang_name_ru';
            $name_org = 'name_org';
        }else{
            $p_name = "name_kaz";
            $name = "name_kaz";
            $lang_name = 'lang_name_kz';
            $name_org = "name_org_kaz";
        }
//        echo groupInfo($_GET['group'])->lang_id ." 333";
        //printAll($grinf);
        if( ($grinf->program_id == 7 || $grinf->program_id == 14) && ($grinf->trener_id == get_current_user_id() || $grinf->independent_trainer_id == get_current_user_id() || $grinf->moderator_id == get_current_user_id() )){
            $data = $wpdb->get_results($s=$wpdb->prepare("SELECT a.create_user_id, b.surname, b.name, b.patronymic
                FROM  p_assessment_rubric  a
                LEFT OUTER JOIN p_user_fields b ON b.user_id = a.create_user_id
                WHERE a.group_id = %d AND a.create_user_id = %d
                GROUP BY a.create_user_id", $_GET['group'], get_current_user_id() ));
        }elseif(($grinf->program_id == 7 || $grinf->program_id == 14) && $grinf->expert_id == get_current_user_id() ){
            $data = $wpdb->get_results($s=$wpdb->prepare("SELECT a.create_user_id, b.surname, b.name, b.patronymic
                FROM  p_assessment_rubric  a
                LEFT OUTER JOIN p_user_fields b ON b.user_id = a.create_user_id
                WHERE a.group_id = %d AND a.create_user_id = %d
                GROUP BY a.create_user_id", $_GET['group'], $grinf->trener_id ));
        }else{
            $data = $wpdb->get_results($s=$wpdb->prepare("SELECT a.create_user_id, b.surname, b.name, b.patronymic
                FROM  p_assessment_rubric  a
                LEFT OUTER JOIN p_user_fields b ON b.user_id = a.create_user_id
                WHERE a.group_id = %d
                GROUP BY a.create_user_id", $_GET['group'] ));
        }
 ?>
<?php if($data):?>
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
            $positiontext = FULLNAME_TRENER;
            $positionnum = 1;
        } elseif ($grinf->independent_trainer_id == $res->create_user_id){
            $position = "Независимый тренер";
            $positiontext = INDEPENCE_TRENER_TEXT;
            $positionnum = 2;
        } elseif ($grinf->moderator_id == $res->create_user_id){
            $position = "Модератор";
            $positiontext = "модератора";
            $positionnum = 3;
        } elseif ($grinf->teamleader_id == $res->create_user_id){
            $position = "Тимлидер";
            $positiontext = "тимлидера";
            $positionnum = 3;
        } elseif ($grinf->expert_id == $res->create_user_id){
            $position = "Эксперт";
            $positiontext = "эксперта";
            $positionnum = 3;
        }

        if($grinf->program_id == 7 && $grinf->admin_id == get_current_user_id() ){
            $files = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_assessment_attached_file WHERE group_id = %d AND deleted = 0", $_GET['group'] ));
            $fileupload = "<hr>".DOWNLOAD_FILES.":<br>";
            foreach($files as $file){
                $fileupload .=  nameUser($file->user_id,5) .' - <a href="/server_file/?assessment_sheet_file='.$file->id.'&assessment_sheet=1" class="btn btn-info btn-xs">'.$file->file_name.'</a> <br>';
            }

        }elseif($grinf->program_id == 7 && $grinf->expert_id == get_current_user_id() ){
            $files = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_assessment_attached_file WHERE user_id = %d AND group_id = %d AND deleted = 0",$grinf->trener_id, $_GET['group'] ));
            $fileupload = "<hr>".DOWNLOAD_FILES.":<br>";
            foreach($files as $file){
                $fileupload .=  nameUser($file->user_id,5) .' - <a href="/server_file/?assessment_sheet_file='.$file->id.'&assessment_sheet=1" class="btn btn-info btn-xs">'.$file->file_name.'</a> <br>';
            }

        }else{
            if(!$idfile = $wpdb->get_row($wpdb->prepare("SELECT id,file_name FROM p_assessment_attached_file WHERE deleted = 0 AND group_id = %d AND user_id = %d", $_GET['group'], get_current_user_id()))){
                $fileupload = '<hr><a href="#" id="fileu" data-id=" '. get_current_user_id() .' " data-link="/assessment/?z=addfile&assessmentaddfile='.$_GET['group'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">'.FILE_ATTACH.'</a>';
            }else{
                $fileupload = '<hr><a href="/server_file/?assessment_sheet_file='.$idfile->id.'&assessment_sheet=1" class="btn btn-info">'.DOWNLOAD_FILE.' '.$idfile->file_name.'</a> 
                <a href="/assessment/?z=addfile&action=delete&id='.$idfile->id.'&group='.$_GET['group'].'" class="btn btn-warning">'.DELETE_FILE.' '.$idfile->file_name.'</a> ';
            }
        }




        $allName = nameUser($res->create_user_id, 5);
        $grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");

        foreach ($grades as $grade){
            $arrGrade[$grade->id] = $grade->$name;
        }

        if($grinf->program_id == 7 && ( $grinf->independent_trainer_id == get_current_user_id() || $grinf->moderator_id == get_current_user_id() )){
            $groupInfo = "";
            $results = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, c.code surname, u.email, p.create_user_id, p.section_a_grade, p.section_b_grade, p.section_c_grade, p.grading_solution 
                FROM p_groups_users g 
                LEFT OUTER JOIN p_assessment_rubric p ON p.listener_id = g.id_user 
                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                LEFT OUTER JOIN p_assessment_coding_user c ON c.listener_id = g.id_user AND c.group_id = %d
                WHERE g.id_group = %d  AND p.group_id = %d AND p.create_user_id = %d", $_GET['group'], $_GET['group'], $_GET['group'], $res->create_user_id ));
        }else{
            $groupInfo = '<b>'.PROFORMA[12].':</b> '.date('Y-m-d').' <br>
                <b>'.PLACE_STUDY.':</b> '.$grinf->$name_org.'<br>
                <b>'.GROUP.':</b> '.$grinf->number_group.'<br>
                <b>'.LANG_EDUCATION.':</b> '.$grinf->$lang_name.'<br>
                <b>'.$positiontext.':</b> '.$allName.'<br>';
            $results = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.surname, u.name, u.patronymic, u.email, p.create_user_id, p.section_a_grade, p.section_b_grade, p.section_c_grade, p.grading_solution, c.code
                                        FROM p_groups_users g
                                        LEFT OUTER JOIN p_assessment_rubric p ON p.listener_id = g.id_user
                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
                                        LEFT OUTER JOIN p_assessment_coding_user c ON c.listener_id = g.id_user
                                        WHERE g.id_group = %d AND p.group_id = %d AND p.create_user_id = %d", $_GET['group'], $_GET['group'], $res->create_user_id ));
        }


        ${'tablink_'.$r} = '<li role="presentation" '.$activeclass.'><a href="#tabdata_'.$r.'" aria-controls="tabdata_'.$r.'" role="tab" data-toggle="tab">'.$position.': '.$allName.'</a></li>';
        ${'tabdata_'.$r} = '<div role="tabpanel" class="tab-pane '.$active.'" id="tabdata_'.$r.'"><p>
                                    '.$grinf->$p_name.' 
                                </p>
                                <p>
                                    '.$groupInfo.' 
                                </p>
                                <table class="table table-bordered no-margin">
                                    <tr>
                                        <th rowspan="2">№</th>
                                        <th rowspan="2">'.FULL_NAME_LEADER.'</th>
                                        <th colspan="3"><div align="center">'.NAME_REPORTS_PORTFOLIO.'</div></th>
                                        <th rowspan="2">'.GRADING_DESCISION.'</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>'.ASSESSMENT_RUBRIC[1].'</b>
                                            '.REPORT_A_DESCRIPTION.'
                                        </td>
                                        <td>
                                            <b>'.ASSESSMENT_RUBRIC[3].'</b> '.REPORT_B_DESCRIPTION.'
                                        </td>
                                        <td>
                                            <b>'.ASSESSMENT_RUBRIC[5].'</b>
                                               '.REPORT_C_DESCRIPTION.'
                                        </td>
                                    </tr>'; ?>

<?php

if($grinf->program_id == 7) {
 $formNum = 'trenerassessment14';
        } else {
            $formNum = 'assessment2';
        } 
?>
        <?php $t=0; foreach($results as $res){
            if(getAccess(get_current_user_id())->access == 1 && $grinf->independent_trainer_id == $res->id_user){
                $code = "(".$res->code.")";
            }else{
                $code = "";
            }
            $t++;
            ${'tabdata_'.$r} .= '
                <tr>
                    <td>' .$t. '</td>
                    <td>' .$res->surname. ' ' .$res->name. ' ' .$res->patronymic. ' ' . $code . '</td>
                    <td>' .$arrGrade[$res->section_a_grade]. '</td>
                    <td>' .$arrGrade[$res->section_b_grade]. '</td>
                    <td>' .$arrGrade[$res->section_c_grade]. '</td>
                    <td>' .$arrGrade[$res->grading_solution]. '</td>
                </tr>';
        }
        ${'tabdata_'.$r} .= '</table><br><!--<a href="/export_to_pdf/?group='.$_GET['group'].'&create_user_id='.$res->create_user_id.'&position='.$positionnum.' " class="btn btn-success">'.GENERATE_PDF.'</a>-->
<a href="/export_to_word/?form=assessment2&group='.$_GET['group'].'&create_user_id='.$res->create_user_id.'&position='.$positionnum.' " class="btn btn-success">'.GENERATE_WORD.'</a>
</div>';?>

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
<?=$fileupload?>
<?php else:?>
<?php alertStatus('warning','Нет данных!'); ?>
<?php endif; ?>




            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="Modal" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form enctype="multipart/form-data" class="form-horizontal" method="POST" action="/assessment/?z=addfile">
                                <div class="card">
                                    <div class="card-head style-primary">
                                        <header><?= SCAN_ATTACH ?></header>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="Username5" class="col-sm-2 control-label">Файл</label>
                                            <div class="col-sm-10">
                                                <br><br>
                                                <div id="ajax-respond" class="ajax-respond"></div>
                                                <input type="file" id="uploadInput" name="file" class="form-control" accept=".docx,.doc,.pptx,.ppt,.pdf,.rar">
                                            </div>
                                        </div>
                                    </div><!--end .card-body -->
                                    <div class="card-actionbar">
                                        <div class="card-actionbar-row">
                                            <button disabled="disabled" class="btn btn-flat btn-primary ink-reaction" id="load"><?= FILE_UPLOAD ?></button>
                                        </div>
                                    </div>
                                </div><!--end .card -->
                                <input type="hidden" name="userid" value="<?= get_current_user_id() ?>">
                                <input type="hidden" name="group" value="<?=$_GET['group']?>">
                                <input type="hidden" name="action" value="add">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close" class="btn btn-default" data-dismiss="modal"><?= ASSESSMENT_SECOND[1] ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <script src="<?= bloginfo('template_url') ?>/assets/js/core/actionsFile.js"></script>

<?php
get_Footer();
?>
