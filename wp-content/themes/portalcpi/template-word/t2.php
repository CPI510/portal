<?php

$text = "Лист оценивания портфолио 
слушателей курсов повышения квалификации педагогов по образовательной программе 
«Разработка и экспертиза заданий для оценивания» по предмету «__________»";

$phpWord->addFontStyle('r2Style', array('name' => 'Times New Roman', 'bold'=>true, 'italic'=>false, 'size'=>11));
$phpWord->addParagraphStyle('p2Style', array('align'=>'center'));
$section->addText($text, 'r2Style', 'p2Style');

$fancyTableStyleName = 'Fancy Table';
$fancyTableStyle = array('borderSize' => 0, 'borderColor' => '000000', 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
$fancyTableFirstRowStyle = array();
$fancyTableCellStyle = array('valign' => 'center');
$fancyTableFontStyle = array('bold' => true);
$phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
$table = $section->addTable($fancyTableStyleName);
$table->addRow(900);
$table->addCell(500, $fancyTableCellStyle)->addText('№', $fancyTableFontStyle,'p2Style');
$table->addCell(2000, $fancyTableCellStyle)->addText('ФИО слушателя', $fancyTableFontStyle,'p2Style');
$table->addCell(2000, $fancyTableCellStyle)->addText('Раздел А', $fancyTableFontStyle,'p2Style');
$table->addCell(2000, $fancyTableCellStyle)->addText('Раздел В', $fancyTableFontStyle,'p2Style');
$table->addCell(2000, $fancyTableCellStyle)->addText('Итоговый балл ', $fancyTableFontStyle,'p2Style');
for ($i = 1; $i <= 8; $i++) {
    $table->addRow();
    $table->addCell()->addText("");
    $table->addCell()->addText("");
    $table->addCell()->addText("");
    $table->addCell()->addText("");
    $table->addCell()->addText("");
}


$section->addTextBreak(1);

// Inline font style
$fontStyle['name'] = 'Times New Roman';
$fontStyle['size'] = 10;
$fontStyle['bold'] = true;

$textrun = $section->addTextRun();
$textrun->addText('Эксперт: ', 'r2Style');
$textrun->addText('ФИО, ', $fontStyle);
$textrun->addText('Дата: ' . date("d-m-Y"), $fontStyle);
$textrun->addText('. ', $fontStyle);

$file = 'Проформа для оценивания.docx';
