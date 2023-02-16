<?php
/* 
Template Name: portal_server 
Template Post Type: post, page, product 
*/

global $wpdb;

$_POST = json_decode(file_get_contents("php://input"), true);

//printAll($_POST); exit();

if(!is_user_logged_in()) {

	//auth_redirect();
    //wp_redirect( site_url() . "/login/" );
    sessionexpired();
	
}else{
	if($_POST['statement'] === "1"){
		$user_id = wp_create_user( $_POST['u_email'], $_POST['u_pass'], $_POST['u_email'] );
	
		if ( is_wp_error( $user_id ) ) {
			if( $user_id->get_error_message() === 'Извините, это имя пользователя уже существует!') $errorText = "Извините, этот Email уже существует!"; 
			else $errorText = $user_id->get_error_message();
			alertStatus('danger', "{$errorText}");
		}
		else { //Добавление пользователя

			$usercreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_user_fields ( `user_id`,`surname`, `name`, `patronymic`, `iin`, `tel`, `email`, `access`, `date_create` ) 
				VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s)"
				,$user_id
				,$_POST['u_surname']
				,$_POST['u_name']
				,$_POST['u_patronymic']
				,$_POST['u_iin']
				,$_POST['u_tel']
				,$_POST['u_email']
				,$_POST['u_access']
                ,dateTime()
			));
            if($usercreate && isset($_POST['subject']) && isset($_POST['region'])){
                $insertData = $wpdb->query($wpdb->prepare( "INSERT INTO p_user_fields_listeners ( `user_id`, `group_id`, `subject_id`, `region_id`, `datetime_create` ) 
			    VALUES (%d, %d, %d, %d, %s)"
                    ,$user_id
                    ,$_POST['group']
                    ,$_POST['subject']
                    ,$_POST['region']
                    ,dateTime()
                ));
            }
            if($usercreate && isset($_POST['group']) ){
                $insertData = $wpdb->query($wpdb->prepare( "INSERT INTO p_groups_users ( `id_group`, `id_user`, `date_reg`) 
			    VALUES (%d, %d, %s)"
                    ,$_POST['group']
                    ,$user_id
                    ,dateTime()
                ));

                echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['group'].'" />';
            }



            //$mail = mailVeryficationMeta($user_id);
			//echo'<meta http-equiv="refresh" content="0;url=/members/?z=list" />'; exit();
		   if($usercreate) alertStatus('success',"Пользователь <b>{$_POST['u_surname']} {$_POST['u_name']} {$_POST['u_patronymic']}</b> добавлен!");
		}
	}elseif($_POST['statement'] === "2"){ //printAll($_POST); exit(); //Обновление пользователя
        //printALL($_POST); exit();
	    if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,}$/', $_POST['u_pass']) && $_POST['u_pass'] != ""){
            alertStatus('danger', "Требование к паролю: Минимум 8 символов, одна цифра, одна буква в верхнем регистре и одна в нижнем");
        }else{
            $user_id = wp_update_user( [
                'ID'       => $_POST['user_id'],
                'user_pass' => $_POST['u_pass'],
                'user_login' => $_POST['u_email'],
                'user_email' => $_POST['u_email'],
            ] );

            if ( is_wp_error( $user_id ) ) {
                alertStatus('danger', "{$user_id->get_error_message()}");
            }else{ //Редактирование пользователя



                if(isset($_POST['program_id'])){  //если редактирование с полем "Выбор программы (по умолчанию будет выбранна в списке групп)"
                    $userupdate = $wpdb->query($s=$wpdb->prepare( "UPDATE p_user_fields SET surname = %s, 	name = %s, patronymic = %s, iin = %s, tel = %s, email = %s, access = %s,  program_id = %d
			WHERE user_id = %d"
                        ,$_POST['u_surname']
                        ,$_POST['u_name']
                        ,$_POST['u_patronymic']
                        ,$_POST['u_iin']
                        ,$_POST['u_tel']
                        ,$_POST['u_email']
                        ,$_POST['u_access']
                        ,$_POST['program_id']
                        ,$_POST['user_id']
                    ));
                    //echo $s;
                }else{ //printALL($_POST); exit(); //если редактирование без "Выбор программы (по умолчанию будет выбранна в списке групп)"
                    $userupdate = $wpdb->query($wpdb->prepare( "UPDATE p_user_fields SET surname = %s, 	name = %s, patronymic = %s, iin = %s, tel = %s, email = %s, access = %s
			WHERE user_id = %d"
                        ,$_POST['u_surname']
                        ,$_POST['u_name']
                        ,$_POST['u_patronymic']
                        ,$_POST['u_iin']
                        ,$_POST['u_tel']
                        ,$_POST['u_email']
                        ,$_POST['u_access']
                        ,$_POST['user_id']
                    ));

                    if($_POST['subject'] && $_POST['region'] ){ // если поступил регион и предмет
                        $subjectRegion = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_user_fields_listeners WHERE user_id = %d", $_POST['user_id']));
                        if(isset($subjectRegion)){
                            $updateSubjectRegion = $wpdb->update('p_user_fields_listeners',
                                ['subject_id' => $_POST['subject'], 'region_id' => $_POST['region'],],
                                ['user_id' => $_POST['user_id']],
                                ['%d','%d'],
                                ['%d']
                            );
                        }else{
                            $insertData = $wpdb->query($wpdb->prepare( "INSERT INTO p_user_fields_listeners ( `user_id`, `subject_id`, `region_id` ) VALUES (%d, %d, %d)"
                                ,$_POST['user_id']
                                ,$_POST['subject']
                                ,$_POST['region']
                            ));
                        }
                    }


                }


                //echo $user_id_attached;


                // if(!empty($_POST)) {
                // 	$pass = wp_set_password( $_POST, $_POST );
                // 	alertStatus('success',"Пароль изменен!");
                // }
                if($user_id) alertStatus('success',"Пользователь <b>{$_POST['u_surname']} {$_POST['u_name']} {$_POST['u_patronymic']}</b> Изменен!");
                elseif($userupdate) alertStatus('success',"Пользователь <b>{$_POST['u_surname']} {$_POST['u_name']} {$_POST['u_patronymic']}</b> Изменен!");
            }
        }

	}elseif($_POST['statement'] === "3"){ // Создание группы
		$results = $wpdb->get_row($wpdb->prepare("SELECT id FROM p_groups WHERE number_group = %s;",$_POST['number_group'] ));
        if($results) alertStatus('danger', "Такая группа есть в системе!");
		else{

            $program_subsection = (isset($_POST['program_subsection'])) ? $_POST['program_subsection'] : "0"; //Для 6 программы ЭО ЛУПС ЛУШ
            $independent_trainer_date = (isset($_POST['independent_trainer_date'])) ? $_POST['independent_trainer_date'] : "0"; //

            if( ( ($_POST['expert_id'] != 0 && $_POST['moderator_id'] != 0)
                || ($_POST['expert_id'] != 0 && $_POST['teamleader_id'] != 0)
                || ($_POST['moderator_id'] != 0 && $_POST['teamleader_id'] != 0) )
                && ( ($_POST['expert_id'] == $_POST['moderator_id'])
                || ($_POST['expert_id'] == $_POST['teamleader_id'])
                || ($_POST['moderator_id'] == $_POST['teamleader_id']) )
            )
            {
                if($_POST['expert_id'] == $_POST['moderator_id']) $text = "Модератора и Эксперта";
                if($_POST['expert_id'] == $_POST['teamleader_id']) $text = "Эксперта и Тимлидера";
                if($_POST['moderator_id'] == $_POST['teamleader_id']) $text = "Модератора и Тимлидера";
                if( ($_POST['expert_id'] == $_POST['moderator_id']) && ( $_POST['moderator_id'] == $_POST['teamleader_id']) ) $text = "Эксперта и Модератора и Тимлидера";
                alertStatus('warning', "Один струдник ЦПИ выбран в качестве $text");
            }else{
                $groupcreate = $wpdb->query($s=$wpdb->prepare( "INSERT INTO p_groups ( 
`number_group`, `program_id`, `training_center`, `trener_id`, `trener_date`, `start_date`, `end_date`, `lang_id`, `expert_id`, 
`moderator_id`, `teamleader_id`, `admin_id`, `potok`, `expert_date`,`moderator_date`,`teamleader_date`,`independent_trainer_id`,`independent_trainer_date`, `program_subsection` ) 
				VALUES (%s, %d, %d, %d, %s, %s, %s, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d, %s, %d)"
                    ,$_POST['number_group']
                    ,$_POST['program_id']
                    ,$_POST['p_training_center']
                    ,$_POST['trener']
                    ,$_POST['trener_date']
                    ,$_POST['start_date']
                    ,$_POST['end_date']
                    ,$_POST['lang_id']
                    ,$_POST['expert_id'] //expert_id
                    ,$_POST['moderator_id'] //moderator_id
                    ,$_POST['teamleader_id'] // teamleader_id
                    ,get_current_user_id()
                    ,$_POST['potok']
                    ,$_POST['expert_date']
                    ,$_POST['moderator_date']
                    ,$_POST['teamleader_date']
                    ,$_POST['independent_trainer_id']
                    ,$independent_trainer_date
                    ,$program_subsection
                )); //echo $s;
                if($groupcreate){
                    alertStatus('success',"Группа: <b>{$_POST['number_group']}</b> была создана! <p><a href='/groups/?z=add' class='btn btn-info'>Добавить новую группу</a></p>");
                }
            }

		}
	}elseif($_POST['statement'] === "4"){ // Редактирование группы

        $program_subsection = (isset($_POST['program_subsection'])) ? $_POST['program_subsection'] : "0"; //Для 6 программы ЭО ЛУПС ЛУШ

        if( ( ($_POST['expert_id'] != 0 && $_POST['moderator_id'] != 0)
                || ($_POST['expert_id'] != 0 && $_POST['teamleader_id'] != 0)
                || ($_POST['moderator_id'] != 0 && $_POST['teamleader_id'] != 0) )
            && ( ($_POST['expert_id'] == $_POST['moderator_id'])
                || ($_POST['expert_id'] == $_POST['teamleader_id'])
                || ($_POST['moderator_id'] == $_POST['teamleader_id']) )
        )
    {
        if($_POST['expert_id'] == $_POST['moderator_id']) $text = "Модератора и Эксперта";
        if($_POST['expert_id'] == $_POST['teamleader_id']) $text = "Эксперта и Тимлидера";
        if($_POST['moderator_id'] == $_POST['teamleader_id']) $text = "Модератора и Тимлидера";
        if( ($_POST['expert_id'] == $_POST['moderator_id']) && ( $_POST['moderator_id'] == $_POST['teamleader_id']) ) $text = "Эксперта и Модератора и Тимлидера";
        alertStatus('warning', "Один струдник ЦПИ выбран в качестве $text");
    }else{
        $groupupdate = $wpdb->query($s=$wpdb->prepare( "UPDATE p_groups set `number_group` = %s, `program_id` = %d, `training_center` = %d, `trener_id` = %d, `trener_date` = %s, `start_date` = %s, 
`end_date` = %s, `active` = %s, `lang_id` = %d, `expert_id` = %d, `moderator_id` = %d, `teamleader_id` = %d, `potok` = %d, `expert_date` = %s, `moderator_date` = %s, `teamleader_date` = %s , `independent_trainer_id` = %d, `independent_trainer_date` = %s, `program_subsection` = %d 
		WHERE id = %d"
            ,$_POST['number_group']
            ,$_POST['program_id']
            ,$_POST['p_training_center']
            ,$_POST['trener']
            ,$_POST['trener_date']
            ,$_POST['start_date']
            ,$_POST['end_date']
            ,$_POST['active']
            ,$_POST['lang_id']
            ,$_POST['expert_id'] //expert_id
            ,$_POST['moderator_id'] //moderator_id
            ,$_POST['teamleader_id'] // teamleader_id
            ,$_POST['potok']
            ,$_POST['expert_date']
            ,$_POST['moderator_date']
            ,$_POST['teamleader_date']
            ,$_POST['independent_trainer_id']
            ,$_POST['independent_trainer_date']
            ,$program_subsection
            ,$_POST['id'] // id
        ));
        $wpdb->last_error;
        if($groupupdate) alertStatus('success',"Группа: <b>{$_POST['number_group']}</b> была изменена!");
    }



	}elseif(isset($_GET['list_file_group_id'])){
        $name_var = translateDir($_GET['list_file_group_id']);
        $access = getAccess(get_current_user_id())->access;
        //printAll($_POST); exit();
        ?>

        <h3><?= USER_FILES ?>: <?php  nameUser($_POST['fileuserdata'],3)?></h3>

        <div class="card">

            <div class="card-body">
                <?php $results = $wpdb->get_results($s=$wpdb->prepare("SELECT a.id, a.datecreate, a.filename, a.filedir, a.filesize, b.name folder_name, c.start_date, c.end_date, a.portfolio
                        FROM p_file a
                        LEFT OUTER JOIN p_folder b ON b.id = a.folder
                        LEFT OUTER JOIN p_groups c ON c.id = a.group_id
                        WHERE a.group_id = %d AND a.user_id = %d ORDER BY b.sort_field",$_GET['list_file_group_id'], $_POST['fileuserdata'] ));
                ?>
                <table class="table no-margin table-hover" id="dataFile">
                    <tbody>
                    <tr>
                        <th><?= DIR_NAME ?></th>
                        <th><?= FILE_NAME ?></th>
                        <th><?= FILE_SIZE ?></th>
                        <th><?= DOWNLOADED_TIME ?></th>
                        <th></th>
                    </tr>
                    <?php if( ( (getCourse($_GET['list_file_group_id'], 'program_id') == 7 || getCourse($_GET['list_file_group_id'], 'program_id') == 14) &&  getCourse($_GET['list_file_group_id'], 'trener_date') > dateTime() &&  getCourse($_GET['list_file_group_id'], 'trener_id') == get_current_user_id() ) //
                        || ( getCourse($_GET['list_file_group_id'], 'end_date') > dateTime() && $access == 4 )
                        || ( getCourse($_GET['list_file_group_id'], 'expert_id') == get_current_user_id() && getCourse($_GET['list_file_group_id'], 'expert_date') > dateTime() )
                        || ( getCourse($_GET['list_file_group_id'], 'moderator_id') == get_current_user_id() && getCourse($_GET['list_file_group_id'], 'moderator_date') > dateTime() )
                        || ( getCourse($_GET['list_file_group_id'], 'teamleader_id') == get_current_user_id() && getCourse($_GET['list_file_group_id'], 'teamleader_id') > dateTime() )
                        || ( getCourse($_GET['list_file_group_id'], 'independent_trainer_id') == get_current_user_id() && getCourse($_GET['list_file_group_id'], 'independent_trainer_id') > dateTime() )
                        || $access == 1 ): ?>
                        <?php foreach($results as $res):?>
                            <tr>
                                <td><?= $res->folder_name ?><?=($res->portfolio == 1) ? " / Портфолио":"" ?></td>
                                <td><span class="badge style-primary-dark">
                                        <a href="/server_file/?download=<?= $res->id ?>&uid=<?= $_POST['fileuserdata'] ?>"><?= $res->filename ?></a>
                                    </span> <?php
                                    if($access == 1 && getCourse($_GET['list_file_group_id'], 'program_id') == 7 && $res->portfolio == 1){
                                        echo "<a href='/assessment/?z=portfolio_action&fileid={$res->id}&res={$res->portfolio}' class='text-primary'>Убрать из портфолио</a>";
                                    }elseif ($access == 1 && getCourse($_GET['list_file_group_id'], 'program_id') == 7 && $res->portfolio == 0){
                                        echo "<a href='/assessment/?z=portfolio_action&fileid={$res->id}&res={$res->portfolio}' class='text-primary'>Добавить в портфолио</a>";
                                    }?>
                                </td>
                                <td><?= formatSizeUnits($res->filesize) ?></td>
                                <td><?= $res->datecreate ?></td>
                            </tr>
                        <?php endforeach;?>
                    <?php else: ?>
                        <?php alertStatus("warning", "<p class='lead'>Истекло время, предоставленное для загрузки файлов</p>") ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
        <?php
    }elseif(isset($_GET['portfolio_list'])){
        if($_GET['lang']) $_SESSION['lang'] = $_GET['lang'];
        if(!isset($_SESSION['lang'])) $_SESSION['lang'] = 'kz';
        include_once(get_stylesheet_directory() . '/assets/lang/' . $_SESSION['lang'] . '.php');
        $access = getAccess(get_current_user_id())->access;
        //printAll($_POST); exit();
        ?>

        <h3><?= USER_FILES ?>: <?php  nameUser($_POST['fileuserdata'],3)?></h3>

        <div class="card">

        <div class="card-body">
        <?php $results = $wpdb->get_results($s=$wpdb->prepare("SELECT a.id, a.datecreate, a.filename, a.filedir, a.filesize, b.name folder_name, c.start_date, c.end_date
                        FROM p_file a
                        LEFT OUTER JOIN p_folder b ON b.id = a.folder
                        LEFT OUTER JOIN p_groups c ON c.id = a.group_id
                        WHERE a.group_id = %d AND a.user_id = %d AND a.portfolio = 1 ORDER BY b.sort_field",$_GET['portfolio_list'], $_POST['fileuserdata'] ));
        ?>
        <table class="table no-margin table-hover" id="dataFile">
            <tbody>
            <tr>
                <th><?= DIR_NAME ?></th>
                <th><?= FILE_NAME ?></th>
                <th><?= FILE_SIZE ?></th>
                <th><?= DOWNLOADED_TIME ?></th>
                <th></th>
            </tr>
                <?php foreach($results as $res):?>
                    <tr>
                        <td><?= $res->folder_name ?>/Портфолио</td>
                        <td><a href="/server_file/?download=<?= $res->id ?>&uid=<?= $_POST['fileuserdata'] ?>" class="text-primary"><?= $res->filename ?></a></td>
                        <td><?= formatSizeUnits($res->filesize) ?></td>
                        <td><?= $res->datecreate ?></td>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        </div>

        </div>
        <?php
    }elseif($_GET['d']){
	    echo "test d";
    }
}