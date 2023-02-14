<div class="row">
    <div class="card">
        <?php
        $groupInfo  = groupInfo($_GET['group']);
        if($groupInfo->expert_id == get_current_user_id()){
            $user_name = 'expert_id';
        }elseif($groupInfo->moderator_id == get_current_user_id()){
            $user_name = 'moderator_id';
        }
        $finalData = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_user_result WHERE proforma_id = %d AND group_id = %d AND $user_name = %d"
            , $_GET['form'], $_GET['group'], get_current_user_id() ));
        printAll($finalData);

        ?>
    </div>
</div>
<?php if( ( $groupInfo->expert_id == get_current_user_id() && $groupInfo->expert_date > dateTime() )
    || ( $groupInfo->moderator_id == get_current_user_id() && $groupInfo->moderator_date > dateTime() )
    || getAccess(get_current_user_id())->access == 1 ): ?>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <?php if($_POST): ?>
                    <?php  //echo date("H:i:s d:m:y");//echo current_time( 'mysql', 0 );

                    //printAll($_POST); exit();

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
                                            $user_name => get_current_user_id()
                                        ], [ '%d', '%d','%d', '%d','%d','%d' ] );

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
                                            [ 'user_id' => $user_id, 'proforma_id' => $_GET['form'], 'group_id' => $_GET['group'], 'proforma_spr_id' => $spr_id, $user_name => get_current_user_id() ],
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



                            if( $groupInfo->moderator_id == get_current_user_id() && $finalData->expert_id != get_current_user_id() ){
                                if($action == "update") {
                                    $wpdb->update( 'p_proforma_user_result',
                                        [ 'section_b' => $section_b, 'section_a' => $section_a, 'total' => $final, 'decision' => $decision, 'date_update' => current_time('mysql', 0) ],
                                        [ 'user_id' => $uid, 'proforma_id' => $_GET['form'], 'group_id' => $_GET['group'], $user_name => get_current_user_id() ],
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
                                        $user_name => get_current_user_id(),
                                    ], ['%d', '%d','%d', '%s', '%s', '%s', '%s','%d']);
                                }
                            }elseif( $groupInfo->expert_id == get_current_user_id() && $finalData->moderator_id != get_current_user_id() ){
                                if($action == "update") {
                                    $wpdb->update( 'p_proforma_user_result',
                                        [ 'section_b' => $section_b, 'section_a' => $section_a, 'total' => $final, 'decision' => $decision, 'date_update' => current_time('mysql', 0) ],
                                        [ 'user_id' => $uid, 'proforma_id' => $_GET['form'], 'group_id' => $_GET['group'], $user_name => get_current_user_id() ],
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
                                        $user_name => get_current_user_id(),
                                    ], ['%d', '%d','%d', '%s', '%s', '%s', '%s','%d']);
                                }
                            }
                        }
                    }

                    echo "<meta http-equiv='refresh' content='0;url=/proforma/?form=$_GET[form]&group=$_GET[group]' />"; exit();
                    ?>
                <?php else: ?>
                    <form method="post" action="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>">
                        <table class="table table-bordered">
                            <tr>
                                <th rowspan="2">№</th>
                                <th rowspan="2">ФИО слушателей</th>
                                <?php $proformaSpr = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_spr WHERE proforma_id = %d", $_GET['form'])); ?>
                                <?php foreach ($proformaSpr as $data): ?>
                                    <th>
                                        <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);"><?= $data->name ?></span>
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
                                <th colspan="<?= $d ?>"><div align="center">Раздел А</div></th>
                                <th colspan="<?= $r ?>"><div align="center">Раздел В</div></th>
                            </tr>
                            <?php
                            if ( ( groupInfo($_GET['group'])->moderator_id == get_current_user_id() || groupInfo($_GET['group'])->teamleader_id == get_current_user_id() ) ){ //Проверка для отображению модератору и тимлидеру
                                $usersField = $wpdb->get_results($wpdb->prepare("SELECT r.total, g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                LEFT OUTER JOIN p_proforma_user_result r ON g.id_user = r.user_id 
                                WHERE r.moderator_id = %d AND g.id_group = %d AND r.group_id = %d AND r.total < 20", get_current_user_id(), $_GET['group'], $_GET['group']));
                            }else{
                                $usersField = $wpdb->get_results($wpdb->prepare("SELECT  g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                WHERE g.id_group = %d ", $_GET['group'] ));
                            } ?>

                            <?php foreach ($usersField as $user): ?>

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
                                        <?= $user->surname ?> <?= $user->name ?> <?= $user->patronymic ?>
                                    </td>
                                    <?php if( $finalData->moderator_id == get_current_user_id() && $groupInfo->moderator_id == get_current_user_id()){ // Если есть проформа модератора
                                        $proformaDataUser = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_user_data WHERE user_id= %d AND proforma_id = %d AND group_id = %d AND $user_name = %d"
                                            , $user->user_id, $_GET['form'], $_GET['group'], get_current_user_id() ));


                                        if ($proformaDataUser[$q]->proforma_spr_id == $data->id) {
                                            $action =  "2"; //update
                                        } else {
                                            $action =  "1"; //insert
                                        }
                                    }elseif( $finalData->moderator_id != get_current_user_id() && $groupInfo->moderator_id == get_current_user_id() ){ // Если нет проформы у модератора
                                        $proformaDataUser = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_user_data WHERE user_id= %d AND proforma_id = %d AND group_id = %d "
                                            , $user->user_id, $_GET['form'], $_GET['group'] ));
                                        $test = $proformaDataUser[$q]->proforma_spr_id ."==". $data->id;
                                        if ($proformaDataUser[$q]->proforma_spr_id == $data->id) {
                                            $action =  "2"; //update
                                        } else {
                                            $action =  "1"; //insert
                                        } // выставляем для создания новой проформы у модератора
                                    }else{ // Для эксперта по идее
                                        $proformaDataUser = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_user_data WHERE user_id= %d AND proforma_id = %d AND group_id = %d "
                                            , $user->user_id, $_GET['form'], $_GET['group'] ));
                                        if ($proformaDataUser[$q]->proforma_spr_id == $data->id) {
                                            $action =  "2"; //update
                                        } else {
                                            $action =  "1"; //insert
                                        }
                                    } ?>

                                    <?php  $q=0; //echo "<pre>"; print_r($proformaDataUser); echo "</pre>";  ?>

                                    <?php foreach ($proformaSpr as $data): ?>
                                        <?php  //echo "{$proformaDataUser[$q]->proforma_spr_id} == {$data->id}<br>";

                                        $key = array_search($user->user_id, array_column($finalData, 'user_id'));
                                        ?>
                                        <td> <?= $test ?>
                                            <select name="item[<?= $user->user_id ?>][<?= $data->id ?>][<?= $data->section_id ?>][<?= $action ?>]" class="form-control" required>
                                                <option></option>
                                                <?php if(($proformaDataUser[$q]->data_value == 3 || ($data->section_id == 1 && $finalData[$key]->section_a == "Плагиат") || ($data->section_id == 2 && $finalData[$key]->section_b == "Плагиат"))){
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
                                        </td>
                                        <?php $q++; ?>
                                    <?php endforeach; ?>

                                    <?php if($finalData[$key]->user_id == $user->user_id): ?>
                                        <td><?= $finalData[$key]->total ?></td>
                                        <td><?= $finalData[$key]->decision ?></td>
                                    <?php else: ?>
                                        <td>Не заполнено</td>
                                        <td>Не заполнено</td>
                                    <?php endif; ?>
                                </tr>
                            <?php  endforeach; ?>
                        </table>
                        <input type="submit" value="Сохранить" class="btn btn-success">
                        <a href="/export_to_word/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>" class="btn btn-primary">Экспортировать проформу</a>
                        <a href="/export_to_word/?form=<?= $_GET['form'] ?>b&group=<?= $_GET['group'] ?>" class="btn btn-primary">Лист оценивания</a>
                        <?php if( $groupInfo->moderator_id == get_current_user_id() && $finalData->expert_id != get_current_user_id() ): ?>
                            <input type="hidden">
                        <?php endif; ?>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?php
    global $wpdb;




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
                                <button type="submit" class="btn btn-flat btn-primary ink-reaction">Сохранить</button>
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
<?php else: ?>
    <?php alertStatus('danger', 'Доступ закрыт!') ?>
<?php endif; ?>
