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
                    //echo groupInfo($_GET['group'])->lang_id ." 333";
                    //printAll($grinf);
                    if(  $grinf->trener_id == get_current_user_id() || $grinf->independent_trainer_id == get_current_user_id() || $grinf->moderator_id == get_current_user_id() || $grinf->expert_id == get_current_user_id() ){
                        $data = $wpdb->get_results($s=$wpdb->prepare("SELECT a.create_user_id, b.surname, b.name, b.patronymic
                FROM  p_assessment_rubric  a
                LEFT OUTER JOIN p_user_fields b ON b.user_id = a.create_user_id
                WHERE a.group_id = %d AND a.create_user_id = %d
                GROUP BY a.create_user_id", $_GET['group'], get_current_user_id() ));
                    }else{
                        $data = $wpdb->get_results($s=$wpdb->prepare("SELECT a.create_user_id, b.surname, b.name, b.patronymic
                FROM  p_assessment_rubric  a
                LEFT OUTER JOIN p_user_fields b ON b.user_id = a.create_user_id
                WHERE a.group_id = %d 
                GROUP BY a.create_user_id", $_GET['group'], $grinf->teamleader_id, $grinf->moderator_id, $grinf->expert_id ));
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
                            } elseif ($grinf->expert_id == $res->create_user_id){
                                $position = "Эксперт";
                                $positiontext = "эксперта";
                                $positionnum = 3;
                            } elseif ($grinf->teamleader_id == $res->create_user_id){
                                $position = "Тимлидер";
                                $positiontext = "тимлидера";
                                $positionnum = 3;
                            }

                            $allName = nameUser($res->create_user_id, 5);
                            $grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");

                            foreach ($grades as $grade){
                                $arrGrade[$grade->id] = $grade->$name;
                                $arrGradeNum[$grade->id] = $grade->proform;
                                $arrGradeProform[$grade->proform] = $grade->$name;
                            }

                            if( $grinf->independent_trainer_id == get_current_user_id() || $grinf->moderator_id == get_current_user_id() ){
                                $groupInfo = "";
                                $results = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.surname, u.name, u.patronymic, u.email, p.create_user_id, 
       p.section_a_grade, p.section_b_grade, p.section_c_grade, p.section_d_grade, p.section_e_grade, p.grading_solution 
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
                                $results = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.surname, u.name, u.patronymic, u.email, p.create_user_id, p.section_a_grade, p.section_b_grade, 
       p.section_c_grade, p.section_d_grade, p.section_e_grade, p.grading_solution
                                        FROM p_groups_users g
                                        LEFT OUTER JOIN p_assessment_rubric p ON p.listener_id = g.id_user
                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
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
                                        <th rowspan="2">'.ASSESSMENT_SHEET[1].'</th>
                                        <th colspan="5"><div align="center">Критерии оценивания</div></th>
                                        <th rowspan="2">Средняя оценка</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            ' . ASSESSMENT_TRENER_17[0] . '
                                        </td>
                                        <td>
                                            '. ASSESSMENT_TRENER_17[1] . ' 
                                        </td>
                                        <td>
                                            ' . ASSESSMENT_TRENER_17[2] . '  
                                        </td>
                                        <td>
                                            ' . ASSESSMENT_TRENER_17[3] . '  
                                        </td>
                                        <td>
                                            ' . ASSESSMENT_TRENER_17[4] . '  
                                        </td>
                                    </tr>'; ?>

                            <?php $t=0; foreach($results as $res){
                                if(
                                    $arrGradeNum[$res->section_a_grade] == 0
                                    || $arrGradeNum[$res->section_b_grade] == 0
                                    || $arrGradeNum[$res->section_c_grade] == 0
                                    || $arrGradeNum[$res->section_d_grade] == 0
                                    || $arrGradeNum[$res->section_e_grade] == 0
                                ){
                                    $average_rating = 0;
                                }else{
                                    $average_rating = round(($arrGradeNum[$res->section_a_grade]
                                            + $arrGradeNum[$res->section_b_grade]
                                            + $arrGradeNum[$res->section_c_grade]
                                            + $arrGradeNum[$res->section_d_grade]
                                            + $arrGradeNum[$res->section_e_grade]
                                        ) / 5);
                                }
                                $t++;
                                ${'tabdata_'.$r} .= '
                <tr>
                    <td>' .$t. '</td>
                    <td>' .$res->surname. ' ' .$res->name. ' ' .$res->patronymic. '</td>
                    <td>' .$arrGrade[$res->section_a_grade]. '</td>
                    <td>' .$arrGrade[$res->section_b_grade]. '</td>
                    <td>' .$arrGrade[$res->section_c_grade]. '</td>
                    <td>' .$arrGrade[$res->section_d_grade]. '</td>
                    <td>' .$arrGrade[$res->section_e_grade]. '</td>
                    <td>' . $arrGradeProform[$average_rating]. '</td>
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



    <script src="<?= bloginfo('template_url') ?>/assets/js/core/actionsFile.js"></script>

<?php
get_Footer();
?>