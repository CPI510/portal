<?php
global $wpdb;

$access = getAccess(get_current_user_id())->access;
accessUser($access);

if(isset($_GET['pid']) && $_GET['pid'] != 'all'){
    $dopsql = "AND g.program_id = %d";
    $pid = $_GET['pid'];
}elseif($_GET['pid'] == 'all'){
    $dopsql = "";
    $pid;
}else{
    $dopsql = "";
    $pid;
//    $userProgramId =  userInfo(get_current_user_id())->program_id;
//    if($userProgramId){
//        $dopsql = "AND admin_id = %d";
//        $pid = get_current_user_id();
//    }
}

if($_GET['notactive'] == 1){
    $notactive = "AND g.active = 2";
    $activetext = "(не активные)";
}else{
    $notactive = "AND g.active = 1";
    $activetext = "";
}

if(isset($_GET['potok'])){
    $potok_sql = " AND g.potok = %d";
    $potokid = $_GET['potok'];
}else{
    $potok_sql;
    $potokid;
}

?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Список групп <?=$activetext?></h1>
    </div>
    <div class="col-lg-12">
        <?php if ($access == 1): ?>
            <a href="/groups/?z=add" class="btn btn-success">Добавить</a>
        <?php endif; ?>
    </div><!--end .col -->
    <div class="col-md-8">
        <article class="margin-bottom-xxl">

        </article>
    </div><!--end .col -->
    <?php if( $access == 1 ): ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-sm-6">
                        <form>
                            <label><b>Программа</b></label>
                            <select name="pid" class="form-control" onchange="this.form.submit()">
                                <option></option>
                                <option value="all" <?php if($_GET['pid'] == 'all') echo 'selected'; ?>>Все</option>
                                <?php $programms = $wpdb->get_results("SELECT * FROM p_programs"); ?>
                                <?php foreach ($programms as $programm): ?>
                                    <?php if($_GET['pid'] == $programm->id): ?>
                                        <option value="<?= $programm->id ?>" selected><?= $programm->p_name ?></option>
                                    <?php else: ?>
                                        <option value="<?= $programm->id ?>"><?= $programm->p_name ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="z" value="<?= $_GET['z']?>">
                            <input type="hidden" name="notactive" value="<?= $_GET['notactive']?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php
    $sqlAll = "SELECT g.id, g.number_group, g.trener_id, g.start_date, g.end_date, p.proforma_id, p.p_name, p.id program_id, g.trener_id, g.independent_trainer_id, 
g.expert_id, g.moderator_id, g.teamleader_id, g.potok, g.admin_id
, COUNT(u.id_group) num_g, f.surname t_surname, f.name t_name, f.patronymic t_patronymic
, e.surname expert_surname, e.name expert_name, e.patronymic expert_patronymic
, m.surname moderator_surname, m.name moderator_name, m.patronymic moderator_patronymic
, t.surname teamleader_surname, t.name teamleader_name, t.patronymic teamleader_patronymic 
                            FROM p_groups g
                            LEFT JOIN p_programs p ON p.id = g.program_id
                            LEFT JOIN p_groups_users u ON u.id_group = g.id
                            LEFT JOIN p_user_fields f ON f.user_id = g.trener_id
                            LEFT OUTER JOIN p_user_fields e ON e.user_id = g.expert_id
                            LEFT OUTER JOIN p_user_fields m ON m.user_id = g.moderator_id
                            LEFT OUTER JOIN p_user_fields t ON t.user_id = g.teamleader_id
                            WHERE g.deleted = 0 $notactive $potok_sql";

    if ($access == 1){
        $sqlPotok = "SELECT g.potok FROM p_groups g  WHERE g.deleted = 0 $notactive";
        $potoks = $wpdb->get_results($s=$wpdb->prepare("$sqlPotok $dopsql GROUP BY g.potok ORDER BY g.potok",$pid, get_current_user_id()));

        $results = $wpdb->get_results($s=$wpdb->prepare("$sqlAll
                            $dopsql
                            GROUP BY g.id  ORDER BY g.date_create DESC", $potokid, $pid, get_current_user_id() ));
        if($_GET['id'] && $_GET['d']){
            //$resd = $wpdb->query($wpdb->prepare("DELETE FROM `p_groups` WHERE `p_groups`.`id` = %d", $_GET['id']));
            $groupInfo = groupInfo($_GET['id']);
            $number_group_deleted = $groupInfo->number_group . '_deleted';
            $resd = $wpdb->query($wpdb->prepare("UPDATE `p_groups` SET `deleted` = 1, number_group = %s WHERE `id` = %d", $number_group_deleted, $_GET['id']));
//                            $resd = $wpdb->update( 'p_groups',
//                                [ 'active' => 2 ],
//                                [ 'id' => $_GET['id'] ],
//                                [ '%d' ],
//                                [ '%d' ]
//                            );
            if(!$resd) alertStatus('warning', 'Нет данных');
            //printAll($_GET);
            //else echo'<meta http-equiv="refresh" content="0;url=/groups/?z=list" />';
        } //echo $s;
    }else if($access == 4){
        $results = $wpdb->get_results($s=$wpdb->prepare("$sqlAll
                            AND g.trener_id = %d OR g.independent_trainer_id = %d
                            GROUP BY g.id ORDER BY g.date_create DESC", get_current_user_id(), get_current_user_id()));
    }else if($access == 7){
        $results = $wpdb->get_results($s=$wpdb->prepare("$sqlAll
                            AND g.expert_id = %d OR g.moderator_id = %d OR g.teamleader_id = %d
                            GROUP BY g.id ORDER BY g.date_create DESC", get_current_user_id(), get_current_user_id(), get_current_user_id()));
    }else{
        $results = $wpdb->get_results($wpdb->prepare("$sqlAll
                            AND u.id_user = %d
                            GROUP BY g.id ORDER BY g.date_create DESC", get_current_user_id()));
    } //echo $s;
    ?>
    <style>
        [aria-current="page"] {
            pointer-events: none;
            cursor: default;
            text-decoration: none;
            color: black;
        }
    </style>

    <?php if($access == 1): ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php foreach ($potoks as $potok): ?>
                        <?php if($_GET['potok'] == $potok->potok): ?>
                            <a href="/groups/?z=foradmin&potok=<?=$potok->potok?>&pid=<?= $_GET['pid'] ?>" class="btn btn-default"><?=$potok->potok?> поток</a>
                        <?php else: ?>
                            <a href="/groups/?z=foradmin&potok=<?=$potok->potok?>&pid=<?= $_GET['pid'] ?>" class="btn btn-info"><?=$potok->potok?> поток</a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['potok']) && $access == 1){
        $viewall = 1;
    }elseif(!isset($_GET['potok']) && $access == 1){
        $viewall = 0;
    }elseif($access != 1){
        $viewall = 1;
    }else{
        $viewall = 0;
    }?>

    <?php if($viewall == 1): ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <form method="post" action="/export_to_excel/?form=1&group=all">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Номер группы</th>
                                    <th>Поток</th>
                                    <?php if( $access == 7): ?>
                                        <th>
                                            Дата окончания работы
                                        </th>
                                    <?php else: ?>
                                        <th>Дата начала загрузки</th>
                                        <th>Дата окончания загрузки</th>
                                    <?php endif; ?>

                                    <th>Сотрудники</th>
                                    <th>Ссылка</th>
                                    <?php if ($access == 1 || $access == 4): ?>
                                        <th>Кол-во</th>
                                    <?php endif; ?>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach($results as $res): ?>
                                    <?php
                                    $hidden = "Скрыто";
                                    $assessment_sheet = '<span class="badge style-primary-dark"><a href="/assessment/?z=sheet&group='.$res->id.'">Лист оценивания</a></span><br>';
                                    $proforma = '<span class="badge style-primary-dark"><a href="/proforma/?form='.$res->proforma_id.'&group='.$res->id.' ">Проформа</a></span><br>';
                                    $proforma_empty = '<span class="badge">Проформа</span><br>';
                                    $download_all = '<span class="badge style-primary-bright">
                                                <a href="/server_file/?zip=2&groupid=' . $res->id . '" ><i class="fa fa-download"></i> Скачать все файлы</a>
                                            </span><br>';
                                    $download_portfolio = '<span class="badge style-primary-bright">
                                                <a href="/server_file/?zip=2&groupid=' . $res->id . '&portfolio=1" ><i class="fa fa-download"></i> Скачать все портфолио</a>
                                            </span><br>';
                                    $res->potok = "<b>" . $res->potok . " поток</b>";

                                    if( $access == 1 ){
                                        $date = groupInfo($res->id)->expert_date;
                                        $res->number_group;
                                        $res->potok;
                                        $res->start_date;
                                        $res->end_date;
                                        $res->t_surname;
                                        $res->t_name;
                                        $res->t_patronymic;
                                        $res->expert_surname;
                                        $res->expert_name;
                                        $res->expert_patronymic;
                                        $res->moderator_surname;
                                        $res->moderator_name;
                                        $res->moderator_patronymic;
                                        $link = get_site_url() ."/registration/?id=". $res->id;
                                        $independent_trainer = "<b>Независимый тренер:</b> ".nameUser($res->independent_trainer_id, 5)."<br>";
                                        if($res->program_id == 7){
                                            $mainModul = $assessment_sheet;
                                        }else{
                                            if ($res->proforma_id == 0){
                                                $mainModul = $proforma_empty;
                                            }else{
                                                $mainModul = $proforma;
                                            }
                                        }
                                        $download_all_files = $download_all;
                                        if($res->program_id == 7){
                                            $download_all_files_portfolio = $download_portfolio;
                                        }


                                    }elseif( $res->independent_trainer_id == get_current_user_id() && $res->program_id == 7 ){
                                        //
                                        $date = groupInfo($res->id)->independent_trainer_date;
                                        $res->number_group = $hidden;
                                        $res->potok = $hidden;
                                        $res->start_date = $hidden;
                                        $res->end_date = $hidden;
                                        $res->t_surname = $hidden;
                                        $res->t_name = $hidden;
                                        $res->t_patronymic = $hidden;
                                        $res->expert_surname = $hidden;
                                        $res->expert_name = $hidden;
                                        $res->expert_patronymic = $hidden;
                                        $res->moderator_surname = $hidden;
                                        $res->moderator_name = $hidden;
                                        $res->moderator_patronymic = $hidden;
                                        $res->p_name = $hidden;
                                        $link = $hidden;
                                        $independent_trainer = "<b>Независимый тренер:</b> ".nameUser($res->independent_trainer_id, 5)."<br>";
                                        $mainModul = $assessment_sheet;


                                    }elseif( $res->moderator_id == get_current_user_id() && $res->program_id == 7 ){
                                        //
                                        $date = groupInfo($res->id)->moderator_date;
                                        $res->number_group = $hidden;
                                        $res->potok = $hidden;
                                        $res->start_date = $hidden;
                                        $res->end_date = $hidden;
                                        $res->t_surname = $hidden;
                                        $res->t_name = $hidden;
                                        $res->t_patronymic = $hidden;
                                        $res->expert_surname = $hidden;
                                        $res->expert_name = $hidden;
                                        $res->expert_patronymic = $hidden;
                                        $res->moderator_surname;
                                        $res->moderator_name;
                                        $res->moderator_patronymic;
                                        $res->p_name = $hidden;
                                        $link = $hidden;
                                        $independent_trainer = $hidden;
                                        $mainModul = $assessment_sheet;

                                    }elseif( $res->trener_id == get_current_user_id() && $res->program_id == 7){
                                        //
                                        $date = groupInfo($res->id)->trener_date;
                                        $res->number_group;
                                        $res->potok = $hidden;
                                        $res->start_date;
                                        $res->end_date;
                                        $res->t_surname;
                                        $res->t_name;
                                        $res->t_patronymic;
                                        $res->expert_surname = $hidden;
                                        $res->expert_name = $hidden;
                                        $res->expert_patronymic = $hidden;
                                        $res->moderator_surname = $hidden;
                                        $res->moderator_name = $hidden;
                                        $res->moderator_patronymic = $hidden;
                                        $link = get_site_url() ."/registration/?id=". $res->id;
                                        $independent_trainer = $hidden;
                                        $mainModul = $assessment_sheet;

                                    }elseif( $res->expert_id == get_current_user_id() && $res->program_id == 7){
                                        //
                                        $date = groupInfo($res->id)->expert_date;
                                        $res->number_group;
                                        $res->potok;
                                        $res->start_date;
                                        $res->end_date;
                                        $res->t_surname;
                                        $res->t_name;
                                        $res->t_patronymic;
                                        $res->expert_surname;
                                        $res->expert_name;
                                        $res->expert_patronymic;
                                        $res->moderator_surname;
                                        $res->moderator_name;
                                        $res->moderator_patronymic;
                                        $link = get_site_url() ."/registration/?id=". $res->id;
                                        $independent_trainer = $hidden;
                                        $mainModul = $assessment_sheet;
                                        $download_all_files = $download_all;
                                        $download_all_files_portfolio = $download_portfolio;

                                    }elseif( $res->trener_id == get_current_user_id() ){
                                        //
                                        $date = groupInfo($res->id)->trener_date;
                                        $res->number_group;
                                        $res->potok;
                                        $res->start_date;
                                        $res->end_date;
                                        $res->t_surname;
                                        $res->t_name;
                                        $res->t_patronymic;
                                        $res->expert_surname;
                                        $res->expert_name;
                                        $res->expert_patronymic;
                                        $res->moderator_surname;
                                        $res->moderator_name;
                                        $res->moderator_patronymic;
                                        $link = get_site_url() ."/registration/?id=". $res->id;
                                        $independent_trainer = $hidden;

                                        $mainModul = '';

                                    }elseif( $res->expert_id == get_current_user_id() ){
                                        $date = groupInfo($res->id)->expert_date;
                                        $res->number_group;
                                        $res->potok;
                                        $res->start_date;
                                        $res->end_date;
                                        $res->t_surname;
                                        $res->t_name;
                                        $res->t_patronymic;
                                        $res->expert_surname;
                                        $res->expert_name;
                                        $res->expert_patronymic;
                                        $res->moderator_surname;
                                        $res->moderator_name;
                                        $res->moderator_patronymic;
                                        $link = get_site_url() ."/registration/?id=". $res->id;
                                        $independent_trainer = $hidden;
                                        if ($res->proforma_id == 0){
                                            $mainModul = $proforma_empty;
                                        }else{
                                            $mainModul = $proforma;
                                        }

                                    }elseif ( $res->moderator_id == get_current_user_id() ){
                                        $date = groupInfo($res->id)->moderator_date;
                                        $res->number_group;
                                        $res->potok;
                                        $res->start_date;
                                        $res->end_date;
                                        $res->t_surname;
                                        $res->t_name;
                                        $res->t_patronymic;
                                        $res->expert_surname;
                                        $res->expert_name;
                                        $res->expert_patronymic;
                                        $res->moderator_surname;
                                        $res->moderator_name;
                                        $res->moderator_patronymic;
                                        $link = get_site_url() ."/registration/?id=". $res->id;
                                        if ($res->proforma_id == 0){
                                            $mainModul = $proforma_empty;
                                        }else{
                                            $mainModul = $proforma;
                                        }

                                    }elseif ( $res->teamleader_id == get_current_user_id() ){
                                        $date = groupInfo($res->id)->teamleader_date;
                                        $res->number_group;
                                        $res->potok;
                                        $res->start_date;
                                        $res->end_date;
                                        $res->t_surname;
                                        $res->t_name;
                                        $res->t_patronymic;
                                        $res->expert_surname;
                                        $res->expert_name;
                                        $res->expert_patronymic;
                                        $res->moderator_surname;
                                        $res->moderator_name;
                                        $res->moderator_patronymic;
                                        $link = get_site_url() ."/registration/?id=". $res->id;
                                        if ($res->proforma_id == 0){
                                            $mainModul = $proforma_empty;
                                        }else{
                                            $mainModul = $proforma;
                                        }

                                    }else{
                                        //$date = groupInfo($res->id)->teamleader_date;
                                        $res->number_group;
                                        $res->potok;
                                        $res->start_date;
                                        $res->end_date;
                                        $res->t_surname;
                                        $res->t_name;
                                        $res->t_patronymic;
                                        $res->expert_surname;
                                        $res->expert_name;
                                        $res->expert_patronymic;
                                        $res->moderator_surname;
                                        $res->moderator_name;
                                        $res->moderator_patronymic;
                                        $link = get_site_url() ."/registration/?id=". $res->id;
                                        $mainModul = $hidden;
                                        $independent_trainer = $hidden;
                                    }
                                    ?>
                                    <input type="hidden" name="groups[]" value="<?= $res->id ?>">
                                    <?php if( $date < dateTime() && $access == 7){
                                        //echo "class='danger'";
                                        //$dangertext = '<br>Время работы закончилось!';
                                        //$linkdanger = 'aria-current="page"';
                                        //echo "<br>$date < " .dateTime();
                                        ?><?php
                                    }else{
                                        ?>
                                        <tr>
                                            <td><i class="fa fa-info-circle fa-fw text-info" data-toggle="tooltip" data-placement="right" data-original-title="<?= $res->p_name ?>" style="cursor: pointer"></i></td>
                                            <td><?= ++$i ?></td>
                                            <td><?= $res->number_group ?></td>
                                            <td><?= $res->potok ?></td>
                                            <?php if( $access == 7): ?>
                                                <th>
                                                    <?= $date ?>
                                                </th>
                                            <?php else: ?>
                                                <td><?= $res->start_date ?></td>
                                                <td><?= $res->end_date ?></td>
                                            <?php endif; ?>

                                            <td>
                                                <b>Тренер:</b> <?= $res->t_surname ?> <?= $res->t_name ?> <?= $res->t_patronymic ?><br>
                                                <b>Эксперт:</b> <?= $res->expert_surname ?> <?= $res->expert_name ?> <?= $res->expert_patronymic ?><br>
                                                <b>Модератор:</b> <?= $res->moderator_surname ?> <?= $res->moderator_name ?> <?= $res->moderator_patronymic ?><br>
                                                <?php if($res->program_id == 7): ?>
                                                    <?= $independent_trainer; ?>
                                                <?php else: ?>

                                                    <b>Тимлидер:</b> <?= $res->teamleader_surname ?> <?= $res->teamleader_name ?> <?= $res->teamleader_patronymic ?><br>
                                                <?php endif; ?>

                                            </td>
                                            <td>
                                                <code class="text-medium">
                                                    <?=$link?>
                                                </code>
                                            </td>
                                            <td>
                                                <?php if ($access == 1 || $access == 4 || $res->expert_id == get_current_user_id() || ($res->moderator_id == get_current_user_id() && $res->program_id == 7) ): ?>
                                                    <span class="badge style-success"><a href="/groups/?z=group&id=<?= $res->id ?>" >Слушателей: <?= $res->num_g ?></a></span><br>
                                                <?php endif; ?>
                                                <?= $mainModul ?>
                                                <?php if( $access == 1 || $res->expert_id == get_current_user_id() ): ?>
                                                    <?= $download_all_files ?>
                                                    <?= $download_all_files_portfolio ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= nameUser($res->admin_id) ?></td>
                                            <td>
                                                <?php if ($access == 1): ?>
                                                    <a href="/groups/?z=edit&id=<?= $res->id ?>" class="btn btn-icon-toggle" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
                                                    <a href="/groups/?z=list&d=d&id=<?= $res->id ?>" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
                                                <?php endif; ?>

                                            </td>
                                        </tr>
                                        <?php
                                        $dangertext = '';
                                    }  ?>

                                    <?php
                                    $link = "";
                                    $download_all_files = "";
                                    $download_all_files_portfolio = "";
                                    ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if( $access == 1 ): ?>
                                <?php //print_r($ids); ?>
                                <button type="submit" class="btn btn-success "><i class="fa fa-file-excel-o"></i> Сформировать ведомость</button>
                            <?php endif; ?>
                        </form>
                    </div><!--end .table-responsive -->
                </div><!--end .card-body -->
            </div><!--end .card -->
        </div><!--end .col -->
    <?php else: ?>
        <p>
            <?php alertStatus('info','Выберите поток!'); ?>
        </p>
    <?php endif; ?>


</div>