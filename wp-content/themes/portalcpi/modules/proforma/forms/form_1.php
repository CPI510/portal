<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="<?= bloginfo('template_url') ?>/assets/js/core/countDown.js"></script>
<div class="row">
    <div class="card">
        <?php
        $groupInfo  = groupInfo($_GET['group']);
        $getAccess = getAccess(get_current_user_id());
        $name_var = translateDir($_GET['group']);

        if( $groupInfo->expert_id == get_current_user_id() ){
            $field_name = "expert_id";
            $mail_text = "эксперт";
            $time_end = $groupInfo->expert_date;

        }elseif( $groupInfo->moderator_id == get_current_user_id() ){
            $field_name = "moderator_id";
            $mail_text = "модератор";
            $time_end = $groupInfo->moderator_date;

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
            <b>Эксперт:</b> <?= $groupInfo->expert_surname ?> <?= $groupInfo->expert_name ?> <?= $groupInfo->expert_patronymic ?><br>
            <b>Модератор:</b> <?= $groupInfo->moderator_surname ?> <?= $groupInfo->moderator_name ?> <?= $groupInfo->moderator_patronymic ?><br>
            <b>Тимлидер:</b> <?= $groupInfo->teamleader_surname ?> <?= $groupInfo->teamleader_name ?> <?= $groupInfo->teamleader_patronymic ?><br>
            <h3 class="text-primary-dark">Осталось времени до окончания работы: <span id="display"></span></h3>
            <script>
                const display<?= $q ?> = document.querySelector('#display<?= $q ?>');
                countDown('<?= $time_end ?>', display<?= $q ?>);
            </script>

        </div>
    </div>
</div>
<div class="row">
    <div class="card">
        <div class="card-body">
            <?php if($_POST): ?>
                <?php  //echo date("H:i:s d:m:y");//echo current_time( 'mysql', 0 );

                foreach ($_POST['item'] as  $user_id => $spr_id){
                    foreach ( $spr_id as $spr_id => $section ) {
                        foreach ($section as $section_id => $item){
                            foreach ( $item as $key => $value ){
//                                echo "userid: $user_id, sprid: $spr_id, sectionid: $section_id, value: $value  <br>";
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
                                        else $final["insert"][$user_id]['plagiarism_section_b'] = 3;

                                    }elseif($value == 0){
                                        $final["insert"][$user_id]['none'] = 1;

                                        if($section_id == 1) $final["insert"][$user_id]['none_section_a'] = 1;
                                        else $final["insert"][$user_id]['none_section_b'] = 1;

                                    }else{
                                        if($section_id == 1){
                                            $final["insert"][$user_id]['section_a'] += $value;
                                        }else{
                                            $final["insert"][$user_id]['section_b'] += $value;
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
                                        else $final["update"][$user_id]['plagiarism_section_b'] = 3;
                                    }elseif($value == 0){
                                        $final["update"][$user_id]['none'] = 1;
                                        if($section_id == 1) $final["update"][$user_id]['none_section_a'] = 1;
                                        else $final["update"][$user_id]['none_section_b'] = 1;
                                    }else{
                                        if($section_id == 1){
                                            $final["update"][$user_id]['section_a'] += $value;
                                        }else{
                                            $final["update"][$user_id]['section_b'] += $value;
                                        }
                                        $final["update"][$user_id]['total'] += $value;
                                    }

                                } else { echo "Нет данных!"; }
                            }
                        }
                    }
                }
                //printAll($_POST);
//                printAll($final);

                foreach ($final as $action => $total) {
                    foreach ($total as $uid => $actions) {
//                        echo "$action : $uid:  decision: $actions[decision], total : $actions[total], plagiarism: $actions[plagiarism], none: $actions[none],
//                        section_a: $actions[section_a], section_b: $actions[section_b]<br>";
                        if ($actions['plagiarism'] == 3 &&  $actions['plagiarism_section_a'] == 3) {
                            $final = 0;
                            $decision = "Незачет";
                            $section_a = "Плагиат";
                            $section_b = $actions['section_b'];
                        } elseif ($actions['plagiarism'] == 3 &&  $actions['plagiarism_section_b'] == 3) {
                            $final = 0;
                            $decision = "Незачет";
                            $section_a = $actions['section_a'];
                            $section_b = "Плагиат";
                        } elseif ( $actions['none'] == 1 && $actions['none_section_a'] == 1) {
                            $final = 0;
                            $decision = 'Незачет';
                            $section_a = 0;
                            $section_b = $actions['section_b'];
                        } elseif ( $actions['none'] == 1 && $actions['none_section_b'] == 1) {
                            $final = 0;
                            $decision = 'Незачет';
                            $section_a = $actions['section_a'];
                            $section_b = 0;
                        } elseif($actions['total'] < 20) {
                            $final = $actions['total'];
                            $decision = 'Незачет';
                            $section_a = $actions['section_a'];
                            $section_b = $actions['section_b'];
                        } else {
                            $final = $actions['total'];
                            $decision = 'Зачет';
                            $section_a = $actions['section_a'];
                            $section_b = $actions['section_b'];
                        }

                        if($action == "update") {
                            $wpdb->update( 'p_proforma_user_result',
                                [ 'section_b' => $section_b, 'section_a' => $section_a, 'total' => $final, 'decision' => $decision, 'date_update' => current_time('mysql', 0) ],
                                [ 'user_id' => $uid, 'proforma_id' => $_GET['form'], 'group_id' => $_GET['group'], $field_name => get_current_user_id() ],
                                [ '%s', '%s', '%s', '%s', '%s' ],
                                [ '%d', '%d', '%d', '%d' ]
                            );
                        } else if($action == "insert") {
                            $wpdb->insert('p_proforma_user_result', [
                                'user_id' => $uid,
                                'proforma_id' => $_GET['form'],
                                'group_id' => $_GET['group'],
                                'total' => $final,
                                'decision' => $decision,
                                'section_a' => $section_a,
                                'section_b' => $section_b,
                                $field_name => get_current_user_id()
                            ], ['%d', '%d','%d', '%s', '%s', '%s', '%s', '%d']);
                        }
                    }
                }

                wp_mail(nameUser($groupInfo->admin_id,6), 'Портал ЦПИ: проведена оценка', $messageText, $headers, $attachments);

                echo "<meta http-equiv='refresh' content='0;url=/proforma/?form=$_GET[form]&group=$_GET[group]' />"; exit();
                ?>
            <?php else: ?>
                <form id="form" method="post" action="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>">
                    <table class="table table-bordered">
                        <tr>
                            <th rowspan="2">№</th>
                            <th rowspan="2">ФИО слушателей</th>
                            <?php $proformaSpr = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_spr WHERE proforma_id = %d", $_GET['form'])); ?>
                            <?php foreach ($proformaSpr as $data): ?>
                                <th>
                                    <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);"><?= $data->$name_var ?></span>
                                </th>
                                <?php if($data->section_id == 1) ++$d; else ++$r; endforeach; ?>
                            <th rowspan="2">
                                <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);">Итого</span>
                            </th>

                            <th rowspan="2">
                                <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);">Решение</span>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="<?= $d ?>"><div align="center"><?= PROFORMA[6] ?></div></th>
                            <th colspan="<?= $r ?>"><div align="center"><?= PROFORMA[7] ?></div></th>
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
                                                $sql_filtr = "AND (p.section_a = 'Плагиат' OR p.section_b = 'Плагиат')";
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

                        <?php for($y = 1; $y <= 2; $y++): // Это для отображение данныех эксперта и модератора ?>
                        <?php if($y == 1){ //Все для эксперта
                            $link_choice = "&expert_id={$groupInfo->expert_id}";
                                $fiels_text = "AND p.expert_id = %d";
                                $fiels_id = $groupInfo->expert_id;
                                $part_text = "эксперта";
                                if($groupInfo->expert_id == get_current_user_id()) {
                                    $y = 3;
                                }elseif($groupInfo->moderator_id == get_current_user_id()){
                                    continue;
                                }
                            }else{ //Все для модератора
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
                                $usersField = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b, p.expert_id, p.moderator_id, p.id proforma_result_id
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
                                $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b, p.expert_id, p.moderator_id, p.id proforma_result_id
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user 
                                WHERE g.id_group = %d AND p.group_id = %d $fiels_text $sql_filtr", $_GET['group'], $_GET['group'], $fiels_id)); // Это нужно для отображения пользователей этой группы

                                if(!$usersField && $groupInfo->expert_id == get_current_user_id()){ // Если эксперт еще не поставил оценки
                                    $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email
                                    FROM p_groups_users g
                                    LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                    WHERE g.id_group = %d", $_GET['group']));
                                }elseif(!$usersField && $groupInfo->moderator_id == get_current_user_id()){ // Если модератор еще не поставил оценки, берется данные эксперта
                                    $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b, p.expert_id, p.moderator_id, p.id proforma_result_id
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user 
                                WHERE g.id_group = %d AND p.group_id = %d AND expert_id = %d AND p.total < 20", $_GET['group'], $_GET['group'], $groupInfo->expert_id));
                                }
                            }

                              //echo $s;//printAll($usersField);
                            ?>

                            <?php foreach ($usersField as $user): ?>
                            <?php $user->decision = ($user->decision == 'Незачет') ? ASSESSMENT_SHEET[8] : ASSESSMENT_SHEET[7]; ?>
                                <tr>
                                    <td><?= ++$i; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn ink-reaction btn-icon-toggle btn-primary" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-chevron-down"></i></button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li class="divider"></li>
                                                <li><a href="/proforma/?form=<?= $_GET['form'] ?>r&group=<?= $_GET['group'] ?>&uid=<?= $user->user_id ?>" ><i class="md md-exit-to-app text-info"></i> Реккоммендация</a></li>
                                                <li class="divider"></li>
                                                <li><a href="/export_to_word/?form=<?= $_GET['form'] ?>a&group=<?= $_GET['group'] ?>&uid=<?= $user->user_id ?>" ><i class="md md-exit-to-app text-info"></i> Обоснование</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" id="fileu" data-id="<?= $user->user_id ?>" data-toggle="modal" data-target="#Modal">
                                                        <i class="md md-exit-to-app text-info"></i>
                                                        Файлы пользователя <?php  echo $num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM p_file WHERE group_id = %d AND user_id = %d", $_GET['group'], $user->user_id)) ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
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
                                    <?php $proformaDataUser = $wpdb->get_results($s=$wpdb->prepare("SELECT p.id, p.user_id, p.proforma_id, p.proforma_spr_id, p.group_id, p.datetime, p.data_value, p.datetime_update, p.expert_id, p.moderator_id FROM p_proforma_user_data p WHERE p.user_id= %d AND p.proforma_id = %d AND p.group_id =%d $fiels_text"
                                        , $user->user_id, $_GET['form'], $_GET['group'], $fiels_id ));
                                        if(!$proformaDataUser){
                                            $proformaDataUser = $wpdb->get_results($s=$wpdb->prepare("SELECT p.id, p.user_id, p.proforma_id, p.proforma_spr_id, p.group_id, p.datetime, p.data_value, p.datetime_update, p.expert_id, p.moderator_id FROM p_proforma_user_data p WHERE p.user_id= %d AND p.proforma_id = %d AND p.group_id =%d AND expert_id"
                                                , $user->user_id, $_GET['form'], $_GET['group'], $groupInfo->expert_id ));
                                            $action_moderator = 1;
                                        }?>
                                    <?php  $q=0; //printAll($proformaDataUser);  ?>

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
                                            <?php if($groupInfo->expert_id == get_current_user_id() || $groupInfo->moderator_id == get_current_user_id()): ?>
                                                <select name="item[<?= $user->user_id ?>][<?= $data->id ?>][<?= $data->section_id ?>][<?= $action ?>]" class="form-control" required>
                                                    <option></option>
                                                    <?php if(($proformaDataUser[$q]->data_value == 3 || ($data->section_id == 1 && $user->section_a == "Плагиат") || ($data->section_id == 2 && $user->section_b == "Плагиат"))){
                                                        echo '<option value="0">0</option>
                                                      <option value="1">1</option>
                                                      <option value="2">2</option>
                                                      <option value="3" selected>Плагиат</option>  
                                                      ';
                                                    }else{

                                                        ?>
                                                        <option value="0" <?= (isset($proformaDataUser[$q]->data_value) && $proformaDataUser[$q]->data_value == 0) ? "selected" : "" ?>>0</option>
                                                        <option value="1" <?= ($proformaDataUser[$q]->data_value == 1) ? "selected" : "" ?>>1</option>
                                                        <option value="2" <?= ($proformaDataUser[$q]->data_value == 2) ? "selected" : "" ?>>2</option>
                                                        <option value="3" >Плагиат</option>
                                                    <?php } ?>
                                                </select>
                                            <?php else:?>
                                            <?= ($proformaDataUser[$q]->data_value == 3) ? "Плагиат" : $proformaDataUser[$q]->data_value ?>
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
                        <button id="timer" class="btn btn-success">Сохранить</button>


                    <?php endif; ?>
                    <a href="/export_to_word/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>" class="btn btn-primary">Экспортировать проформу</a>
                    <a href="/export_to_word/?form=<?= $_GET['form'] ?>b&group=<?= $_GET['group'] ?>" class="btn btn-primary">Лист оценивания</a>
                </form>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php

$fileArr = $wpdb->get_row($wpdb->prepare("SELECT f.id, f.file_name, u.email FROM p_proforma_attached_file f LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id WHERE f.id = %d", $_GET['id']));


if($_GET['action'] == 'delete'){
    $commArr = $wpdb->get_row($wpdb->prepare("SELECT id, file_name, user_id, file_dir, year FROM p_proforma_attached_file WHERE id = %d", $_GET['id']));
    if($commArr->user_id != get_current_user_id()){ echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1&group='.$_GET['group'].'" />'; exit(); }

    if(unlink('/var/www/uploads/' . $commArr->year . '/' . $commArr->user_id . '/proforma/' . $commArr->file_dir)) {
        $wpdb->delete( 'p_proforma_attached_file', array( 'id' => $_GET['id'] ), array( '%d' ) );
    }

    echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1&group='.$_GET['group'].'" />';
}

if($_FILES && $_GET['group']){

//    echo "<pre>";
//    print_r($_FILES);
//    echo "</pre>";
//    exit();

    $year_folder = '/var/www/uploads/' . date('Y') . '/';
    if(!is_dir($year_folder))
        mkdir( $year_folder, 0777 );

    $uploaddircom = '/var/www/uploads/' . date('Y') . '/' . get_current_user_id() . '/';
    if(!is_dir($uploaddircom))
        mkdir( $uploaddircom, 0777 );

    $uploaddir = '/var/www/uploads/' . date('Y') . '/' . get_current_user_id() . '/proforma/';

    //printAll($_FILES); exit;

    if($_GET['action'] == 'add'){
        $filename = time() . '.loc';

//        $upload_dir = wp_upload_dir();
//        $uploaddir = $upload_dir['basedir'] . '/users_file/' . get_current_user_id() . '/comments/';

        if(!is_dir($uploaddir))
            mkdir( $uploaddir, 0777 );

        if($wpdb->insert(
            'p_proforma_attached_file',
            array( 'user_id' => get_current_user_id(), 'file_name' => $_FILES['file']['name'], 'group_id' => $_GET['group'],
                'file_size' => $_FILES['file']['size'], 'year' => date('Y'), 'file_dir' => $filename),
            array( '%d', '%s', '%d', '%d', '%d', '%s' )
        )){
            if( move_uploaded_file( $_FILES['file']['tmp_name'], $uploaddir . basename($filename) ) ){
                // отправка уведомление о загрузке файлов адменистратору
                //$attachments = array(WP_CONTENT_DIR . '/uploads/attach.zip');
                $uInfo = userInfo(get_current_user_id());
                $headers = 'From: portal@cpi.nis.edu.kz <portal@cpi.nis.edu.kz>' . "\r\n";
                $content =  "
Пользователь {$uInfo->surname} {$uInfo->name}

В проформе, прикрепил файл {$_FILES['file']['name']}, в группе {$groupInfo->number_group}
";

                wp_mail(userInfo($groupInfo->admin_id)->email, 'Уведомление о загрузке файла', $content, $headers, $attachments);
            }

        }else{
            $error = "Вы уже загрузили файл!";
        }
    }

    echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1&group='.$_GET['group'].'&e='.$error.'" />'; exit();

}

?>

<div class="row">

    <div class="col-md-8">
        <article class="margin-bottom-xxl">
            <?php if($_GET['e']):?>
                <div class="alert alert-warning" role="alert">
                    <strong>Warning!</strong> <?= $_GET['e'] ?>
                </div>
            <?php endif; ?>

        </article>
    </div><!--end .col -->

    <?php if($_GET['action'] == 'edit' || $_GET['action'] == 'add'): ?>
        <?php if($_GET['action'] == 'edit') {
            $commArr = $wpdb->get_row($wpdb->prepare("SELECT id, file_id, user_id, comments FROM p_proforma_attached_file WHERE id = %d", $_GET['commid']));
            if($commArr->user_id != get_current_user_id()) echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1&group=' . $_GET['group'] . '" />';
        }
        ?>
        <div class="col-lg-12">
            <form enctype="multipart/form-data" class="form-horizontal" method="POST" action="/proforma/?form=1&group=<?= $_GET['group'] ?><?= ($_GET['action'] == 'edit') ? "&action=edit&commid=" . $commArr->id : "&action=add" ?>">
                <div class="card">
                    <div class="card-head style-primary">
                        <header>Прикрепите скан</header>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="Username5" class="col-sm-2 control-label">Файл</label>
                            <div class="col-sm-10">
                                <input type="file" name="file" class="form-control" accept=".docx,.doc,.pptx,.ppt,.pdf">
                            </div>
                        </div>
                    </div><!--end .card-body -->
                    <div class="card-actionbar">
                        <div class="card-actionbar-row">
                            <input type="submit" class="btn btn-flat btn-primary ink-reaction"  value="Сохранить">
                        </div>
                    </div>
                </div><!--end .card -->
            </form>
        </div>
    <?php else: ?>

        <div class="col-lg-12">
            <a href="/proforma/?form=1&group=<?= $_GET['group'] ?>&action=add" class="btn btn-success">Прикрепить файл</a>
        </div>
        <div class="col-md-8">
            <article class="margin-bottom-xxl">

            </article>
        </div><!--end .col -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Файл</th>
                            <th>Автор</th>
                            <th>Дата создания</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $commentsArr = $wpdb->get_results($wpdb->prepare("SELECT f.id, f.file_name,  f.datetime, f.file_size, u.surname, u.name, u.patronymic, f.user_id
                            FROM p_proforma_attached_file f
                            LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id WHERE f.group_id = %d", $_GET['group']));?>
                        <?php foreach($commentsArr as $comm): ?>
                            <tr>
                                <td><?= ++$we ?></td>
                                <td><a href="/server_file/?proformadownload=<?= $comm->id ?>" class="text-primary"><?= $comm->file_name ?></a></td>
                                <td><?= $comm->surname ?> <?= $comm->name ?> <?= $comm->patronymic ?></td>
                                <td><?= $comm->datetime ?></td>
                                <td>
                                    <?php if (get_current_user_id() == $comm->user_id): ?>
                                        <a href="/proforma/?form=1&group=<?= $_GET['group'] ?>&id=<?= $comm->id ?>&action=delete" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>


</div>



<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <div class="box"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>

    const timeBtn = document.querySelectorAll("#timer");
    // const timeBtn2 = document.querySelector("#timer2");

    for (var i = 0; i < timeBtn.length ; i++) {
        timeBtn[i].addEventListener('click', save_wait);
    }

    function save_wait(event){
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