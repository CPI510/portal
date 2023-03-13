<?php

$grinf = groupInfo($_GET['group']);

if($name_var = translateDir($_GET['group']) == 'name'){
    $p_name = "p_name";
    $name = 'name';
    $lang_name = 'lang_name_ru';
    $name_org = 'name_org';
    $docLang = "rus";
    $subject = SUBJECTS[$grinf->subject_id];
}else{
    $p_name = "name_kaz";
    $name = "name_kaz";
    $lang_name = 'lang_name_kz';
    $name_org = "name_org_kaz";
    $docLang = "kaz";
    $subject = SUBJECTS[$grinf->subject_id];
}

if ($grinf->trener_id == get_current_user_id() && $grinf->program_id = 18){
    $filed_name = "trener_id";
    $fiels_text = "AND p.trener_id = %d";
    $fiels_id = $grinf->trener_id;
    $templateName = 'templates/18template_proforma_'.$docLang.'_trener-expert.docx';
    $position_text = 'Тренер';
} elseif($grinf->expert_id == get_current_user_id() && $grinf->program_id = 18){
    $filed_name = "expert_id";
    $fiels_text = "AND p.expert_id = %d";
    $fiels_id = $grinf->expert_id;
    $templateName = 'templates/18template_proforma_'.$docLang.'_trener-expert.docx';
    $position_text = 'Эксперт';
} elseif($grinf->moderator_id == get_current_user_id() && $grinf->program_id = 18){
    $filed_name = "moderator_id";
    $fiels_text = "AND p.moderator_id = %d";
    $fiels_id = $grinf->moderator_id;
    $templateName = '!!!!!!!!!!!!!!';
    $position_text = 'Модератор';
}

$results = $wpdb->get_results($wpdb->prepare("SELECT d.id, d.user_id, u.surname, u.name, u.patronymic, p.total, p.decision, d.proforma_id,d.proforma_spr_id,d.group_id,d.data_value,d.trener_id,d.expert_id,d.moderator_id
                                                    FROM p_proforma_user_data d
                                                    LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = d.user_id AND p.$filed_name = d.$filed_name 
                                                    LEFT OUTER JOIN p_user_fields u ON u.user_id = d.user_id                                                     
                                                    WHERE p.group_id = %d $fiels_text",$_GET['group'], $fiels_id));

// 2 Цикла для оценок
foreach ($results as $grade) {
    $arrGrade[$grade->user_id] = array(
        'user_id'=>$grade->user_id,
        'surname'=>$grade->surname,
        'name'=>$grade->name,
        'patronymic'=>$grade->patronymic,
        'total'=>$grade->total,
        'decision'=>$grade->decision,
        'trener_id'=>$grade->trener_id,
        'expert_id'=>$grade->expert_id,
        'moderator_id'=>$grade->moderator_id,
    );
}

foreach ($results as $grade) {
    array_push($arrGrade[$grade->user_id], $grade->data_value);
}


$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/portalcpi/template-word/'.$templateName);
$count = count($results)/9;  //9 критериев
$templateProcessor->setValue('date', htmlspecialchars(date('Y-m-d') ));
$templateProcessor->setValue('name_org', htmlspecialchars($grinf->$name_org));
$templateProcessor->setValue('group_name', htmlspecialchars($grinf->number_group));
$templateProcessor->setValue('subject', htmlspecialchars($subject));
$templateProcessor->setValue('position_text', htmlspecialchars($position_text));

$t=0;
$templateProcessor->cloneRow('rowNumber', $count);

foreach($arrGrade as $data) {
    $t++;
    for ($i = 0; $i < 9; $i++)
    {
        if ($data[$i] == -1){
            $k="-";
        } elseif ($data[$i] == 3){
            $k=" ";
        } else {
            $k=$data[$i];
        }
        $templateProcessor->setValue('k'.$i.'#'.$t, htmlspecialchars($k));
    }
    if ($data['decision'] == 'Зачет') $decision = ASSESSMENT_SHEET[7];
    if ($data['decision'] == 'Незачет') $decision = ASSESSMENT_SHEET[8];
    if ($data['decision'] == 'Неявка') $decision = ASSESSMENT_SHEET[10];
    if ($data['total'] == 'Плагиат') $total = "";
    elseif ($decision == ASSESSMENT_SHEET[10]) $total = "-";
    else $total = $data['total'];

    $templateProcessor->setValue('rowNumber#'.$t, htmlspecialchars($t));
    $templateProcessor->setValue('userName#'.$t, htmlspecialchars($data['surname']. ' ' .$data['name']. ' ' .$data['patronymic']));
    $templateProcessor->setValue('total#'.$t, htmlspecialchars($total));
    $templateProcessor->setValue('decision#'.$t, htmlspecialchars($decision));
}

$file = 'Проформа для оценивания.docx';

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



//$text = "Лист оценивания портфолио
//слушателей курсов повышения квалификации педагогов по образовательной программе
//«Разработка и экспертиза заданий для оценивания» по предмету «__________»";
//
//$phpWord->addFontStyle('r2Style', array('name' => 'Times New Roman', 'bold'=>true, 'italic'=>false, 'size'=>11));
//$phpWord->addParagraphStyle('p2Style', array('align'=>'center'));
//$section->addText($text, 'r2Style', 'p2Style');
//
//$fancyTableStyleName = 'Fancy Table';
//$fancyTableStyle = array('borderSize' => 0, 'borderColor' => '000000', 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
//$fancyTableFirstRowStyle = array();
//$fancyTableCellStyle = array('valign' => 'center');
//$fancyTableFontStyle = array('bold' => true);
//$phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
//$table = $section->addTable($fancyTableStyleName);
//$table->addRow(900);
//$table->addCell(500, $fancyTableCellStyle)->addText('№', $fancyTableFontStyle,'p2Style');
//$table->addCell(2000, $fancyTableCellStyle)->addText('ФИО слушателя', $fancyTableFontStyle,'p2Style');
//$table->addCell(2000, $fancyTableCellStyle)->addText('Раздел А', $fancyTableFontStyle,'p2Style');
//$table->addCell(2000, $fancyTableCellStyle)->addText('Раздел В', $fancyTableFontStyle,'p2Style');
//$table->addCell(2000, $fancyTableCellStyle)->addText('Итоговый балл ', $fancyTableFontStyle,'p2Style');
//for ($i = 1; $i <= 8; $i++) {
//    $table->addRow();
//    $table->addCell()->addText("");
//    $table->addCell()->addText("");
//    $table->addCell()->addText("");
//    $table->addCell()->addText("");
//    $table->addCell()->addText("");
//}
//
//
//$section->addTextBreak(1);
//
//// Inline font style
//$fontStyle['name'] = 'Times New Roman';
//$fontStyle['size'] = 10;
//$fontStyle['bold'] = true;
//
//$textrun = $section->addTextRun();
//$textrun->addText('Эксперт: ', 'r2Style');
//$textrun->addText('ФИО, ', $fontStyle);
//$textrun->addText('Дата: ' . date("d-m-Y"), $fontStyle);
//$textrun->addText('. ', $fontStyle);
//
//$file = 'Проформа для оценивания.docx';
