<?php

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

if($grinf->expert_id == get_current_user_id() && $grinf->program_id = 18){
    $filed_name = "expert_id";
    $fiels_text = "AND p.expert_id = %d";
    $fiels_id = $grinf->expert_id;
    $templateName = 'templates/18template_proforma_'.$docLang.'_expert_review.docx';
    $position_text = 'Эксперт';
}elseif($grinf->moderator_id == get_current_user_id() && $grinf->program_id = 18){
    $filed_name = "moderator_id";
    $fiels_text = "AND p.moderator_id = %d";
    $fiels_id = $grinf->moderator_id;
    $templateName = 'templates/18template_proforma_'.$docLang.'_expert_review.docx';
    $position_text = 'Модератор';
}

$results = $wpdb->get_results($wpdb->prepare("SELECT d.id, d.user_id, u.surname, u.name, u.patronymic, p.total, p.decision,p.review, d.proforma_id,d.proforma_spr_id,d.group_id,d.data_value,d.trener_id,d.expert_id,d.moderator_id
                                                    FROM p_proforma_user_data d
                                                    LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = d.user_id AND p.$filed_name = d.$filed_name 
                                                    LEFT OUTER JOIN p_user_fields u ON u.user_id = d.user_id                                                     
                                                    WHERE p.group_id = %d AND d.user_id = %d $fiels_text",$_GET['group'],$_GET['user_id'], $fiels_id));

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
        'review'=>$grade->review
    );
}

foreach ($results as $grade) {
    array_push($arrGrade[$grade->user_id], $grade->data_value);
}



$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/portalcpi/template-word/'.$templateName);
$templateProcessor->setValue('date', htmlspecialchars(date('Y-m-d') ));
$templateProcessor->setValue('name_org', htmlspecialchars($grinf->$name_org));
$templateProcessor->setValue('group_name', htmlspecialchars($grinf->number_group));
$templateProcessor->setValue('subject', htmlspecialchars($grinf->subject));
$templateProcessor->setValue('position_text', htmlspecialchars($position_text));

$t=0;

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
        $templateProcessor->setValue('k'.$i, htmlspecialchars($k));
//        printAll('k'.$i.'#'.$t);
//        exit();
    }
    $templateProcessor->setValue('userName', htmlspecialchars($data['surname']. ' ' .$data['name']. ' ' .$data['patronymic']));
    $templateProcessor->setValue('total', htmlspecialchars($data['total']));
    $templateProcessor->setValue('decision', htmlspecialchars($data['decision']));
    $templateProcessor->setValue('review', htmlspecialchars($data['review']));
}

$file = 'focus_review.docx';

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

