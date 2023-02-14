<?php

$section = $phpWord->addSection();

$grinf = groupInfo($_GET['group']);

if($name_var = translateDir($_GET['group']) == 'name'){
    $p_name = "p_name";
    $name = 'name';
    $lang_name = 'lang_name_ru';
    $name_org = 'name_org';
    $docLang = "rus";
}else{
    $p_name = "name_kaz";
    $name = "name_kaz";
    $lang_name = 'lang_name_kz';
    $name_org = "name_org_kaz";
    $docLang = "kaz";
}

if($_GET['position'] == '1'){
    $position = "тренера";
} elseif ($_GET['position'] == '2'){
    $position = "независимого тренера";
} elseif ($_GET['position'] == '3'){
    $position = "модератора";
}

$allName = nameUser($_GET['create_user_id'], 5);
$grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");

foreach ($grades as $grade){
    $arrGrade[$grade->id] = $grade->$name;
    $arrGradeNum[$grade->id] = $grade->proform;
    $arrGradeProform[$grade->proform] = $grade->$name;
}

// instantiate and use the dompdf class


    $results = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.surname, u.name, u.patronymic, u.email, p.create_user_id, p.section_a_grade, 
       p.section_b_grade, p.section_c_grade, p.section_d_grade, p.section_e_grade, p.grading_solution
                                        FROM p_groups_users g
                                        LEFT OUTER JOIN p_assessment_rubric p ON p.listener_id = g.id_user
                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
                                        WHERE g.id_group = %d AND p.group_id = %d AND p.create_user_id = %d  ORDER BY u.surname, u.name, u.patronymic", $_GET['group'], $_GET['group'], $_GET['create_user_id'] ));

    if($grinf->independent_trainer_id == $_GET['create_user_id'] && ($grinf->program_id == 6 || $grinf->program_id == 16)){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $positionInGroupText = FIO_ALL_TRENER[1];
        $tamplateName = 'templates/'.$grinf->program_subsection.'/6template_assessment_'.$docLang.'_independent_trainer.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif($grinf->trener_id == $_GET['create_user_id'] && ($grinf->program_id == 6 || $grinf->program_id == 16)){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $positionInGroupText = FIO_ALL_TRENER[1];
        $tamplateName = 'templates/'.$grinf->program_subsection.'/6template_assessment_'.$docLang.'_trener.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif( $grinf->program_id == 7){
$list_text = FOR_ALL_TRENER;
$position_text = TRENER_GROUP_TEXT[1];
$positionInGroupText = FIO_ALL_TRENER[1];
$tamplateName = 'templates/template_assessment_'. $docLang . '_trener_indepedence_trener.docx';
$groupnum = $grinf->number_group;
$nameOrg = $grinf->$name_org;
}elseif($grinf->moderator_id == $_GET['create_user_id'] && $grinf->program_id == 17){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $positionInGroupText = FIO_ALL_TRENER[1];
        $tamplateName = 'templates/'.$grinf->program_id.'/template_proform_'.$docLang.'_moderator.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif($grinf->teamleader_id == $_GET['create_user_id'] && $grinf->program_id == 17){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $positionInGroupText = FIO_ALL_TRENER[1];
        $tamplateName = 'templates/'.$grinf->program_id.'/template_proform_'.$docLang.'_moderator.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif($grinf->trener_id == $_GET['create_user_id'] && $grinf->program_id == 17){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $positionInGroupText = FIO_ALL_TRENER[1];
        $tamplateName = 'templates/'.$grinf->program_id.'/template_proform_'.$docLang.'_trener.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif($grinf->expert_id == $_GET['create_user_id'] && $grinf->program_id == 17){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $positionInGroupText = FIO_ALL_TRENER[1];
        $tamplateName = 'templates/'.$grinf->program_id.'/template_proform_'.$docLang.'_expert.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif(($grinf->expert_id == $_GET['create_user_id'] || $grinf->teamleader_id == $_GET['create_user_id']) && ($grinf->program_id == 6 || $grinf->program_id == 16)){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $positionInGroupText = FIO_ALL_TRENER[1];
        $tamplateName = 'templates/'.$grinf->program_subsection.'/6template_assessment_'.$docLang.'_expert_tm.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif($grinf->independent_trainer_id == $_GET['create_user_id']){
    $list_text = FOR_ALL_TRENER;
    $position_text = TRENER_GROUP_TEXT[1];
    $positionInGroupText = FIO_ALL_TRENER[1];
    $tamplateName = 'templates/14template_assessment_'.$docLang.'_moder.docx';
    $groupnum = "";
    $nameOrg = "";

    }elseif($grinf->moderator_id == $_GET['create_user_id']){
        $list_text = FOR_MODERATION;
        $position_text = PROFORMA[11];
        $positionInGroupText = PROFORMA[5];
        $tamplateName = 'templates/14template_assessment_'.$docLang.'_moder.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif($grinf->expert_id == $_GET['create_user_id']){
        $list_text = FOR_MODERATION;
        $position_text = PROFORMA[11];
        $positionInGroupText = PROFORMA[5];
        $tamplateName = 'templates/14template_assessment_'.$docLang.'_expert.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }elseif($grinf->trener_id == $_GET['create_user_id']){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[0];
        $positionInGroupText = FIO_ALL_TRENER[0];
        $tamplateName = 'templates/14template_assessment_'.$docLang.'_trener.docx';
        $groupnum = $grinf->number_group;
        $nameOrg = $grinf->$name_org;

    }
//echo $tamplateName; exit();

$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/portalcpi/template-word/'.$tamplateName);
$count = count($results);
$templateProcessor->setValue('date', htmlspecialchars(date('Y-m-d') ));
$templateProcessor->setValue('name_org', htmlspecialchars($nameOrg));
$templateProcessor->setValue('group', htmlspecialchars($groupnum));
$templateProcessor->setValue('lang_name', htmlspecialchars($grinf->$lang_name));
$templateProcessor->setValue('allName', htmlspecialchars($allName));
$templateProcessor->setValue('positionInGroupText', htmlspecialchars($positionInGroupText));
//echo $count; printAll($results); exit();
$t=0;
$templateProcessor->cloneRow('rowNumber', $count);
foreach($results as $res){

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

//if ($grinf->program_id == 17) {
//    $grade_a = $arrGradeNum[$res->section_a_grade];
//    $grade_b = $arrGradeNum[$res->section_b_grade];
//    $grade_c = $arrGradeNum[$res->section_c_grade];
//    $grade_d = $arrGradeNum[$res->section_d_grade];
//    $grade_e = $arrGradeNum[$res->section_e_grade];
//} else {
//    $grade_a = $arrGrade[$res->section_a_grade];
//    $grade_b = $arrGrade[$res->section_b_grade];
//    $grade_c = $arrGrade[$res->section_c_grade];
//    $grade_d = $arrGrade[$res->section_d_grade];
//    $grade_e = $arrGrade[$res->section_e_grade];
//}

    $grade_a = $arrGrade[$res->section_a_grade];
    $grade_b = $arrGrade[$res->section_b_grade];
    $grade_c = $arrGrade[$res->section_c_grade];
    $grade_d = $arrGrade[$res->section_d_grade];
    $grade_e = $arrGrade[$res->section_e_grade];

    $t++;

    $templateProcessor->setValue('rowNumber#'.$t, htmlspecialchars($t));
    $templateProcessor->setValue('userName#'.$t, htmlspecialchars($res->surname. ' ' .$res->name. ' ' .$res->patronymic));
    $templateProcessor->setValue('section_a_grade#'.$t, htmlspecialchars($grade_a));
    $templateProcessor->setValue('section_b_grade#'.$t, htmlspecialchars($grade_b));
    $templateProcessor->setValue('section_c_grade#'.$t, htmlspecialchars($grade_c));
    $templateProcessor->setValue('section_d_grade#'.$t, htmlspecialchars($grade_d));
    $templateProcessor->setValue('section_e_grade#'.$t, htmlspecialchars($grade_e));
    $templateProcessor->setValue('grading_solution#'.$t, htmlspecialchars($arrGrade[$res->grading_solution] ));
    $templateProcessor->setValue('average_rating#'.$t, htmlspecialchars($arrGradeProform[$average_rating]));
}

$templateProcessor->setValue('position_text', htmlspecialchars($position_text));

$file = 'LO.docx';

header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
ob_clean();
//$templateProcessor = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$templateProcessor->saveAs("php://output");
exit();
