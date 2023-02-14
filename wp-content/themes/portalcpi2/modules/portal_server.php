<?php
/* 
Template Name: portal_server 
Template Post Type: post, page, product 
*/

global $wpdb;

$_POST = json_decode(file_get_contents("php://input"), true);

//printAll($_POST);

if(!is_user_logged_in()) {

	auth_redirect();
	
}else{
	if($_POST['userdata'][0] === "1"){
		$user_id = wp_create_user( $_POST['userdata'][5], $_POST['userdata'][8], $_POST['userdata'][5] );
	
		if ( is_wp_error( $user_id ) ) {
			if( $user_id->get_error_message() === 'Извините, это имя пользователя уже существует!') $errorText = "Извините, этот Email уже существует!"; 
			else $errorText = $user_id->get_error_message();
			alertStatus('danger', "{$errorText}");
		}
		else {

			$usercreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_user_fields ( `user_id`,`surname`, `name`, `patronymic`, `iin`, `tel`, `email`, `access` ) 
				VALUES (%d, %s, %s, %s, %s, %s, %s, %s)"
				,$user_id
				,$_POST['userdata'][1]
				,$_POST['userdata'][2]
				,$_POST['userdata'][3]
				,$_POST['userdata'][4]
				,$_POST['userdata'][6]
				,$_POST['userdata'][5]
				,$_POST['userdata'][7]
			));
			//echo'<meta http-equiv="refresh" content="0;url=/members/?z=list" />'; exit();
		   if($usercreate) alertStatus('success',"Пользователь <b>{$_POST['userdata'][1]} {$_POST['userdata'][2]} {$_POST['userdata'][3]}</b> добавлен!");
		}
	}elseif($_POST['userdata'][0] === "2"){
		$user_id = wp_update_user( [ 
			'ID'       => $_POST['userdata'][9], 
			'user_pass' => $_POST['userdata'][8],
			'user_login' => $_POST['userdata'][5],
			'user_email' => $_POST['userdata'][5],
		] );
		
		if ( is_wp_error( $user_id ) ) {
			alertStatus('danger', "{$user_id->get_error_message()}");
		}else{
			//printALL($_POST['userAttached']);

			$user_id_attached = implode(',',$_POST['userAttached'][0]);

			$userupdate = $wpdb->query($wpdb->prepare( "UPDATE p_user_fields SET surname = %s, 	name = %s, patronymic = %s, iin = %s, tel = %s, email = %s, access = %s, user_id_attached = %s 
			WHERE user_id = %d"
				,$_POST['userdata'][1]
				,$_POST['userdata'][2]
				,$_POST['userdata'][3]
				,$_POST['userdata'][4]
				,$_POST['userdata'][6]
				,$_POST['userdata'][5]
				,$_POST['userdata'][7]
				,$user_id_attached
				,$_POST['userdata'][9]
			));
			//echo $user_id_attached;
			
		
			// if(!empty($_POST['userdata'][8])) {
			// 	$pass = wp_set_password( $_POST['userdata'][8], $_POST['userdata'][9] );
			// 	alertStatus('success',"Пароль изменен!");
			// }
		}
		if($user_id) alertStatus('success',"Пользователь <b>{$_POST['userdata'][1]} {$_POST['userdata'][2]} {$_POST['userdata'][3]}</b> Изменен!");
			elseif($userupdate) alertStatus('success',"Пользователь <b>{$_POST['userdata'][1]} {$_POST['userdata'][2]} {$_POST['userdata'][3]}</b> Изменен!");
	
	}elseif($_POST['userdata'][0] === "3"){
		$results = $wpdb->get_row($wpdb->prepare("SELECT id FROM p_groups WHERE number_group = %s;",$_POST['userdata'][1] ));
		if($results) alertStatus('danger', "Такая группа есть в системе!");
		else{
			$groupcreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_groups ( `number_group`, `program_id`, `training_center`, `trener_id`, `start_date`, `end_date` ) 
				VALUES (%s, %s, %s, %s, %s, %s)"
				,$_POST['userdata'][1]
				,$_POST['userdata'][2]
				,$_POST['userdata'][3]
				,$_POST['userdata'][4]
				,$_POST['userdata'][5]
				,$_POST['userdata'][6]
			));
			if($groupcreate) alertStatus('success',"Группа: <b>{$_POST['userdata'][1]}</b> была создана!");
		}
	}elseif($_POST['userdata'][0] === "4"){
		$groupupdate = $wpdb->query($wpdb->prepare( "UPDATE p_groups set `number_group` = %s, `program_id` = %s, `training_center` = %s, `trener_id` = %s, `start_date` = %s, `end_date` = %s, `active` = %s 
		WHERE id = %d"
				,$_POST['userdata'][1]
				,$_POST['userdata'][2]
				,$_POST['userdata'][3]
				,$_POST['userdata'][4]
				,$_POST['userdata'][5]
				,$_POST['userdata'][6]
				,$_POST['userdata'][7]
				,$_POST['userdata'][8]
			));
			$wpdb->last_error;
			if($groupupdate) alertStatus('success',"Группа: <b>{$_POST['userdata'][1]}</b> была изменена!");
	}
}