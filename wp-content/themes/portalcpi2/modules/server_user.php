<?php
/* 
Template Name: server_user
Template Post Type: post, page, product 
*/

global $wpdb;


$_POST = json_decode(file_get_contents("php://input"), true);

//printAll($_POST);

if($_POST['userdata'][0] === "1" && md5(date("Y-m-d") . "pass@realtime") === $_POST['userdata'][8]){
	$user_id = wp_create_user( $_POST['userdata'][5], $_POST['userdata'][7], $_POST['userdata'][5] );

	if ( is_wp_error( $user_id ) ) {
		if( $user_id->get_error_message() === 'Извините, это имя пользователя уже существует!') $errorText = "Извините, этот Email уже существует!"; 
			else $errorText = $user_id->get_error_message();
		alertStatus('danger', "{$errorText}");
	}
	else {

		if($_POST['userdata'][8]) $_POST['userdata'][8] = $_POST['userdata'][8];
		else $_POST['userdata'][8] = '5';

		$access = '5';		

		$usercreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_user_fields ( `user_id`,`surname`, `name`, `patronymic`, `iin`, `tel`, `email`, `access` ) 
			VALUES (%d, %s, %s, %s, %s, %s, %s, %s)"
			,$user_id
			,$_POST['userdata'][1]
			,$_POST['userdata'][2]
			,$_POST['userdata'][3]
			,$_POST['userdata'][4]
			,$_POST['userdata'][6]
			,$_POST['userdata'][5]
			,$access
		));
		//echo'<meta http-equiv="refresh" content="0;url=/members/?z=list" />'; exit();
	   if($usercreate) alertStatus('success',"Уважаемый <b>{$_POST['userdata'][1]} {$_POST['userdata'][2]} {$_POST['userdata'][3]}</b> вы зарегистрированы в системе! Авторизуйтесь пожалуйста используя Email и пароль!");
	}
}elseif($_POST['userdata'][0] === "2" && md5(date("Y-m-d") . "pass@realtime") === $_POST['userdata'][1]){
	$usercreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_groups_users ( `id_group`,`id_user` ) 
		VALUES (%d, %d)"
		,$_POST['userdata'][2]
		,get_current_user_id()
	));
	if($usercreate) {
		alertStatus('success',"Вы успешно записаны в группу! Перейти в раздел загрузки файлов");
	}

}

