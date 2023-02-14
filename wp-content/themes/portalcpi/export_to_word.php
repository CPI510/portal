<?php 
/* 
Template Name: export_to_word 
Template Post Type: post, page, product 
*/

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

global $wpdb;

if(!is_user_logged_in()) {
    auth_redirect();
}

if(getAccess(get_current_user_id())->access == 5) {
    echo "<br>";
    alertStatus('warning', 'Доступ закрыт');

    if($_SERVER['HTTP_REFERER']) $url = $_SERVER['HTTP_REFERER'];
    else $url = site_url();

    echo'<meta http-equiv="refresh" content="2;url=' . $url . '" />';
    exit();
}

$gr_info = groupInfo($_GET['group']);

$name_var = translateDir($_GET['group']);

if(!is_user_logged_in()) {
    auth_redirect();
}

require_once 'assets/vendor/phpoffice/phpword/bootstrap.php';

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

if($_GET['form'] && $_GET['group']) require_once 'template-word/t' . $_GET['form'] . '.php';
else exit("Нет данных!");

// Adding Text element with font customized using explicitly created font style object...
// $fontStyle = new \PhpOffice\PhpWord\Style\Font();
// $fontStyle->setBold(true);
// $fontStyle->setName('Tahoma');
// $fontStyle->setSize(13);
// $myTextElement = $section->addText('Отчет по комплектующим');
// $myTextElement->setFontStyle($fontStyle);

// Adding Text element to the Section having font styled by default...
// $section->addText(
//     'Лист оценивания портфолио 
//     слушателей курсов повышения квалификации педагогов по образовательной программе 
//     «Разработка и экспертиза заданий для оценивания» по предмету «__________» 
//     '
// );

// $results = $wpdb->get_results("SELECT id, name, date date_create, quantity FROM kit"); 
// 					 foreach($results as $item){
// 						 $count = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) co FROM kit_result WHERE notactive = 0 AND kit_id = %s", $item->id));
// 						 $section->addText(++$i . ". Наименование: " . $item->name .", количество у сотрудников: ". $count->co);
// 					 };

// Saving the document as OOXML file...
//$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
//$objWriter->save('helloWorld.docx');

// Saving the document as ODF file...
//$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
//$objWriter->save('helloWorld.odt');

// Saving the document as HTML file...
//$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
//$objWriter->save('helloWorld.html');

header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
$xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$xmlWriter->save("php://output");
/* Note: we skip RTF, because it's not XML-based and requires a different example. */
/* Note: we skip PDF, because "HTML-to-PDF" approach is used to create PDF documents. */


?>