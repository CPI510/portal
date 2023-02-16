<?php
/* 
Template Name: server_user
Template Post Type: post, page, product 
*/

global $wpdb;

$_POST = json_decode(file_get_contents("php://input"), true);

//printAll($_POST); exit();

if (stripos($_POST, " ") !== false) {
    //echo $fio = explode(" ", $_POST);
}

if( isset($_GET['get_user']) && getAccess(get_current_user_id())->access == 1 ){
    $_POST = trim( json_decode(file_get_contents("php://input"), true) );
    $usersearch = $wpdb->get_results($s=$wpdb->prepare( "SELECT u.user_id, UPPER(u.email) email, UPPER(u.surname) surname, UPPER(u.name) name, UPPER(u.patronymic) patronymic, a.name_ru access_name_ru, a.name_kz access_name_kz
                                FROM p_user_fields u
                                INNER JOIN p_access a ON a.value = u.access WHERE u.active = 1 and (u.surname LIKE %s OR  u.name LIKE %s OR  u.patronymic LIKE %s OR  u.email LIKE %s)"
        ,"%$_POST%"
        ,"%$_POST%"
        ,"%$_POST%"
        ,"%$_POST%"
    ));
//echo $s;
    echo '<table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>ФИО</th>
                <th>Email</th>
                <th>Статус</th>
                <th></th>
                <th>Последний вход</th>
            </tr>
            </thead>
            <tbody>';
    $i=0;
    foreach ($usersearch as $user){
        ++$i;
        echo "
        <tr>
            <td>
                $i 
            </td>
            <td>
                <a href='/members/?z=user_page&id={$user->user_id}' class='text-primary'> 
                 " .firstUpperStr($user->surname). " " .firstUpperStr($user->name). " " .firstUpperStr($user->patronymic). "</a>
            </td>
            <td>
                " .mb_strtolower($user->email). " ";
            if (get_the_author_meta('mailveryfication', $user->user_id) == '0') echo "<code>не подтвержден</code> <a href='/members/?z=list&searchtext={$user->surname}&user_id={$user->user_id}&confirmemail=1' class='btn btn-primary btn-xs'>Подтвердить email</a>";
                echo "
            </td>
            <td>" .$user->access_name_ru. "
                
            </td><td>";

        if(getAccess(get_current_user_id())->access == 1 && $user->user_id != 1){
            echo "<a href='/members/?z=list&authAnoherid={$user->user_id}' class='text-success'>Войти под учеткой</a>
                    <a href='/members/?z=edit&id={$user->user_id}' class='btn btn-icon-toggle' data-original-title='Редактировать'><i class='fa fa-pencil'></i></a>
                                            <a href='/members/?z=list&d=d&id={$user->user_id}' class='btn btn-icon-toggle' onclick='return confirm('Вы действительно хотите удалить?');' data-original-title='Удалить'><i class='fa fa-trash-o'></i></a>";
        }

        echo "</td><td>" .lastlogin($user->user_id). "</td></tr>
        ";
    } echo '</tbody></table>';


}elseif( isset($_GET['get_user_group_id']) && getAccess(get_current_user_id())->access == 1 ){

    $_POST = json_decode(file_get_contents("php://input"), true);
    $usersearch = $wpdb->get_results($s=$wpdb->prepare( "SELECT user_id, UPPER(surname) surname, UPPER(name) name, UPPER(patronymic) patronymic  FROM p_user_fields WHERE access = 5 AND (surname LIKE %s OR  name LIKE %s OR  patronymic LIKE %s)"
        ,"%$_POST%"
        ,"%$_POST%"
        ,"%$_POST%"
    ));

    foreach ($usersearch as $user){
        $data .= "<a href='groups/?z=group&id={$_GET['get_user_group_id']}&adduser={$user->user_id}' class='text-primary'>{$user->surname} {$user->name} {$user->patronymic}</a><br>";
    }
    echo $data;

} elseif($_POST['userdata'][0] === "1" && md5(date("Y-m-d") . "pass@realtime") === $_POST['userdata'][8]){ //printAll($_POST); exit(); // регистрация учителей
    $grinf = groupInfo($_POST['userdata'][9]);
    $name_var = translateDir($_POST['userdata'][9]);
	$user_id = wp_create_user( $_POST['userdata'][5], $_POST['userdata'][7], $_POST['userdata'][5] );

	if ( is_wp_error( $user_id ) ) {
		if( $user_id->get_error_message() === 'Извините, это имя пользователя уже существует!') $errorText = EMAIL_DUBL;
			else $errorText = $user_id->get_error_message();
		alertStatus('warning', "{$errorText}");
	}
	else {

		if($_POST['userdata'][8]) $_POST['userdata'][8] = $_POST['userdata'][8];
		else $_POST['userdata'][8] = '5';

		$access = '5';
        userRandomStrMeta($user_id,$_POST['userdata'][9]);
        update_user_meta( $user_id, 'mailveryfication', '0' );

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

        $insertData = $wpdb->query($wpdb->prepare( "INSERT INTO p_user_fields_listeners ( `user_id`, `group_id`, `subject_id`, `region_id`, `lang_education_id` ) 
			VALUES (%d, %d, %d, %d, %d)"
            ,$user_id
            ,$_POST['userdata'][9]
            ,$_POST['userdata'][10]
            ,$_POST['userdata'][11]
            ,$_POST['userdata'][12]
        ));

		$linkCode = site_url() . "/registration/?id={$_POST['userdata'][9]}&code=" . get_the_author_meta('randomstring', $user_id);

        $messageText = REG_SEND_EMAIL[0]." {$_POST['userdata'][1]} {$_POST['userdata'][2]}! 
<br><br>".REG_SEND_EMAIL[1]."
<br><br>".REG_SEND_EMAIL[2]."
<br><br><a href='$linkCode'>$linkCode</a>
<br><br>".REG_SEND_EMAIL[3]."
<br><br>".REG_SEND_EMAIL[4];

        $attachments = ""; //array(WP_CONTENT_DIR . '/uploads/attach.zip');
        $headers = array(
            'From: Портал ЦПИ <portal@cpi.nis.edu.kz>',
            'content-type: text/html',
        );

        if ($grinf->mail_confirmation == 0) { //Для того что бы регать без подтверждения почты
            mailVeryficationMeta($user_id);
            $wpdb->insert( 'p_groups_users', [
                'id_user' => $user_id,
                'id_group' => $_POST['userdata'][9]
            ], [ '%d', '%d'] );
            $text_success = "<p class='lead'>".NOTIFICATION_REG[0]." <b>{$_POST['userdata'][1]} {$_POST['userdata'][2]} {$_POST['userdata'][3]}</b>! <br>".NOTIFICATION_REG[2]."</p>";
        } else {
            wp_mail($_POST['userdata'][5], REG_SEND_EMAIL[5], $messageText, $headers, $attachments);
            $text_success = "<p class='lead'>".NOTIFICATION_REG[0]." <b>{$_POST['userdata'][1]} {$_POST['userdata'][2]} {$_POST['userdata'][3]}</b>! <br>".NOTIFICATION_REG[1]."</p>";
        }

		//echo'<meta http-equiv="refresh" content="0;url=/members/?z=list" />'; exit();
	   if($usercreate) {
           alertStatus('success', $text_success);
       }
	}
}elseif($_POST['userdata'][0] === "2" && md5(date("Y-m-d") . "pass@realtime") === $_POST['userdata'][1]){
	$usercreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_groups_users ( `id_group`,`id_user` ) 
		VALUES (%d, %d)"
		,$_POST['userdata'][2]
		,get_current_user_id()
	));
	if($usercreate) {
		alertStatus('success',REG_SUCCESS);
	}

}elseif($_POST['userdata'][0] === "3" && md5(date("Y-m-d") . "pass@realtime") === $_POST['userdata'][8]){ // регистрация тренеров
    $user_id = wp_create_user( $_POST['userdata'][5], $_POST['userdata'][7], $_POST['userdata'][5] );
    $name_var = translateDir($_POST['userdata'][9]);

    if ( is_wp_error( $user_id ) ) {
        if( $user_id->get_error_message() === 'Извините, это имя пользователя уже существует!') $errorText = EMAIL_DUBL;
        else $errorText = $user_id->get_error_message();
        alertStatus('danger', "{$errorText}");
    }
    else { //printAll($_POST); exit();

        if($_POST['userdata'][8]) $_POST['userdata'][8] = $_POST['userdata'][8];
        else $_POST['userdata'][8] = '5';

        $access = '4';
        userRandomStrMeta($user_id);
        update_user_meta( $user_id, 'mailveryfication', '0' );

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

        $linkCode = site_url() . "/registration/?z=trener&code=" . get_the_author_meta('randomstring', $user_id);

        $messageText = "Здравствуйте {$_POST['userdata'][1]} {$_POST['userdata'][2]}!
<br><br>На портале ЦПИ был запрос на создание учетной записи с указанием Вашего адреса электронной почты.
<br><br>Для подтверждения новой учетной записи пройдите по следующему адресу:
<br><br><a href='$linkCode'>$linkCode</a>
<br><br>В большинстве почтовых программ этот адрес должен выглядеть как синяя ссылка, на которую достаточно нажать. Если это не так, просто скопируйте этот адрес и вставьте его в строку адреса в верхней части окна Вашего браузера.
<br><br>С уважением, администрация портала.";

        $attachments = ""; //array(WP_CONTENT_DIR . '/uploads/attach.zip');
        $headers = array(
            'From: Портал ЦПИ <portal@cpi.nis.edu.kz>',
            'content-type: text/html',
        );

        wp_mail($_POST['userdata'][5], 'Портал ЦПИ: подтверждение учетной записи', $messageText, $headers, $attachments);

        //echo'<meta http-equiv="refresh" content="0;url=/members/?z=list" />'; exit();
        if($usercreate) alertStatus('success',"<p class='lead'>".NOTIFICATION_REG[0]." <b>{$_POST['userdata'][1]} {$_POST['userdata'][2]} {$_POST['userdata'][3]}</b>! <br>
".NOTIFICATION_REG[1]."</p>");
    }

}elseif ($_GET['get_email'] == 24){
    $_POST = json_decode(file_get_contents("php://input"), true);
    echo $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) num FROM p_user_fields WHERE email = %s", $_POST));
}

