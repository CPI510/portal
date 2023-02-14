<?php

$section = $phpWord->addSection();

$groupInfo = groupInfo($_GET['group']);
if($name_var = translateDir($_GET['group']) == 'name'){
    $p_name = "p_name";
    $name = 'name';
    $lang_t = "rus";
    $name_org = 'name_org';
}else{
    $p_name = "name_kaz";
    $name = "name_kaz";
    $lang_t = "kaz";
    $name_org = "name_org_kaz";
}

$grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");
foreach ($grades as $grade){
    $arrGrade[$grade->id] = $grade->$name;
}

if($_GET['tm'] == 'all'){
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_assessment_rubric WHERE group_id = %d AND create_user_id = %d AND 	grading_solution = 4", $_GET['group'], $groupInfo->teamleader_id));
    $tamplateName = 'templates/14template_rubric_'.$lang_t.'_teamleader_all.docx';
    $rubric_text = 'Обоснование_';
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/portalcpi/template-word/'.$tamplateName);

    $count = count($results);
//    $templateProcessor->cloneRow('listener_name', $count);
    $templateProcessor->cloneBlock('CLONEBLOCK', $count, true, true);
    $t=0;
    foreach ($results as $res){
        $nameListener = nameUser($res->listener_id,5);
        $t++;
//        $templateProcessor->setValue('rowNumber#'.$t, htmlspecialchars($t));
        $templateProcessor->setValue('section_a#'.$t, $res->section_a_description);
        $templateProcessor->setValue('section_b#'.$t, $res->section_b_description);
        $templateProcessor->setValue('section_c#'.$t, $res->section_c_description);
        $templateProcessor->setValue('section_a_grade#'.$t, $arrGrade[$res->section_a_grade]);
        $templateProcessor->setValue('section_b_grade#'.$t, $arrGrade[$res->section_b_grade]);
        $templateProcessor->setValue('section_c_grade#'.$t, $arrGrade[$res->section_c_grade]);
        $templateProcessor->setValue('review#'.$t, $res->review);
        $templateProcessor->setValue('grading_solution#'.$t, $arrGrade[$res->grading_solution]);

        $templateProcessor->setValue('listener_name#'.$t, $nameListener);
        $templateProcessor->setValue('create_user_name#'.$t, nameUser($res->create_user_id,5));
        $templateProcessor->setValue('date#'.$t, date('Y-m-d'));
        $templateProcessor->setValue('training_center#'.$t, $groupInfo->$name_org );
        $templateProcessor->setValue('position#'.$t, $position );
        $templateProcessor->setValue('position2#'.$t, $position2 );

        $section = $phpWord->addSection(
            array('marginLeft' => 600, 'marginRight' => 600,
                'marginTop' => 600, 'marginBottom' => 600)
        );

        $section= $phpWord->addSection(array('orientation' => 'landscape'));
    }

    $file = $rubric_text . '.docx';

}else{
    if(!$res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_assessment_rubric WHERE create_user_id = %d AND listener_id = %d AND group_id = %d",$_GET['create_user_id'], $_GET['listener_id'], $_GET['group']))){
        echo "Нет данных!"; exit();
    }

    $rubric_text = 'Рубрика_';
    if($res->create_user_id == $groupInfo->trener_id){
        $position = "тренера";
        $position2 = "Тренер";
        $tamplateName = 'templates/14template_rubric_'.$lang_t.'_trener.docx';
    } elseif ($res->create_user_id == $groupInfo->independent_trainer_id){
        $position = "независимого тренера";
        $position2 = "Независимый тренер";
        $tamplateName = 'templates/14template_rubric_'.$lang_t.'_moderator.docx';
    } elseif ($res->create_user_id == $groupInfo->moderator_id){
        $position = "модератора";
        $position2 = "Модератор";
        $tamplateName = 'templates/14template_rubric_'.$lang_t.'_moderator.docx';
    } elseif ($res->create_user_id == $groupInfo->expert_id){
        $tamplateName = 'templates/14template_rubric_'.$lang_t.'_expert.docx';
    } elseif ($res->create_user_id == $groupInfo->teamleader_id && $_GET['ver'] == '2'){
        $tamplateName = 'templates/14template_rubric_'.$lang_t.'_moderator.docx';
        //$rubric_text = 'Обоснование_';
    } elseif ($res->create_user_id == $groupInfo->teamleader_id){
        $tamplateName = 'templates/14template_rubric_'.$lang_t.'_teamleader.docx';
        $rubric_text = 'Обоснование_';
    }

    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('/var/www/html/wp-content/themes/portalcpi/template-word/'.$tamplateName);
//echo $html;
//exit();
//\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

    $nameListener = nameUser($res->listener_id,5);
// Template processor instance creation
//echo date('H:i:s'), ' Creating new TemplateProcessor instance...', EOL;


//$_doc = new \PhpOffice\PhpWord\TemplateProcessor('Template.docx');
    $templateProcessor->setValue('section_a', $res->section_a_description);
    $templateProcessor->setValue('section_b', $res->section_b_description);
    $templateProcessor->setValue('section_c', $res->section_c_description);
    $templateProcessor->setValue('section_a_grade', $arrGrade[$res->section_a_grade]);
    $templateProcessor->setValue('section_b_grade', $arrGrade[$res->section_b_grade]);
    $templateProcessor->setValue('section_c_grade', $arrGrade[$res->section_c_grade]);
    $templateProcessor->setValue('review', $res->review);
    $templateProcessor->setValue('grading_solution', $arrGrade[$res->grading_solution]);

    $templateProcessor->setValue('listener_name', $nameListener);
    $templateProcessor->setValue('create_user_name', nameUser($res->create_user_id,5));
    $templateProcessor->setValue('date', date('Y-m-d'));
    $templateProcessor->setValue('training_center', $groupInfo->$name_org );
    $templateProcessor->setValue('position', $position );
    $templateProcessor->setValue('position2', $position2 );
//    echo "<pre>";
//    print_r($res);
//    echo "</pre>";
//
//    echo nameUser($res->listener_id,5); exit();
// вывод непосредственно в браузер
//header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//header('Content-Disposition: attachment;filename="dogovor.docx"');
//header('Cache-Control: max-age=0');
//$templateProcessor->saveAs('php://output');

    $file = $rubric_text . userInfo($res->listener_id)->surname . '_' . userInfo($res->listener_id)->name . '_' . userInfo($res->listener_id)->patronymic . '.docx';

}






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