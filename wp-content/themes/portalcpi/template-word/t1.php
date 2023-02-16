<?php
$sectionStyle = array('orientation' => 'landscape',
    'marginTop' => 500,
    'marginLeft' => 500,
    'marginRight' => 500,
);
// Adding an empty Section to the document...
$section = $phpWord->addSection($sectionStyle);

$phpWord->addFontStyle('r2Style', array('name' => 'Times New Roman', 'bold'=>false, 'italic'=>false, 'size'=>10));
$phpWord->addParagraphStyle('p2Style', array('align'=>'right', 'spaceBefore' => 0, 'spaceAfter' => 0));
$phpWord->addFontStyle('rstyle2', array('name' => 'Times New Roman', 'bold'=>true, 'italic'=>false, 'size'=>11));
$phpWord->addParagraphStyle('pstyle2', array('align'=>'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
$phpWord->addParagraphStyle('pstyle3', array('align'=>'center', 'spaceBefore' => 0, 'spaceAfter' => 0));

$group_info = groupInfo($_GET['group']);

$section->addText(PROFORMA[0], 'r2Style', 'p2Style');
$section->addText(PROFORMA[1], 'r2Style', 'p2Style');

$section->addTextBreak(1);

$section->addText(PROFORMA[2], 'rstyle2', 'pstyle2');

$section->addText("<w:br/>", 'r2Style', 'p2Style');

$expert_res = $wpdb->get_results("SELECT * FROM p_user_fields WHERE access = 3");
foreach ($expert_res as $expert){
    $array_attach = explode(",", $expert->user_id_attached);
    $key = array_search($group_info->trener_id, $array_attach);

    if( $array_attach[$key] == $group_info->trener_id ){
        ++$q;
        if ($q > 1) $qu =", ";
        $all_experts .=  "$qu{$expert->surname} {$expert->name} {$expert->patronymic}";
    }

}

$fancyTableStyleName = 'Fancy Table';
$fancyTableStyle = array('cellMargin'  => 50, 'borderSize' => 0, 'borderColor' => '000000', 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
$fancyTableFirstRowStyle = array();
$fancyTableCellStyle = array('valign' => 'center');
$fancyTableFontStyle = array('bold' => true);
$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
$cellRowContinue = array('vMerge' => 'continue');
$phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
$table = $section->addTable($fancyTableStyleName);
$table->addRow(500);
$table->addCell(12000,$cellRowSpan)->addText(PROFORMA[3]." <w:br/>{$group_info->name_org}", $fancyTableFontStyle,'pstyle3');
$table->addCell(2000, $fancyTableCellStyle)->addText(PROFORMA[4], $fancyTableFontStyle,'pstyle3');
$table->addCell(2000, $fancyTableCellStyle)->addText("{$group_info->surname} {$group_info->name} {$group_info->patronymic}", $fancyTableFontStyle,'pstyle3');
$table->addRow(500);
$table->addCell(null,$cellRowContinue);
$table->addCell()->addText(PROFORMA[5], $fancyTableFontStyle,'pstyle3');
$table->addCell()->addText($all_experts, $fancyTableFontStyle,'pstyle3');

$tablename2 = "Table2";
$phpWord->addTableStyle($tablename2, $fancyTableStyle, $fancyTableFirstRowStyle);
$table2 = $section->addTable($tablename2);
$table2->addRow(500);
$table2->addCell(null,array('gridSpan' => 13,'valign' => 'center'))->addText(PROFORMA[6], array('bold' => true), array('align'=>'center'));
$table2->addCell(null,array('gridSpan' => 9, 'valign' => 'center'))->addText(PROFORMA[7], array('bold' => true), array('align'=>'center'));
$table2->addRow(500);
$table2->addCell(100,$cellRowSpan)->addText("№", array('bold' => true), array('align'=>'center'));
$table2->addCell(1200,$cellRowSpan)->addText(PROFORMA[8], array('bold' => true), array('align'=>'center'));
$proform_category = $wpdb->get_results("SELECT * FROM p_proforma_category");
foreach ($proform_category as $cat){
    if($cat->id == 1) $gridSpan = array('gridSpan' => 1, 'valign' => 'center');
    if($cat->id == 2) $gridSpan = array('gridSpan' => 2, 'valign' => 'center');
    if($cat->id == 3) $gridSpan = array('gridSpan' => 6, 'valign' => 'center');
    if($cat->id == 4) $gridSpan = array('gridSpan' => 2, 'valign' => 'center');
    if($cat->id == 5) $gridSpan = array('gridSpan' => 4, 'valign' => 'center');
    if($cat->id == 6) $gridSpan = array('gridSpan' => 3, 'valign' => 'center');
    $table2->addCell(null, $gridSpan)->addText($cat->$name_var, $fancyTableFontStyle,'pstyle3');
}

$table2->addCell(500, array('vMerge' => 'restart', 'textDirection'=>\PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR) )->addText(PROFORMA[9]);
$table2->addCell(500, array('vMerge' => 'restart', 'textDirection'=>\PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR) )->addText(PROFORMA[10]);

$table2->addRow(3000);
$table2->addCell(null,$cellRowContinue);
$table2->addCell(null,$cellRowContinue);

$proform_spr = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_spr WHERE proforma_id = %d", $_GET['form']));
foreach ($proform_spr as $spr){
    $table2->addCell(800, array('textDirection'=>\PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR) )->addText($spr->$name_var);
}
$table2->addCell(null,$cellRowContinue);
$table2->addCell(null,$cellRowContinue);

$usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.surname, u.name, u.patronymic, u.email, r.total, r.decision, r.section_a, r.section_b
FROM p_groups_users g
LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
LEFT OUTER JOIN p_proforma_user_result r ON u.user_id = r.user_id
WHERE g.id_group = %d AND r.proforma_id = %d AND r.group_id = %d", $_GET['group'], $_GET['form'], $_GET['group']));

foreach ($usersField as $user){
    $user->decision = ($user->decision == 'Незачет') ? ASSESSMENT_SHEET[8] : ASSESSMENT_SHEET[7];
    ++$w;
    $proformaDataUser = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_user_data WHERE user_id= %d AND proforma_id = %d AND group_id =%d", $user->id_user, $_GET['form'], $_GET['group'] ));
    $table2->addRow(500);
    $table2->addCell()->addText($w);
    $table2->addCell()->addText("{$user->surname} {$user->name} {$user->patronymic}");
    $q=0;
    foreach ($proform_spr as $data){
        if ($proformaDataUser[$q]->data_value == 0){
            $datatext = 0;
        } elseif ($proformaDataUser[$q]->data_value == 3) {
            $datatext = "Плагиат";
        } else {
            $datatext = $proformaDataUser[$q]->data_value;
        }

        $table2->addCell()->addText($datatext);
        $q++;
    }


    $table2->addCell()->addText($user->total);
    $table2->addCell()->addText($user->decision);
}

$section->addTextBreak(1);

// Inline font style
$fontStyle['name'] = 'Times New Roman';
$fontStyle['size'] = 12;
$fontStyle['bold'] = true;

$textrun = $section->addTextRun();
$textrun->addText(PROFORMA[11].'       ____________________________________________                ', $fontStyle);

$textrun->addText(PROFORMA[12].': ' . date("d-m-Y"), $fontStyle);
$textrun->addText('. ', $fontStyle);

$file = 'Проформа для оценивания.docx';
