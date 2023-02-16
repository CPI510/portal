<?php
// Adding an empty Section to the document...
$sectionStyle = array('orientation' => 'landscape',
    'marginTop' => 500,
    'marginLeft' => 500,
    'marginRight' => 500,
    'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED
);
$section = $phpWord->addSection($sectionStyle);

$phpWord->addFontStyle('r2Style', array('name' => 'Times New Roman', 'bold'=>false, 'italic'=>false, 'size'=>10));
$phpWord->addParagraphStyle('p2Style', array('align'=>'right', 'spaceBefore' => 0, 'spaceAfter' => 0));
$phpWord->addFontStyle('rstyle2', array('name' => 'Times New Roman', 'bold'=>true, 'italic'=>false, 'size'=>11));
$phpWord->addParagraphStyle('pstyle2', array('align'=>'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
$phpWord->addParagraphStyle('pstyle3', array('align'=>'left', 'spaceBefore' => 0,  'spaceAfter' => 0));

$section->addTextBreak(1);

$phpWord->addFontStyle('rstyle2', array('name' => 'Times New Roman', 'bold'=>true, 'italic'=>false, 'size'=>11));
$phpWord->addParagraphStyle('pstyle2', array('align'=>'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
$section->addText("Обоснование ", 'rstyle2', 'pstyle2');
$section->addText("по оцениванию портфолио слушателя курсов повышения квалификации педагогов", 'rstyle2', 'pstyle2');
$section->addText("по образовательной программе «Разработка и экспертиза заданий для оценивания» по предметам в рамках обновления содержания среднего образования", 'rstyle2', 'pstyle2');

$section->addText("<w:br/>", 'r2Style', 'p2Style');

$group_info = groupInfo($_GET['group']);

$expert_res = $wpdb->get_results("SELECT * FROM p_user_fields WHERE access = 3");
foreach ($expert_res as $expert){
    $array_attach = explode(",", $expert->user_id_attached);
    $key = array_search($group_info->trener_id, $array_attach);

    if( $array_attach[$key] == $group_info->trener_id ){
        ++$q;
        if ($q > 1) $qu =", ";
        $all_experts .=  "$qu{$expert->surname} {$expert->name} {$expert->patronymic}";
    }
    $q=0;

}

$fancyTableStyleName = 'Fancy Table';
$fancyTableStyle = array('cellMargin'  => 50, 'borderSize' => 0, 'borderColor' => '000000', 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
$fancyTableFirstRowStyle = array();
$fancyTableCellStyle = array('valign' => 'center');
$fancyTableFontStyle = array('bold' => true);
$fancyTableFontStyle2 = array('bold' => false);
$phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
$table = $section->addTable($fancyTableStyleName);
$table->addRow(300);
$table->addCell(3000, $fancyTableCellStyle)->addText('ФИО слушателя', $fancyTableFontStyle,'pstyle2');
$table->addCell(4000, $fancyTableCellStyle)->addText(nameUser($_GET['uid'], 5), $fancyTableFontStyle2,'pstyle3');
$table->addCell(3000, $fancyTableCellStyle)->addText('ФИО тренера группы', $fancyTableFontStyle,'pstyle2');
$table->addCell(4000, $fancyTableCellStyle)->addText("{$group_info->surname} {$group_info->name} {$group_info->patronymic}", $fancyTableFontStyle2,'pstyle3');

$table->addRow(300);
$table->addCell(null, $fancyTableCellStyle)->addText('Центр обучения', $fancyTableFontStyle,'pstyle2');
$table->addCell(null, $fancyTableCellStyle)->addText($group_info->name_org, $fancyTableFontStyle2,'pstyle3');
$table->addCell(null, $fancyTableCellStyle)->addText('ФИО эксперта', $fancyTableFontStyle,'pstyle2');
$table->addCell(null, $fancyTableCellStyle)->addText($all_experts, $fancyTableFontStyle2,'pstyle3');

$section->addTextBreak(1);

$fancyTableStyleName2 = 'Fancy Table2';
$phpWord->addTableStyle($fancyTableStyleName2, $fancyTableStyle, $fancyTableFirstRowStyle);
$table2 = $section->addTable($fancyTableStyleName2);
$table2->addRow(300);
$table2->addCell(5000, $fancyTableCellStyle)->addText('Задания', $fancyTableFontStyle,'pstyle2');
$table2->addCell(10770, $fancyTableCellStyle)->addText('Обоснование по критериям и компонентам оценивания', $fancyTableFontStyle,'pstyle2');
$table2->addRow(2000);
$table2->addCell(5000, $fancyTableCellStyle)->addText('Раздел А. Задания по суммативному оцениванию за раздел/сквозную тему', $fancyTableFontStyle2,'pstyle3');

$finalData = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_proforma_user_result WHERE user_id= %d AND proforma_id = %d AND group_id =%d", $_GET['uid'], $_GET['form'], $_GET['group'] ));
$proformaDataUser = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_user_data WHERE user_id= %d AND proforma_id = %d AND group_id =%d", $_GET['uid'], $_GET['form'], $_GET['group'] ));
$proform_spr = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_spr WHERE proforma_id = %d", $_GET['form']));

foreach ($proformaDataUser as $data) {
    if($_GET['uid'] == 18){
        // Для презентации
        $data_text = "Цели соответствуют разделу учебной программы. Критерии оценивания не соответствуют целям обучения и уровням мыслительных навыков. Задания №2, №3, №4 не соответствуют критериям оценивания. Задания №3 и №4 не соответствуют уровням мыслительных навыков. Задания соответствуют возрастным особенностям. Формулировка заданий понятна.  Формулировка заданий не содержит подсказок. Время, отведенное на выполнение заданий не указано. Дескрипторы описывают наблюдаемые и измеримые действия/шаги по выполнению заданий, но не поясняется, при каком условии ставится каждый балл, когда указываются несколько элементов. Количество баллов соответствует уровню сложности заданий.";
        $data_text2 = "Не все цели обучения соответствуют спецификации. В характеристике несколько целей обучения и количество баллов за разделы не соответствуют спецификации. Для заданий №8 и №9 неверно указан уровень мыслительных навыков. Содержание заданий №1, №2, №4, №6 не соответствует целям обучения. Время на выполнение заданий распределено рационально. В схеме выставления баллов неверно указаны ответы к заданию №5 и №6.   Количество баллов соответствует уровню сложности заданий. ";
    }elseif($finalData->section_a == "Плагиат" && $proform_spr[$data->proforma_spr_id]->section_id == 1){
        $data_text = "Согласно подпункту 4 пункта 10 раздела 2 Правил организации и проведения процедур суммативного оценивания портфолио слушателей на курсах повышения квалификации педагогов по образовательной программе «Разработка и экспертиза заданий для оценивания» по предметам в рамках обновления содержания среднего образования, утвержденных решением Правления АОО от 12.07.2018 года (протокол №41), с внесенными изменениями и дополнениями, утвержденными решением Правления АОО от 22.05.2019 года (протокол №18) в разделе А обнаружен плагиат.";
        $extra_table = 1;
    }elseif($finalData->section_b == "Плагиат" && $proform_spr[$data->proforma_spr_id]->section_id == 2){
        $data_text2 = "Согласно подпункту 4 пункта 10 раздела 2 Правил организации и проведения процедур суммативного оценивания портфолио слушателей на курсах повышения квалификации педагогов по образовательной программе «Разработка и экспертиза заданий для оценивания» по предметам в рамках обновления содержания среднего образования, утвержденных решением Правления АОО от 12.07.2018 года (протокол №41), с внесенными изменениями и дополнениями, утвержденными решением Правления АОО от 22.05.2019 года (протокол №18) в разделе B обнаружен плагиат.";
        $extra_table = 1;
    }else{
        if($data->data_value == 2 && $proform_spr[$data->proforma_spr_id]->section_id == 1)
            $data_text .= $proform_spr[$data->proforma_spr_id]->name . ". ";
        elseif($data->data_value == 1 && $proform_spr[$data->proforma_spr_id]->section_id == 1)
            $data_text .= $proform_spr[$data->proforma_spr_id]->partially_name .". ";
        elseif($data->data_value == 0 && $proform_spr[$data->proforma_spr_id]->section_id == 1)
            $data_text .= $proform_spr[$data->proforma_spr_id]->negation_name;
        elseif($data->data_value == 2 && $proform_spr[$data->proforma_spr_id]->section_id == 2)
            $data_text2 .= $proform_spr[$data->proforma_spr_id]->name . ". ";
        elseif($data->data_value == 1 && $proform_spr[$data->proforma_spr_id]->section_id == 2)
            $data_text2 .= $proform_spr[$data->proforma_spr_id]->partially_name .". ";
        elseif($data->data_value == 0 && $proform_spr[$data->proforma_spr_id]->section_id == 2)
            $data_text2 .= $proform_spr[$data->proforma_spr_id]->negation_name;
    }

}

$table2->addCell(null, $fancyTableCellStyle)->addText($data_text, $fancyTableFontStyle2,'pstyle3');

$table2->addRow(2000);
$table2->addCell(5000, $fancyTableCellStyle)->addText('Раздел В. Задания по суммативному оцениванию за четверть', $fancyTableFontStyle2,'pstyle3');
$table2->addCell(null, $fancyTableCellStyle)->addText($data_text2, $fancyTableFontStyle2,'pstyle3');

$table2->addRow(500);
$table2->addCell(5000, $fancyTableCellStyle)->addText('Решение', $fancyTableFontStyle,'pstyle2');
$table2->addCell(null, $fancyTableCellStyle)->addText(" \"{$finalData->decision}\" ", $fancyTableFontStyle,'pstyle2');

$section->addTextBreak(1);

// Inline font style
$fontStyle['name'] = 'Times New Roman';
$fontStyle['size'] = 12;
$fontStyle['bold'] = true;

$textrun = $section->addTextRun();
$textrun->addText('Подпись эксперта       ____________________________________________                ', $fontStyle);

$textrun->addText('Дата: ' . date("d-m-Y"), $fontStyle);
$textrun->addText('. ', $fontStyle);

$section->addTextBreak(1);

if($extra_table == 1){
    $fancyTableStyleName3 = 'Fancy Table3';
    $phpWord->addTableStyle($fancyTableStyleName3, $fancyTableStyle, $fancyTableFirstRowStyle);
    $table3 = $section->addTable($fancyTableStyleName3);
    $table3->addRow(500);
    $table3->addCell(16000, array('gridSpan' => 2, 'valign' => 'center'))->addText('Сравнительный анализ результатов проверки портфолио слушателя', $fancyTableFontStyle,'pstyle2');
    $table3->addRow(500);
    $table3->addCell(8000, $fancyTableCellStyle)->addText('Текст портфолио слушателя', $fancyTableFontStyle,'pstyle2');
    $table3->addCell(8000, $fancyTableCellStyle)->addText('Заимствованный текст', $fancyTableFontStyle,'pstyle2');
    $table3->addRow(1500);
    $table3->addCell(null, $fancyTableCellStyle)->addText('');
    $table3->addCell(null, $fancyTableCellStyle)->addText('');
}

$file = 'Обоснование по оцениванию портфолио слушателя.docx';
