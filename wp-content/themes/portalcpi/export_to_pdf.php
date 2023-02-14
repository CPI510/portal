<?php
/*
Template Name: export_to_pdf
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
require '/var/www/html/wp-content/themes/portalcpi/assets/pdf/vendor/autoload.php';

use Dompdf\Dompdf;

$grinf = groupInfo($_GET['group']);

if($name_var = translateDir($_GET['group']) == 'name'){
    $p_name = "p_name";
    $name = 'name';
    $lang_name = 'lang_name_ru';
    $name_org = 'name_org';
}else{
    $p_name = "name_kaz";
    $name = "name_kaz";
    $lang_name = 'lang_name_kz';
    $name_org = "name_org_kaz";
}

if($_GET['position'] == '1'){
    $position = "тренера";
} elseif ($_GET['position'] == '2'){
    $position = "независимого тренера";
} elseif ($_GET['position'] == '3'){
    $position = "модератора";
}

$allName = nameUser($_GET['create_user_id'], 5);
$grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");

foreach ($grades as $grade){
    $arrGrade[$grade->id] = $grade->$name;
}

// instantiate and use the dompdf class
if($grinf->program_id == 7 && ( $grinf->independent_trainer_id == get_current_user_id() || $grinf->moderator_id == get_current_user_id() )){
    $results = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, c.code surname, p.create_user_id, p.section_a_grade, p.section_b_grade, p.section_c_grade, p.grading_solution
                                        FROM p_groups_users g
                                        LEFT OUTER JOIN p_assessment_rubric p ON p.listener_id = g.id_user
                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
                                        LEFT OUTER JOIN p_assessment_coding_user c ON c.listener_id = g.id_user AND c.group_id = %d
                                        WHERE g.id_group = %d AND p.group_id = %d AND p.create_user_id = %d", $_GET['group'], $_GET['group'], $_GET['group'], get_current_user_id() ));
    $groupData = '';
    if($grinf->moderator_id == get_current_user_id()){

        $list_text = FOR_MODERATION;
        $position_text = ASSESSMENT_SHEET[5];
        $groupData = '<b>'.ASSESSMENT_SHEET[6].':</b> '.date('Y-m-d').' <br>
        <b>'.PROFORMA[5].':</b> '.$allName.'<br>';

    }elseif($grinf->independent_trainer_id == get_current_user_id()){

        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $groupData = '<b>'.PROFORMA[12].':</b> '.date('Y-m-d').' <br>
        <b>'.PLACE_STUDY.':</b> <br>
        <b>'.GROUP.':</b> <br>
        <b>'.LANG_EDUCATION.':</b> '.$grinf->$lang_name.'<br>
        <b>'.FIO_ALL_TRENER[1].' :</b> '.$allName.'<br>';

    }elseif($grinf->trener_id == get_current_user_id()){

        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[0];
        $groupData = '<b>'.PROFORMA[12].':</b> '.date('Y-m-d').' <br>
        <b>'.PLACE_STUDY.':</b> <br>
        <b>'.GROUP.':</b> <br>
        <b>'.LANG_EDUCATION.':</b> '.$grinf->$lang_name.'<br>
        <b>'.FIO_ALL_TRENER[0].' :</b> '.$allName.'<br>';

    }

}else{
    $results = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.surname, u.name, u.patronymic, u.email, p.create_user_id, p.section_a_grade, p.section_b_grade, p.section_c_grade, p.grading_solution
                                        FROM p_groups_users g
                                        LEFT OUTER JOIN p_assessment_rubric p ON p.listener_id = g.id_user
                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
                                        WHERE g.id_group = %d AND p.group_id = %d AND p.create_user_id = %d", $_GET['group'], $_GET['group'], $_GET['create_user_id'] ));

    if($grinf->independent_trainer_id == $_GET['create_user_id']){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[1];
        $positionInGroupText = FIO_ALL_TRENER[1];

    }elseif($grinf->moderator_id == $_GET['create_user_id']){
        $list_text = FOR_MODERATION;
        $position_text = PROFORMA[11];
        $positionInGroupText = PROFORMA[5];
    }elseif($grinf->trener_id == $_GET['create_user_id']){
        $list_text = FOR_ALL_TRENER;
        $position_text = TRENER_GROUP_TEXT[0];
        $positionInGroupText = FIO_ALL_TRENER[0];
    }

    $groupData = '<b>'.PROFORMA[12].':</b> '.date('Y-m-d').' <br>
        <b>'.PLACE_STUDY.':</b> '.$grinf->$name_org.'<br>
        <b>'.GROUP.':</b> '.$grinf->number_group.'<br>
        <b>'.LANG_EDUCATION.':</b> '.$grinf->$lang_name.'<br>
        <b>'.$positionInGroupText.':</b> '.$allName.'<br>';

}

$html = '
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	
	@font-face {
      font-family: "Arial";
    }
    
		* { 
			font-family: Arial;
			font-size: 14px;
			line-height: 14px;
		}
		table {
			margin: 0 0 15px 0;
			width: 100%;
			border-collapse: collapse; 
			border-spacing: 0;
		}		
		table td {
			padding: 5px;
		}	
		table th {
			padding: 5px;
			font-weight: bold;
		}
 
		.header {
			margin: 0 0 0 0;
			padding: 0 0 15px 0;
			font-size: 12px;
			line-height: 12px;
			text-align: center;
		}
		
		/* Реквизиты банка */
		.details td {
			padding: 3px 2px;
			border: 1px solid #000000;
			font-size: 12px;
			line-height: 12px;
			vertical-align: top;
		}
 
		h1 {
			margin: 0 0 10px 0;
			padding: 10px 0 10px 0;
			border-bottom: 2px solid #000;
			font-weight: bold;
			font-size: 20px;
		}
 
		/* Поставщик/Покупатель */
		.contract th {
			padding: 3px 0;
			vertical-align: top;
			text-align: left;
			font-size: 13px;
			line-height: 15px;
		}	
		.contract td {
			padding: 3px 0;
		}		
 
		/* Наименование товара, работ, услуг */
		.list thead, .list tbody  {
			border: 2px solid #000;
		}
		.list thead th {
			padding: 4px 0;
			border: 1px solid #000;
			vertical-align: middle;
			text-align: center;
		}	
		.list tbody td {
			padding: 0 2px;
			border: 1px solid #000;
			vertical-align: middle;
			font-size: 11px;
			line-height: 13px;
		}	
		.list tfoot th {
			padding: 3px 2px;
			border: none;
			text-align: right;
		}	
 
		/* Сумма */
		.total {
			margin: 0 0 20px 0;
			padding: 0 0 10px 0;
			border-bottom: 2px solid #000;
		}	
		.total p {
			margin: 0;
			padding: 0;
		}
		
		/* Руководитель, бухгалтер */
		.sign {
			position: relative;
		}
		.sign table {
			width: 60%;
		}
		.sign th {
			padding: 40px 0 0 0;
			text-align: left;
		}
		.sign td {
			padding: 40px 0 0 0;
			border-bottom: 1px solid #000;
			text-align: right;
			font-size: 12px;
		}
		
		.sign-1 {
			position: absolute;
			left: 149px;
			top: -44px;
		}	
		.sign-2 {
			position: absolute;
			left: 149px;
			top: 0;
		}	
		.printing {
			position: absolute;
			left: 271px;
			top: -15px;
		}
	</style>
</head>
<body>
	<p class="header">
		'.$grinf->$p_name.' '.$list_text.' 
    </p>
    <p>
		'.$groupData.'
	</p>
 
	<table class="details" border="1">
        <tr>
            <th rowspan="2">№</th>
            <th rowspan="2">'.FULL_NAME_LEADER.'</th>
            <th colspan="3"><div align="center">'.NAME_REPORTS_PORTFOLIO.'</div></th>
            <th rowspan="2">'.GRADING_DESCISION.'</th>
        </tr>
        <tr>
            <td>
                <b>'.ASSESSMENT_RUBRIC[1].'</b>
                '.REPORT_A_DESCRIPTION.'
            </td>
            <td>
                <b>'.ASSESSMENT_RUBRIC[3].'</b> '.REPORT_B_DESCRIPTION.'
            </td>
            <td>
                <b>'.ASSESSMENT_RUBRIC[5].'</b>
                   '.REPORT_C_DESCRIPTION.'
            </td>
        </tr>';
$t=0; foreach($results as $res){
    $t++;
    $html .= '
        <tr>
            <td>' .$t. '</td>
            <td>' .$res->surname. ' ' .$res->name. ' ' .$res->patronymic. '</td>
            <td>' .$arrGrade[$res->section_a_grade]. '</td>
            <td>' .$arrGrade[$res->section_b_grade]. '</td>
            <td>' .$arrGrade[$res->section_c_grade]. '</td>
            <td>' .$arrGrade[$res->grading_solution]. '</td>
        </tr>';
}

$html .= '</table>
<p>
    <b>'.$position_text.':</b><br><br> _____________________________________________________________________<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;('.SIGN_TEXT.')                          ('.FIO_FULL.')
</p></body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();
