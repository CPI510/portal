<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="<?= bloginfo('template_url') ?>/assets/js/core/countDown.js"></script>
<div class="row">
    <div class="card">
        <?php
        $groupInfo  = groupInfo($_GET['group']);
        $getAccess = getAccess(get_current_user_id());
        $name_var = translateDir($_GET['group']);

        if ($groupInfo->trener_id == get_current_user_id() ){
            $field_name = "trener_id";
            $mail_text = "тренер";
            $time_end = $groupInfo->trener_date;
        }

        if( $groupInfo->expert_id == get_current_user_id() ){
            $field_name = "expert_id";
            $mail_text = "эксперт";
            $time_end = $groupInfo->expert_date;

        }elseif( $groupInfo->moderator_id == get_current_user_id() ){
            $field_name = "moderator_id";
            $mail_text = "модератор";
            $time_end = $groupInfo->moderator_date;

            if ($groupInfo->program_id == 18) {
                alertStatus('danger','Доступ запрещен',true);
                exit();
            }


        }elseif( $groupInfo->teamleader_id == get_current_user_id() ){
            $mail_text = "тимлидер";
            $time_end = $groupInfo->teamleader_date;

            //$field_name = "teamleader_id";
        }elseif(  $getAccess->access == 1 ){
            //$field_name = "";
            $time_end = $groupInfo->end_date;
        }




        $messageText = "
                    На портале ЦПИ была проведена оценка.
                    <br><br>Оценку провел $mail_text: ".nameUser(get_current_user_id(),5)."
                    <br><br>Дата: ".dateTime()."
                    <br><br>Группа: ".$groupInfo->number_group."
                    <br><br>Ссылка: <a href='https://portal.cpi-nis.kz/proforma/?form={$_GET['form']}&group={$_GET['group']}'>https://portal.cpi-nis.kz/proforma/?form={$_GET['form']}&group={$_GET['group']}</a>
                                    ";

        $attachments = ""; //array(WP_CONTENT_DIR . '/uploads/attach.zip');
        $headers = array(
            'From: Портал ЦПИ <portal@cpi.nis.edu.kz>',
            'content-type: text/html',
        );

        ?>
        <div class="card-body">
            <b>Номер группы:</b> <?=$groupInfo->number_group?><br>
            <b>Программа:</b> <?=$groupInfo->p_name?><br>
            <b>Тренер:</b> <?= $groupInfo->surname ?> <?= $groupInfo->name ?> <?= $groupInfo->patronymic ?></a><br>
            <b>Язык обучения:</b> <?= $groupInfo->lang_name_ru ?> <br>
        </div>
    </div>
</div>

<div class="row">

    <div id="select_validation" class=""></div>
    <div class="card">
        <div class="card-body">
            <?php if($_POST): ?>
                <?php

                foreach ($_POST['item'] as  $user_id => $spr_id){
                    foreach ( $spr_id as $spr_id => $section ) {
                        foreach ($section as $section_id => $item){
                            foreach ( $item as $key => $value ){
                                //echo "userid: $user_id, sprid: $spr_id, sectionid: $section_id, value: $value , key: $key  <br>"; exit();

                                if ( $key == 1 ){
                                    //insert
                                    $wpdb->insert( 'p_proforma_user_data', [
                                        'user_id' => $user_id,
                                        'proforma_id' => $_GET['form'],
                                        'group_id' => $_GET['group'],
                                        'proforma_spr_id' => $spr_id,
                                        'data_value' => $value,
                                        $field_name => get_current_user_id(),
                                    ], [ '%d', '%d','%d', '%d','%d', '%d' ] );

                                    if ($value == 3){
                                        $final["insert"][$user_id]['plagiarism'] = 3;

                                        if($section_id == 1) $final["insert"][$user_id]['plagiarism_section_a'] = 3;
                                        elseif($section_id == 2)  $final["insert"][$user_id]['plagiarism_section_b'] = 3;
                                        else $final["insert"][$user_id]['plagiarism_section_c'] = 3;

                                    }elseif($value == 0){
                                        $final["insert"][$user_id]['none'] = 1;

                                        if($section_id == 1) $final["insert"][$user_id]['none_section_a'] = 1;
                                        elseif ($section_id == 2)  $final["insert"][$user_id]['none_section_b'] = 1;
                                        else $final["insert"][$user_id]['none_section_c'] = 1;

                                    }elseif($value == -1){
                                        $final["insert"][$user_id]['absent'] = 1;

                                        if($section_id == 1) $final["insert"][$user_id]['absent_section_a'] = 1;
                                        elseif ($section_id == 2)  $final["insert"][$user_id]['absent_section_b'] = 1;
                                        else $final["insert"][$user_id]['absent_section_c'] = 1;

                                    }else{
                                        if($section_id == 1){
                                            $final["insert"][$user_id]['section_a'] += $value;
                                        }elseif ($section_id == 2){
                                            $final["insert"][$user_id]['section_b'] += $value;
                                        }elseif ($section_id == 3){
                                            $final["insert"][$user_id]['section_c'] += $value;
                                        }
                                        $final["insert"][$user_id]['total'] += $value;
                                    }

                                } elseif ( $key == 2 ){

                                    //echo " $user_id: $spr_id: $key => $value<br>";
                                    //update
                                    $wpdb->update( 'p_proforma_user_data',
                                        [ 'data_value' => $value, 'datetime_update' => dateTime() ],
                                        [ 'user_id' => $user_id, 'proforma_id' => $_GET['form'], 'group_id' => $_GET['group'], 'proforma_spr_id' => $spr_id, $field_name => get_current_user_id() ],
                                        [ '%d', '%s' ],
                                        [ '%d', '%d', '%d', '%d', '%d' ]
                                    );

                                    if ($value == 3){
                                        $final["update"][$user_id]['plagiarism'] = 3;
                                        if($section_id == 1) $final["update"][$user_id]['plagiarism_section_a'] = 3;
                                        elseif($section_id == 2) $final["update"][$user_id]['plagiarism_section_b'] = 3;
                                        else $final["update"][$user_id]['plagiarism_section_c'] = 3;
                                    }elseif($value == 0){
                                        $final["update"][$user_id]['none'] = 1;
                                        if($section_id == 1) $final["update"][$user_id]['none_section_a'] = 1;
                                        elseif($section_id == 2) $final["update"][$user_id]['none_section_b'] = 1;
                                        else $final["update"][$user_id]['none_section_c'] = 1;
                                    }elseif($value == -1){
                                        $final["update"][$user_id]['absent'] = 1;
                                        if($section_id == 1) $final["update"][$user_id]['absent_section_a'] = 1;
                                        elseif ($section_id == 2)  $final["update"][$user_id]['absent_section_b'] = 1;
                                        else $final["update"][$user_id]['absent_section_c'] = 1;
                                    }else{
                                        if($section_id == 1){
                                            $final["update"][$user_id]['section_a'] += $value;
                                        }elseif ($section_id == 2) {
                                            $final["update"][$user_id]['section_b'] += $value;
                                        }elseif ($section_id == 3){
                                            $final["update"][$user_id]['section_c'] += $value;
                                        }
                                        $final["update"][$user_id]['total'] += $value;

                                    }

                                } else { echo "Нет данных!"; }
                            }
                        }
                    }
                }

                //printAll($_POST);
                //printAll($final);



                foreach ($final as $action => $total) {
                    foreach ($total as $uid => $actions) {
                        //echo "$action : $uid:  decision: $actions[decision], total : $actions[total], plagiarism: $actions[plagiarism], none: $actions[none],
                        //section_a: $actions[section_a], section_b: $actions[section_b],  sections_c: $actions[section_c]<br>";

                        if ($actions['plagiarism'] == 3 &&  $actions['plagiarism_section_a'] == 3 ) {
                            $final = "Плагиат";
                            $decision = "Незачет";
                            $section_a = "Плагиат";
                            $section_b = $actions['section_b'];
                            $section_c = $actions['section_c'];
                        } elseif ($actions['plagiarism'] == 3 &&  $actions['plagiarism_section_b'] == 3) {
                            $final = "Плагиат";
                            $decision = "Незачет";
                            $section_a = $actions['section_a'];
                            $section_b = "Плагиат";
                            $section_c = $actions['section_c'];
                        } elseif ($actions['plagiarism'] == 3 &&  $actions['plagiarism_section_c'] == 3) {
                            $final = "Плагиат";
                            $decision = "Незачет";
                            $section_a = $actions['section_a'];
                            $section_b = $actions['section_b'];
                            $section_c = "Плагиат";
                        } elseif ( $actions['absent'] == 1 && $actions['absent_section_a'] == 1) {
                            $final = 0;
                            $decision = 'Неявка';
                            $section_a = 'Неявка';
                            $section_b = 'Неявка';
                            $section_c = 'Неявка';
                        } elseif ( $actions['absent'] == 1 && $actions['absent_section_b'] == 1) {
                            $final = 0;
                            $decision = 'Неявка';
                            $section_a = 'Неявка';
                            $section_b = 'Неявка';
                            $section_c = 'Неявка';
                        } elseif ( $actions['absent'] == 1 && $actions['absent_section_c'] == 1) {
                            $final = 0;
                            $decision = 'Неявка';
                            $section_a = 'Неявка';
                            $section_b = 'Неявка';
                            $section_c = 'Неявка';
                        } elseif ( $actions['none'] == 1 && $actions['none_section_a'] == 1) {
                            $final = 0;
                            $decision = 'Незачет';
                            $section_a = 0;
                            $section_b = $actions['section_b'];
                            $section_c = $actions['section_c'];
                        } elseif ( $actions['none'] == 1 && $actions['none_section_b'] == 1) {
                            $final = 0;
                            $decision = 'Незачет';
                            $section_a = $actions['section_a'];
                            $section_b = 0;
                            $section_c = $actions['section_c'];
                        } elseif ( $actions['none'] == 1 && $actions['none_section_c'] == 1) {
                            $final = 0;
                            $decision = 'Незачет';
                            $section_a = $actions['section_a'];
                            $section_b = $actions['section_b'];
                            $section_c = 0;
                        }elseif($actions['total'] < 10) {
                            $final = $actions['total'];
                            $decision = 'Незачет';
                            $section_a = $actions['section_a'];
                            $section_b = $actions['section_b'];
                            $section_c = $actions['section_c'];
                        } else {
                            $final = $actions['total'];
                            $decision = 'Зачет';
                            $section_a = $actions['section_a'];
                            $section_b = $actions['section_b'];
                            $section_c = $actions['section_c'];
                        }


                        if($action == "update") {
                            $result = $wpdb->update( 'p_proforma_user_result',
                                [ 'section_c' => $section_c, 'section_b' => $section_b, 'section_a' => $section_a, 'total' => $final, 'decision' => $decision, 'date_update' => current_time('mysql', 0) ],
                                [ 'user_id' => $uid, 'proforma_id' => $_GET['form'], 'group_id' => $_GET['group'], $field_name => get_current_user_id() ],
                                [ '%s', '%s', '%s', '%s', '%s', '%s'],
                                [ '%d', '%d', '%d', '%d' ]
                            );

                        } else if($action == "insert") {
                            $result = $wpdb->insert('p_proforma_user_result', [
                                'user_id' => $uid,
                                'proforma_id' => $_GET['form'],
                                'group_id' => $_GET['group'],
                                'total' => $final,
                                'decision' => $decision,
                                'section_a' => $section_a,
                                'section_b' => $section_b,
                                'section_c' => $section_c,
                                $field_name => get_current_user_id()
                            ], ['%d', '%d','%d', '%d', '%s', '%s', '%s', '%s', '%d']);

//                           printAll('user_id ' . $uid);
//                           printAll('proforma_id ' . $_GET['form'],);
//                           printAll('group_id ' . $_GET['group'],);
//                           printAll('total ' . $final,);
//                           printAll('decision ' . $decision,);
//                           printAll('section_a ' . $section_a);
//                           printAll('section_b ' . $section_b);
//                           printAll('section_c ' . $section_c);

                        }
                    }
                }

                wp_mail(nameUser($groupInfo->admin_id,6), 'Портал ЦПИ: проведена оценка', $messageText, $headers, $attachments);
                //DELETE FROM `p_proforma_user_data` WHERE `user_id` = 10730 or `user_id` = 10729 or `user_id` = 10728
                echo "<meta http-equiv='refresh' content='0;url=/proforma/?form=$_GET[form]&group=$_GET[group]' />"; exit();


                ?>
            <?php else: ?>
                <form novalidate id="form" method="post" action="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>">
                    <table class="table table-bordered">
                        <tr>
                            <th rowspan="2">№</th>
                            <th rowspan="2">ФИО слушателей</th>

                            <th colspan="3"><div align="center">Цели</div></th>
                            <th colspan="3"><div align="center">Методы обучения</div></th>
                            <th colspan="3"><div align="center">Задания</div></th>


                            <th rowspan="2">
                                <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);">Итог</span>
                            </th>

                            <th rowspan="2">
                                <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);">Решение</span>
                            </th>
                        </tr>
                        <tr>
                            <?php $proformaSpr = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_spr WHERE proforma_id = %d", $_GET['form'])); ?>
                            <?php foreach ($proformaSpr as $data): ?>
                                <th>
                                    <span> <?= 'K'. ++$i;  ?>
                                        <i class="fa fa-info-circle fa-fw text-info" data-toggle="tooltip" data-placement="right" data-original-title="<?= $data->$name_var ?>" style="cursor: pointer"></i>
                                    </span>
                                </th>
                                <?php endforeach; ?>
                        </tr>
                        <?php if($getAccess->access == 1): ?>
                            <tr>
                                <td colspan="22">
                                    <div class="btn-group dropup align-items-end" >
                                        <button type="button" class="btn ink-reaction btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <?php if ($_GET['filtr'] == 1){
                                                $filtr_text = "(Зачет)";
                                                $sql_filtr = "AND p.decision = 'Зачет'";
                                            } elseif ($_GET['filtr'] == 2){
                                                $filtr_text = "(Незачет)";
                                                $sql_filtr = "AND p.decision = 'Незачет'";
                                            } elseif ($_GET['filtr'] == 3){
                                                $filtr_text = "(Плагиат)";
                                                $sql_filtr = "AND (p.section_a = 'Плагиат' OR p.section_b = 'Плагиат' or p.section_c = 'Плагиат')";
                                            } else {
                                                $sql_filtr = "";
                                            } ?>
                                            Фильтр <?= $filtr_text ?><i class="fa fa-caret-up text-default-light"></i>
                                        </button>
                                        <ul class="dropdown-menu animation-expand" role="menu">
                                            <li><?php if($_GET['filtr'] == 1): ?><a href="" class="btn btn-info btn-xs active">Зачет</a><?php else: ?><a href="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&filtr=1" class="btn btn-default btn-xs">Зачет</a><?php endif; ?></li>
                                            <li><?php if($_GET['filtr'] == 2): ?><a href="" class="btn btn-info btn-xs active">Незачет</a><?php else: ?><a href="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&filtr=2" class="btn btn-default btn-xs">Незачет</a><?php endif; ?></li>
                                            <li><?php if($_GET['filtr'] == 3): ?><a href="" class="btn btn-info btn-xs active">Плагиат</a><?php else: ?><a href="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&filtr=3" class="btn btn-default btn-xs">Плагиат</a><?php endif; ?></li>
                                            <li><a href="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&filtr=reject" class="btn btn-default btn-xs">Сбросить</a></li>
                                            <li class="divider"></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php for($y = 1; $y <= 3; $y++): // Это для отображение данныех тренера эксперта и модератора ?>
                             <?php if($y == 1){ //Все для тренера
                                $link_choice = "&trener_id={$groupInfo->trener_id}";
                                $fiels_text = "AND p.trener_id = %d";
                                $fiels_id = $groupInfo->trener_id;
                                $part_text = "тренера";
                                if($groupInfo->trener_id == get_current_user_id()) {
                                    $y = 4;
                                }elseif($groupInfo->expert_id == get_current_user_id()){
                                    continue;   // Станет 2
                                } elseif ($groupInfo->moderator_id == get_current_user_id()){
                                    $y = 2;
                                    continue;   //Станет 3
                                }
                            } else if ($y == 2) {
                                $link_choice = "&expert_id={$groupInfo->expert_id}";
                                $fiels_text = "AND p.expert_id = %d";
                                $fiels_id = $groupInfo->expert_id;
                                $part_text = "эксперта";
                                if($groupInfo->expert_id == get_current_user_id()) {
                                    $y = 4;
                                }elseif($groupInfo->moderator_id == get_current_user_id()){
                                    continue;
                                }
                            }
                            else{ //Все для модератора
                                $link_choice = "&moderator_id={$groupInfo->moderator_id}";
                                $fiels_text = "AND p.moderator_id = %d";
                                $fiels_id = $groupInfo->moderator_id;
                                $part_text = "модератора";
                            } ?>
                            <tr>
                                <th colspan="22">Оценка <?= $part_text ?></th>
                            </tr>
                            <?php
                            if($groupInfo->teamleader_id == get_current_user_id()){ // Показываем данные для тимлидера
                                $usersField = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b, p.trener_id, p.expert_id, p.moderator_id, p.id proforma_result_id
                                    FROM p_groups_users g
                                    LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                    LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user 
                                    WHERE g.id_group = %d AND p.group_id = %d $fiels_text AND p.total < 20", $_GET['group'], $_GET['group'], $fiels_id  ));

                                if($_GET['teamleader_choice'] == 1
                                    && $_GET['form']
                                    && $_GET['group']
                                    && $_GET['user_id']
                                    && $_GET['proforma_result_id']
                                ){
                                    if($_GET['trener_id']) $messageText .= "<br><br> Выбрана оценка тренера, пользователя ".nameUser($_GET['user_id'],5);
                                    if($_GET['expert_id']) $messageText .= "<br><br> Выбрана оценка эксперта, пользователя ".nameUser($_GET['user_id'],5);
                                    if($_GET['moderator_id']) $messageText .= "<br><br> Выбрана оценка модератора, пользователя ".nameUser($_GET['user_id'],5);

                                    //printAll($_GET);
                                    if($wpdb->insert('p_proforma_teamleader_choice', [
                                        'proforma_id' => $_GET['form'],
                                        'group_id' => $_GET['group'],
                                        'user_id' => $_GET['user_id'],
                                        'teamleader_id' => get_current_user_id(),
                                        'expert_id' => $_GET['expert_id'],
                                        'moderator_id' => $_GET['moderator_id'],
                                        'proforma_result_id' => $_GET['proforma_result_id']
                                    ], ['%d','%d','%d','%d','%d','%d','%d'])){
                                        wp_mail(nameUser($groupInfo->admin_id,6), 'Портал ЦПИ: Тимлидер выполнил оценку', $messageText, $headers, $attachments);
                                        echo "<meta http-equiv='refresh' content='0;url=/proforma/?form=$_GET[form]&group=$_GET[group]' />"; exit();
                                    }elseif($wpdb->update( 'p_proforma_teamleader_choice',
                                        [ 'moderator_id' => $_GET['moderator_id'],'expert_id' => $_GET['expert_id'], 'date_update' => dateTime(), 'proforma_result_id' => $_GET['proforma_result_id'] ],
                                        [ 'proforma_id' => $_GET['form'], 'group_id' => $_GET['group'], 'user_id' => $_GET['user_id'], 'teamleader_id' => get_current_user_id() ],
                                        [ '%d', '%d', '%s', '%d' ],
                                        [ '%d', '%d', '%d', '%d' ]
                                    )){
                                        wp_mail(nameUser($groupInfo->admin_id,6), 'Портал ЦПИ: Тимлидер выполнил оценку', $messageText, $headers, $attachments);
                                        echo "<meta http-equiv='refresh' content='0;url=/proforma/?form=$_GET[form]&group=$_GET[group]' />"; exit();
                                    }
                                }
                            }else{
                                //Все участники группы
                                $allUsersOfGroup = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email
                                        FROM p_groups_users g
                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                        WHERE g.id_group = %d", $_GET['group']));

                                // Участники групп с оценками
                                $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b, p.trener_id, p.expert_id, p.moderator_id, p.id proforma_result_id
                                    FROM p_groups_users g
                                    LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
                                    LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user
                                    WHERE g.id_group = %d AND p.group_id = %d $fiels_text $sql_filtr", $_GET['group'], $_GET['group'], $fiels_id)); // Это нужно для отображения пользователей этой группы

                                // Проверить условие, было ИЛИ, поменял на И, потом проверить
                                if ( !$usersField && $groupInfo->trener_id == get_current_user_id() && (count($allUsersOfGroup) != count($usersField)) ){        // Если тренер еще не поставил оценки
                                    $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email
                                        FROM p_groups_users g
                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                        WHERE g.id_group = %d", $_GET['group']));
                                }
                                // TODO Старая версия, позже посмотреть почему так написл код
//                                elseif(!$usersField && $groupInfo->expert_id == get_current_user_id()){    // Если эксперт еще не поставил оценки
//                                    $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b,  p.trener_id, p.expert_id, p.moderator_id, p.id proforma_result_id
//                                        FROM p_groups_users g
//                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
//                                        LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user
//                                        WHERE g.id_group = %d AND p.group_id = %d AND trener_id = %d AND p.total < 20", $_GET['group'], $_GET['group'], $groupInfo->trener_id));
//                                }
                                elseif (!$usersField && $groupInfo->expert_id == get_current_user_id()){
                                    $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email
                                        FROM p_groups_users g
                                        LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                        WHERE g.id_group = %d", $_GET['group']));
                                }
                                elseif(!$usersField && $groupInfo->moderator_id == get_current_user_id()){ // Если модератор еще не поставил оценки, берется данные эксперта
                                    $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.trener_id, p.expert_id, p.moderator_id
                                    FROM p_groups_users g
                                    LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                    LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user 
                                    WHERE g.id_group = %d AND p.group_id = %d AND expert_id = %d AND p.decision = 'Незачет'", $_GET['group'], $_GET['group'], $groupInfo->expert_id));

                                }
                            }

                            //echo $s;
                            //printAll($usersField);

                            ?>

                            <?php foreach ($usersField as $user): ?>
                                <?php
                                    // Показывает поле решение если оно есть
                                    $user->decision = getDecisionById($user->id_user, $fiels_text,$fiels_id);
                                ?>

                                <tr>
                                    <td><?= ++$counter; ?></td>
                                    <td>
<!--                                        Временно убрал, не нужный функционал скорее всего-->
<!--                                        <div class="btn-group">-->
<!--                                            <button type="button" class="btn ink-reaction btn-icon-toggle btn-primary" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-chevron-down"></i></button>-->
<!--                                            <ul class="dropdown-menu" role="menu">-->
<!--                                                <li class="divider"></li>-->
<!--                                                <li><a href="/proforma/?form=--><?php //= $_GET['form'] ?><!--r&group=--><?php //= $_GET['group'] ?><!--&uid=--><?php //= $user->user_id ?><!--" ><i class="md md-exit-to-app text-info"></i> Реккоммендация</a></li>-->
<!--                                                <li class="divider"></li>-->
<!--                                                <li><a href="/export_to_word/?form=--><?php //= $_GET['form'] ?><!--a&group=--><?php //= $_GET['group'] ?><!--&uid=--><?php //= $user->user_id ?><!--" ><i class="md md-exit-to-app text-info"></i> Обоснование</a></li>-->
<!--                                                <li class="divider"></li>-->
<!--                                                <li><a href="#" id="fileu" data-id="--><?php //= $user->user_id ?><!--" data-toggle="modal" data-target="#Modal">-->
<!--                                                        <i class="md md-exit-to-app text-info"></i>-->
<!--                                                        Файлы пользователя --><?php // echo $num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM p_file WHERE group_id = %d AND user_id = %d", $_GET['group'], $user->user_id)) ?>
<!--                                                    </a>-->
<!--                                                </li>-->
<!--                                            </ul>-->
<!--                                        </div>-->
                                        <?= $user->surname ?> <?= $user->$name_var ?> <?= $user->patronymic // ФИО Участика ?>
                                        <?php
                                        $choiceProforma = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_proforma_teamleader_choice WHERE proforma_result_id = %d",$user->proforma_result_id));
                                        if ( $choiceProforma->proforma_result_id == $user->proforma_result_id && ( $getAccess->access == 1 || $groupInfo->teamleader_id == get_current_user_id() ) ){ // Проверяем если есть ли запись в базе с таким выбором и отображаем для тимлидера и админа
                                            echo "<span class='badge'>Эта оценка была выбрана тимлидером!</span>";
                                        }elseif($groupInfo->teamleader_id == get_current_user_id()){
                                            echo "<a href='/proforma/?form={$_GET['form']}&group={$_GET['group']}&user_id={$user->user_id}{$link_choice}&teamleader_choice=1&proforma_result_id={$user->proforma_result_id}' 
                                                class='btn btn-success btn-xs' id='timer'>Выбрать оценку</a>";
                                        } ?>
                                    </td>
                                    <?php
                                        //Показываются оценки если они есть
                                        $proformaDataUser = $wpdb->get_results($s=$wpdb->prepare("SELECT p.id, p.user_id, p.proforma_id, p.proforma_spr_id, p.group_id, p.datetime, p.data_value, p.datetime_update, p.trener_id, p.expert_id, p.moderator_id 
                                                FROM p_proforma_user_data p 
                                                WHERE p.user_id= %d AND p.proforma_id = %d AND p.group_id =%d $fiels_text"
                                            , $user->user_id, $_GET['form'], $_GET['group'], $fiels_id ));

//                                        // Если не выставил оценки
//                                        if(!$proformaDataUser){
//                                            $proformaDataUser = $wpdb->get_results($s=$wpdb->prepare("SELECT p.id, p.user_id, p.proforma_id, p.proforma_spr_id, p.group_id, p.datetime, p.data_value, p.datetime_update, p.trener_id, p.expert_id, p.moderator_id
//                                                FROM p_proforma_user_data p
//                                                WHERE p.user_id= %d AND p.proforma_id = %d AND p.group_id =%d"
//                                                , $user->user_id, $_GET['form'], $_GET['group'] ));
//                                            $action_moderator = 1;
//
//                                        }




                                    ?>


                                    <?php $q=0; ?>

                                    <?php foreach ($proformaSpr as $data): ?>
                                        <?php  //echo "{$proformaDataUser[$q]->proforma_spr_id} == {$data->id}<br>";
                                        if ($proformaDataUser[$q]->proforma_spr_id == $data->id) {
                                            $action = ($action_moderator == 1) ? "1" : "2";
                                        } else {
                                            $action =  "1";
                                        }

                                        //$key = array_search($user->user_id, array_column($finalData, 'user_id'));
                                        ?>
                                        <td>

                                            <?php if($groupInfo->trener_id == get_current_user_id()  || $groupInfo->expert_id == get_current_user_id() || $groupInfo->moderator_id == get_current_user_id()): ?>
                                                <select name="item[<?= $user->user_id ?>][<?= $data->id ?>][<?= $data->section_id ?>][<?= $action ?>]" class="form-control" required>
                                                    <option></option>
                                                    <?php if(($proformaDataUser[$q]->data_value == 3)){
                                                        //Прошлое условие которое показывало плагиатом целую секцию if(($proformaDataUser[$q]->data_value == 3 || ($data->section_id == 1 && $user->section_a == "Плагиат") || ($data->section_id == 2 && $user->section_b == "Плагиат")  || ($data->section_id == 3 && $user->section_c == "Плагиат")))
                                                        echo '<option value="0">0</option>
                                                          <option value="1">1</option>
                                                          <option value="2">2</option>
                                                          <option value="3" selected>Плагиат</option>  
                                                          <option value="-1">Неявка</option>  
                                                          ';
                                                    }elseif(($proformaDataUser[$q]->data_value == -1 || ($data->section_id == 1 && $user->section_a == "Неявка") || ($data->section_id == 2 && $user->section_b == "Неявка")  || ($data->section_id == 3 && $user->section_c == "Неявка"))){
                                                        echo '<option value="0">0</option>
                                                          <option value="1">1</option>
                                                          <option value="2">2</option>
                                                          <option value="3">Плагиат</option>  
                                                          <option value="-1" selected>Неявка</option>  
                                                          ';
                                                    } else{

                                                        ?>
                                                        <option value="0" <?= (isset($proformaDataUser[$q]->data_value) && $proformaDataUser[$q]->data_value == 0) ? "selected" : "" ?>>0</option>
                                                        <option value="1" <?= ($proformaDataUser[$q]->data_value == 1) ? "selected" : "" ?>>1</option>
                                                        <option value="2" <?= ($proformaDataUser[$q]->data_value == 2) ? "selected" : "" ?>>2</option>
                                                        <option value="3" >Плагиат</option>
                                                        <option value="-1" >Неявка</option>
                                                    <?php } ?>
                                                </select>
                                            <?php else:?>
                                                <?php
                                                    if ($proformaDataUser[$q]->data_value == 3) {
                                                        echo "Плагиат";
                                                    } elseif ($proformaDataUser[$q]->data_value == -1){
                                                        echo "Неявка";
                                                    } else {
                                                        echo $proformaDataUser[$q]->data_value;
                                                    }

                                                ?>
<!--                                                --><?php //= ($proformaDataUser[$q]->data_value == 3) ? "Плагиат" : $proformaDataUser[$q]->data_value ?>
                                            <?php endif; ?>

                                        </td>
                                        <?php $q++; ?>
                                    <?php endforeach; ?>
                                    <td><?= $user->total ?></td>
                                    <td><?= $user->decision ?></td>

                                </tr>
                            <?php  endforeach; ?>
                        <?php endfor; ?>

                    </table>
                    <?php if($groupInfo->teamleader_id == get_current_user_id() || $getAccess->access == 1 ): ?>
                    <?php else: ?>
                        <div  id="timer" class="btn btn-success">Сохранить</div>

                    <?php endif; ?>
                    <a href="/export_to_word/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>" class="btn btn-primary">Экспортировать проформу</a>
                </form>
            <?php endif; ?>

        </div>
    </div>
</div>


<script>

    const timeBtn = document.querySelectorAll("#timer");
    // const timeBtn2 = document.querySelector("#timer2");

    select_error_div = document.getElementById('select_validation');
    message_div = document.createElement('div');
    message_div.classList.add("alert");
    message_div.classList.add("alert-danger");
    message_txt = document.createElement('strong');
    message_txt.innerText = 'Заполните все поля!'

    message_div.appendChild(message_txt);
    select_error_div.appendChild(message_div);
    select_error_div.hidden = true;

    for (var i = 0; i < timeBtn.length ; i++) {
        timeBtn[i].addEventListener('click', save_wait);
    }


    // Функция валидации, чтобы не отправлялись пустые данные в форму
    function select_validator(){
        var selects = $(":input[name^='item']").css("color","");

        isFind = false;

        for (var i = 0; i < selects.length ; i++) {
            if (selects[i].value == "") {
                isFind = true;
                selects[i].style.color = 'red';
            }
        }

        if (isFind) {
            select_error_div.hidden = false;
            return false;
        } else {
            select_error_div.hidden = true;
            return true;
        }

    }

    function save_wait(event){

        if (select_validator()) {
            this.setAttribute("disabled", "disabled");
            const spinner = document.createElement('img');
            spinner.src = message.loading;
            this.append(spinner);
            document.forms["form"].submit();
            setTimeout(function () {
                this.removeAttribute("disabled");
                spinner.parentNode.removeChild(spinner);
            }, 10 * 1000);
        }

    }

    // timeBtn2.addEventListener("click", ()=>{
    //     timeBtn2.setAttribute("disabled", "disabled");
    // const spinner = document.createElement('img');
    // spinner.src = message.loading;
    // timeBtn2.append(spinner);
    // document.forms["form"].submit();
    // setTimeout(function () {
    //     timeBtn2.removeAttribute("disabled");
    //     spinner.parentNode.removeChild(spinner);
    // }, 10 * 1000);
    //
    // });


    const allData = {};
    const fileu = document.querySelectorAll("#fileu"), boxdiv = document.querySelector('.box'), close = document.querySelector("#close");
    const message = {
        loading: `${document.location.origin}/wp-content/themes/portalcpi/assets/img/spinner.svg`,
        success: "",
        failure: "Что-то пошло не так, попробуйте зайти позднее!"
    };
    const statusMessage = document.createElement('div');

    statusMessage.classList.add('status');

    boxdiv.append(statusMessage);

    for (var i = 0; i < fileu.length ; i++) {
        //let dataid = fileu[i].getAttribute('data-id');
        fileu[i].addEventListener('click', saveData);
        //console.log(fileu[i].getAttribute('data-id'));
    }
    function saveData(event) {
        statusMessage.innerHTML = "";
        var target = event.currentTarget;
        var parent = target.parentElement.nodeName;
        //console.log(target.getAttribute('data-id'));
        allData.fileuserdata = target.getAttribute('data-id');

        const request = new XMLHttpRequest();
        request.open('POST', `${document.location.origin}/portal_server/?list_file_group_id=<?= $_GET['group'] ?>`);

        request.setRequestHeader('Content-type', 'application/json');
        const json = JSON.stringify(allData);

        request.send(json);

        const spinner = document.createElement('img');
        spinner.src = message.loading;
        boxdiv.append(spinner);

        request.addEventListener('load', () => {
            if (request.status === 200){
            //console.log(request.response);

            // statusMessage.textContent = message.success;
            statusMessage.innerHTML = request.response;
            spinner.remove();

            /*if(dataform == 'add') formreg.reset();
            else {
                //location.href = window.location.href;
            }*/


        } else {
            spinner.remove();
            statusMessage.textContent = message.failure;
        }
    });
    }

    close.addEventListener("click", () => {
        statusMessage.innerHTML = "";
    });

</script>