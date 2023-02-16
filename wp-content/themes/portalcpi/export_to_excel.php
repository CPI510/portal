<?php
/*
Template Name: export_to_excel
Template Post Type: post, page, product
*/

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

//require_once 'assets/vendor/phpoffice/phpword/bootstrap.php';
require 'assets/excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This script should only be run from a Web Browser' . PHP_EOL);

    return;
}

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

if($_GET['form'] && $_GET['group']) require_once 'template-excel/excel_' . $_GET['form'] . '.php';
else exit("Нет данных!");

if($_GET['download'] == 'pdf'){
    IOFactory::registerWriter('Pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class);

// Redirect output to a client’s web browser (PDF)
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment;filename="01simple.pdf"');
    header('Cache-Control: max-age=0');
    $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
    exit('22');
    $writer->save('php://output');
    exit;
}else{
    // Redirect output to a client’s web browser (Xls)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}

