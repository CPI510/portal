<?php

///////////////////////
///  НЕ НУЖНЫЙ СКРИПТ ПОКА ЧТО
/// ФОРМИРУЕТ ДЛЯ АДМИНА РУБРИКУ КОТОРЫЕ НАПИСАЛИ ТРЕНЕРЫ , НЕЗАВИСИМЫЕ ТРЕНРЫ, ЭКСПЕРТЫ МОДЕРАТОРЫ
/// И ВЫДАЕТ НА СКАЧИВАНИЕ В АРХИВЕ
/// //////////////////
///
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

$e = 0;

$from_zip_file = '/var/www/uploads/rubricall/' .get_current_user_id();

if(!is_dir($from_zip_file))
    mkdir( $from_zip_file, 0777 );

$groupListeners = $wpdb->get_results($wpdb->prepare('
SELECT a.date_create, a.create_user_id, a.listener_id, a.section_a_grade, a.section_a_description, a.section_b_grade, a.section_b_description, a.section_c_grade, a.section_c_description, a.review, a.grading_solution,  
b.surname, b.name, b.patronymic
FROM p_assessment_rubric a
INNER JOIN p_user_fields b ON b.user_id = a.listener_id
WHERE a.group_id = %d AND a.create_user_id = %d
', $_GET['group'], $_GET['create_user_id'] ));


foreach ($groupListeners as $user){

    $e++;

    if(!$res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_assessment_rubric WHERE create_user_id = %d AND listener_id = %d AND group_id = %d",$_GET['create_user_id'], $user->listener_id, $_GET['group']))){
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
    }
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('/var/www/portal/wp-content/themes/portalcpi/template-word/'.$tamplateName);
//echo $html;
//exit();
//\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

    if( $groupInfo->independent_trainer_id == get_current_user_id() || $groupInfo->moderator_id == get_current_user_id()){
        $nameListener = $wpdb->get_var($wpdb->prepare("SELECT code FROM p_assessment_coding_user WHERE group_id = %d AND listener_id = %d", $_GET['group'], $user->listener_id));
    }else{
        echo "<div align='center'>Рубрика <br>Слушатель: " . nameUser($_POST['fileuserdata'], 5) . "</div>";
        $nameListener = nameUser($res->listener_id,5);
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

    $file = $e.'_'.$user->surname.'_'.$user->name.''.$user->patronymic.'_'.$_GET['group'].'_'.$_GET['create_user_id'].'_rubric.docx';

//header("Content-Description: File Transfer");
//header('Content-Disposition: attachment; filename="' . $file . '"');
//header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//header('Content-Transfer-Encoding: binary');
//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//header('Expires: 0');
//ob_clean();
//$templateProcessor = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
//$templateProcessor->saveAs("php://output");
    $pathToSave = $from_zip_file .'/'. $file;
    $templateProcessor->saveAs($pathToSave);

}

$userfull = $wpdb->get_row($wpdb->prepare("SELECT surname, name, patronymic, user_id_attached, email FROM p_user_fields WHERE user_id = %d", $_GET['create_user_id']));

$zip = new ZipArchive();
$filename = "{$userfull->surname}_{$userfull->name}_{$userfull->patronymic}" . time() . '.zip';
$filenamezip = $from_zip_file . $filename ;
if ($zip->open($filenamezip, ZipArchive::CREATE)!==TRUE) {
    exit("Невозможно открыть <$filenamezip>\n");
}

$q=0;
foreach ($groupListeners as $user){
    $q++;
    $file = $e.'_'.$user->surname.'_'.$user->name.''.$user->patronymic.'_'.$_GET['group'].'_'.$_GET['create_user_id'].'_rubric.docx';
    $zip->addFile( $from_zip_file . '/' . $file  );
}

$zip->close();

if(file_exists($filenamezip)){
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='. $filename); //. basename($res->filename));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filenamezip));
    readfile($filenamezip);

    unlink($filenamezip);


}else{
    echo "!";
}

delDir($from_zip_file);

function delDir($dir) {
    $files = array_diff(scandir($dir), ['.','..']);
    foreach ($files as $file) {
        (is_dir($dir.'/'.$file)) ? delDir($dir.'/'.$file) : unlink($dir.'/'.$file);
    }
    return rmdir($dir);
}

exit();