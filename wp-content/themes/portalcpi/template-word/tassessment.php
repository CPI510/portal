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

if(!$res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_assessment_rubric WHERE create_user_id = %d AND listener_id = %d AND group_id = %d",$_GET['create_user_id'], $_GET['listener_id'], $_GET['group']))){
    echo "Нет данных!"; exit();
}
$grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");
foreach ($grades as $grade){
    $arrGrade[$grade->id] = $grade->$name;
}
if($res->create_user_id == $groupInfo->trener_id){
    $position = "тренера";
    $position2 = "Тренер";
    $tamplateName = 'template_rubric_'.$lang_t.'_trener_indepedence_trener.docx';
} elseif ($res->create_user_id == $groupInfo->independent_trainer_id){
    $position = "независимого тренера";
    $position2 = "Независимый тренер";
    $tamplateName = 'template_rubric_'.$lang_t.'_trener_indepedence_trener.docx';
} elseif ($res->create_user_id == $groupInfo->moderator_id){
    $position = "модератора";
    $position2 = "Модератор";
    $tamplateName = 'template_rubric_'.$lang_t.'_moderator.docx';
} elseif ($res->create_user_id == $groupInfo->expert_id){
    $tamplateName = 'template_rubric_'.$lang_t.'_moderator.docx';
}else{
    $tamplateName = 'template_rubric_'.$lang_t.'_moderator.docx';
}
$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($_SERVER['DOCUMENT_ROOT'] .'/wp-content/themes/portalcpi/template-word/'.$tamplateName);
//echo $html;
//exit();
//\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

if( $groupInfo->independent_trainer_id == get_current_user_id() || $groupInfo->moderator_id == get_current_user_id()){
    $nameListener = $wpdb->get_var($wpdb->prepare("SELECT code FROM p_assessment_coding_user WHERE group_id = %d AND listener_id = %d", $_GET['group'], $_GET['listener_id']));
}elseif($groupInfo->trener_id == get_current_user_id() || $groupInfo->expert_id == get_current_user_id()){
    echo "<div align='center'>Рубрика <br>Слушатель: " . nameUser($_POST['fileuserdata'], 5) . "</div>";
    $nameListener = nameUser($res->listener_id,5);
}else{
    $nameListener = $wpdb->get_var($wpdb->prepare("SELECT code FROM p_assessment_coding_user WHERE group_id = %d AND listener_id = %d", $_GET['group'], $_GET['listener_id']));
}
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

// вывод непосредственно в браузер
//header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//header('Content-Disposition: attachment;filename="dogovor.docx"');
//header('Cache-Control: max-age=0');
//$templateProcessor->saveAs('php://output');

$file = 'Рубрика.docx';

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