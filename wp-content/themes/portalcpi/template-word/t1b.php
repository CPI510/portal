<?php

// Adding an empty Section to the document...
$section = $phpWord->addSection();

$text = ASSESSMENT_SHEET[0];

$phpWord->addFontStyle('r2Style', array('name' => 'Times New Roman', 'bold'=>true, 'italic'=>false, 'size'=>11));
$phpWord->addParagraphStyle('p2Style', array('align'=>'center'));
$section->addText($text, 'r2Style', 'p2Style');

$phpWord->addParagraphStyle('pstyle2', array('align'=>'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
$phpWord->addParagraphStyle('pstyle3', array('align'=>'left', 'spaceBefore' => 0, 'spaceAfter' => 0));
$fancyTableFontStyle2 = array('bold' => false);

$fancyTableStyleName = 'Fancy Table';
$fancyTableStyle = array('cellMargin'  => 50, 'borderSize' => 0, 'borderColor' => '000000', 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
$fancyTableFirstRowStyle = array();
$fancyTableCellStyle = array('valign' => 'center');
$fancyTableFontStyle = array('bold' => true);
$phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
$table = $section->addTable($fancyTableStyleName);
$table->addRow(900);
$table->addCell(500, $fancyTableCellStyle)->addText('№', $fancyTableFontStyle,'p2Style');
$table->addCell(2000, $fancyTableCellStyle)->addText(ASSESSMENT_SHEET[1], $fancyTableFontStyle,'p2Style');
$table->addCell(2000, $fancyTableCellStyle)->addText(ASSESSMENT_SHEET[2], $fancyTableFontStyle,'p2Style');
$table->addCell(2000, $fancyTableCellStyle)->addText(ASSESSMENT_SHEET[3], $fancyTableFontStyle,'p2Style');
$table->addCell(2000, $fancyTableCellStyle)->addText(ASSESSMENT_SHEET[4], $fancyTableFontStyle,'p2Style');


$results = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.surname, u.name, u.patronymic, u.email, r.total, r.decision, r.section_a, r.section_b
FROM p_groups_users g
LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
LEFT OUTER JOIN p_proforma_user_result r ON u.user_id = r.user_id
WHERE g.id_group = %d AND r.proforma_id = %d AND r.group_id = %d", $_GET['group'], $_GET['form'], $_GET['group'] ));

foreach ($results as $user){
    $user->decision = ($user->decision == 'Незачет') ? ASSESSMENT_SHEET[8] : ASSESSMENT_SHEET[7];
    ++$w;
    $table->addRow();
    $table->addCell()->addText($w, $fancyTableFontStyle2, 'pstyle2');
    $table->addCell()->addText("{$user->surname} {$user->name} {$user->patronymic}", $fancyTableFontStyle2, 'pstyle3');
    $table->addCell()->addText($user->section_a, $fancyTableFontStyle2, 'pstyle2');
    $table->addCell()->addText($user->section_b, $fancyTableFontStyle2, 'pstyle2');
    if($user->total == "0" || $user->total < 20 ) $total = $user->decision;
    else $total = $user->total;
    $table->addCell()->addText($total, $fancyTableFontStyle2, 'pstyle2');
}

$section->addTextBreak(1);

// Inline font style
$fontStyle['name'] = 'Times New Roman';
$fontStyle['size'] = 10;
$fontStyle['bold'] = true;

$nameUser = nameUser(get_current_user_id(),5);
$textrun = $section->addTextRun();
$textrun->addText(ASSESSMENT_SHEET[5].': ', 'r2Style');
$textrun->addText($nameUser . '                  ________________________ ', $fontStyle);
$textrun->addText('              '.ASSESSMENT_SHEET[6].': ' . date("d-m-Y"), $fontStyle);
$textrun->addText('. ', $fontStyle);

$file = 'Лист оценивания.docx';
