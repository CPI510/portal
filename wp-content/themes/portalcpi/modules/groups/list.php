<?php
global $wpdb;

$access = getAccess(get_current_user_id())->access;
accessUser($access);

if(!isset($_GET['y'])){
    $_GET['y'] = date('Y');
}

if(isset($_GET['pid']) && $_GET['pid'] != 'all'){
    $dopsql = "AND g.program_id = %d AND (g.admin_id = %d OR g.admin_id2 = %d)";
    $pid = $_GET['pid'];
    $pid2 = $_GET['pid'];
}elseif($_GET['pid'] == 'all'){
    $dopsql = "AND (g.admin_id = %d OR g.admin_id2 = %d";
    $pid = get_current_user_id();
    $pid2 = get_current_user_id();
}else{
    $dopsql = "AND (g.admin_id = %d OR g.admin_id2 = %d)";
    $pid = get_current_user_id();
    $pid2 = get_current_user_id();
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
    $year_sql = " AND g.start_date LIKE %s";
    $year_id = "%".$_GET['y']."%";
    $potokid = $_GET['potok'];
}else{
    $potok_sql = "";
    $year_sql  = " AND g.start_date LIKE %s";
    $year_id = "%".$_GET['y']."%";
    $potokid = "";
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
    <?php if( $access == 1111 ): ?>
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
g.expert_id, g.moderator_id, g.teamleader_id, g.potok
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
                            WHERE g.deleted = 0 $notactive $potok_sql ";

    if ($access == 1){

        $sqlPotok = "SELECT g.potok FROM p_groups g  WHERE g.deleted = 0 $notactive ";
        $potoks = $wpdb->get_results($ps = $wpdb->prepare("$sqlPotok $dopsql $year_sql GROUP BY g.potok ORDER BY g.potok",$pid,$pid2,$year_id));

        $results = $wpdb->get_results($sss=$wpdb->prepare("$sqlAll
                            $dopsql $year_sql
                            GROUP BY g.id  ORDER BY g.date_create DESC", $potokid, $pid,$pid2, $year_id ));

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
        }
//                        echo $sss;
//                        echo "$sss
//                        <br>potokid=$potokid
//                        <br>pid=$pid
//                        <br>get_current_user_id=".get_current_user_id()."
//                        <br>year_id=$year_id
//                        ";
    }else if($access == 4){
        $results = $wpdb->get_results($s=$wpdb->prepare("$sqlAll
                            AND (g.trener_id = %d OR g.independent_trainer_id = %d)
                            GROUP BY g.id ORDER BY g.date_create DESC", get_current_user_id(), get_current_user_id()));
    }else if($access == 7){
        $results = $wpdb->get_results($s=$wpdb->prepare("$sqlAll
                            AND (g.expert_id = %d OR g.moderator_id = %d OR g.teamleader_id = %d)
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

                    <?php $current_year = date('Y');
                    $year_data = $wpdb->get_results($wpdb->prepare("SELECT  YEAR(start_date) second_year FROM `p_groups` WHERE admin_id = %d GROUP BY second_year",get_current_user_id()));
                    ?>
                    <?php foreach ($year_data as $data): ?>
                        <?php if($_GET['y'] == $data->second_year): ?>
                            <a href="#" class="btn btn-primary-dark " ><?=$data->second_year?> год</a>
                        <?php else: ?>
                            <a href="/groups/?z=list&y=<?= $data->second_year ?>&notactive=<?=$_GET['notactive']?>" class="btn btn-primary-bright"><?=$data->second_year?> год</a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <br>
                    <br>
                    <?php foreach ($potoks as $potok): ?>
                        <?php if($_GET['potok'] == $potok->potok): ?>
                            <a href="#" class="btn btn-primary-dark "><?=$potok->potok?> поток</a>
                        <?php else: ?>
                            <a href="/groups/?z=list&y=<?= $_GET['y'] ?>&potok=<?=$potok->potok?>&notactive=<?=$_GET['notactive']?>" class="btn btn-primary-bright"><?=$potok->potok?> поток</a>
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
                    <?php if (getAccess( get_current_user_id() )->access == 7 || getAccess( get_current_user_id() )->access == 4): ?>
                        <?php if ($appointed_users = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM p_appointed7 WHERE appointed_user_id = %d", get_current_user_id() ) ) ){
                            echo "<a href='/assessment/?z=appointed7&list=all' class='btn ink-reaction btn-success'>Назначенные (закрепленные) слушатели</a>";
                        } ?>
                    <?php endif; ?>

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
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach($results as $res): ?>
                                    <?php
                                    $groupArr = groupInfo($res->id);
                                    $listeners = '<span class="badge style-success"><a href="/groups/?z=group&id='. $res->id .'" >Слушателей: ' . $res->num_g . '</a></span><br>'; // должен видеть админ, тренер группы, эксперт группы, модератор и тимлидер в 14 программе
                                    $hidden = "Скрыто";
                                    if($res->program_id == 14 ){
                                        $assessment_sheet = '<span class="badge style-primary-dark"><a href="/assessment/?z=sheet'.$res->program_id.'&group='.$res->id.'">Лист оценивания</a></span><br>';
                                        $independent_trainer = "<b>Модератор 2: </b>".nameUser($res->independent_trainer_id,5)."<br>";
                                    }elseif($res->program_id == 6 || $res->program_id == 16){
                                        $assessment_sheet = '<span class="badge style-primary-dark"><a href="/assessment/?z=sheet'.$res->program_id.'&group='.$res->id.'">Лист оценивания</a></span><br>';
                                        $independent_trainer = "<b>Независимый тренер: </b>".nameUser($res->independent_trainer_id,5)."<br>";
                                    }elseif($res->program_id == 17){
                                        $assessment_sheet = '<span class="badge style-primary-dark"><a href="/assessment/?z=sheet'.$res->program_id.'&group='.$res->id.'">Лист оценивания</a></span><br>';
                                        $independent_trainer = "<b>Независимый тренер: </b>".nameUser($res->independent_trainer_id,5)."<br>";
                                    }else{
                                        $assessment_sheet = '<span class="badge style-primary-dark"><a href="/assessment/?z=sheet&group='.$res->id.'">Лист оценивания</a></span><br>';
                                        $independent_trainer = "<b>Независимый тренер: </b>".nameUser($res->independent_trainer_id,5)."<br>";
                                    }
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
                                        $date = $groupArr->expert_date;
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

                                        if($res->program_id == 7 || $res->program_id == 14 || $res->program_id == 6 || $res->program_id == 16 || $res->program_id == 17){
                                            $mainModul = $assessment_sheet;
                                        }else{
                                            if ($res->proforma_id == 0){
                                                $mainModul = $proforma_empty;
                                            }else{
                                                $mainModul = $proforma;
                                            }
                                        }
                                        $download_all_files = $download_all;
                                        if($res->program_id == 7 || $res->program_id == 15){
                                            $download_all_files_portfolio = $download_portfolio;
                                        }
                                        $listeners_link = $listeners;


                                    }elseif( $res->program_id == 7 ){

                                        if($res->independent_trainer_id == get_current_user_id()){
                                            //
                                            $date = $groupArr->independent_trainer_date;
                                            $res->number_group = $hidden;
                                            $res->potok = $hidden;
                                            $res->start_date = $hidden;
                                            $res->end_date = $hidden;
                                            $res->p_name = $hidden;
                                            $link = $hidden;
                                            //$independent_trainer = "<b>Независимый тренер:</b> ".nameUser($res->independent_trainer_id, 5)."<br>";
                                            $mainModul = $assessment_sheet;
                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Независимый тренер: " . nameUser($res->independent_trainer_id, 5);
                                        }elseif ($res->moderator_id == get_current_user_id()){
                                            $date = $groupArr->moderator_date;
                                            $res->number_group = $hidden;
                                            $res->potok = $hidden;
                                            $res->start_date = $hidden;
                                            $res->end_date = $hidden;
                                            $res->p_name = $hidden;
                                            $link = $hidden;
                                            $mainModul = $assessment_sheet;
                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Модератор: {$res->moderator_surname} {$res->moderator_name} {$res->moderator_patronymic}";
                                        }elseif ( $res->trener_id == get_current_user_id() ){
                                            $date = $groupArr->trener_date;
                                            $res->number_group;
                                            $res->potok;
                                            $res->start_date;
                                            $res->end_date;
                                            $link = get_site_url() ."/registration/?id=". $res->id;
                                            $mainModul = $assessment_sheet;
                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Тренер: {$res->t_surname} {$res->t_name} {$res->t_patronymic}";
                                        }elseif ( $res->expert_id == get_current_user_id() ){
                                            $date = $groupArr->expert_date;
                                            $res->number_group;
                                            $res->potok;
                                            $res->start_date;
                                            $res->end_date;
                                            $link = get_site_url() ."/registration/?id=". $res->id;
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Эксперт: {$res->expert_surname} {$res->expert_name} {$res->expert_patronymic}";
                                            $independent_trainer = "<b>Независимый тренер:</b> ".nameUser($res->independent_trainer_id, 5)."<br>";
                                        }elseif ( $res->teamleader_id == get_current_user_id() ){
                                            $date = $groupArr->teamleader_date;
                                            $res->number_group;
                                            $res->potok;
                                            $res->start_date;
                                            $res->end_date;
                                            $link = get_site_url() ."/registration/?id=". $res->id;
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Эксперт: {$res->expert_surname} {$res->expert_name} {$res->expert_patronymic}";
                                            $independent_trainer = "<b>Независимый тренер:</b> ".nameUser($res->independent_trainer_id, 5)."<br>";
                                        }



                                    }elseif( $res->program_id == 14 || $res->program_id == 6 || $res->program_id == 16){
                                        $listeners_null = '<span class="badge style-success" ><a href="/groups/?z=group&id='. $res->id .'" >Слушатели</a></span><br>'; // должен видеть админ, тренер группы, эксперт группы, модератор и тимлидер в 14 программе
                                        $date = $groupArr->end_date;
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
                                        $res->moderator_name ;
                                        $res->moderator_patronymic;
                                        $link = get_site_url() ."/registration/?id=". $res->id;

                                        if ($res->program_id == 14) {
                                            $independent_trainer = "<b>Модератор 2: </b>".nameUser($res->independent_trainer_id,5);
                                            $assessment_rubric = $wpdb->get_row($wpdb->prepare("SELECT COUNT(id) num_l FROM p_assessment_rubric WHERE grading_solution = 4 AND create_user_id = %d AND group_id = %d", $groupArr->expert_id, $res->id )) ;
                                            $count = $assessment_rubric->num_l;
                                        } elseif ($res->program_id == 6 || $res->program_id == 16) {
                                            $independent_trainer = "<b>Независимый тренер: </b>".nameUser($res->independent_trainer_id,5);
                                            $assessment_rubric = count($wpdb->get_results($s=$wpdb->prepare("SELECT listener_id FROM p_assessment_rubric WHERE grading_solution IN (4,3) AND create_user_id IN (%d, %d) AND group_id = %d GROUP BY listener_id", $groupArr->trener_id, $groupArr->independent_trainer_id, $res->id )));
                                            $count = $assessment_rubric;
                                        }

                                        $listeners_link_for_moder_teamleader = '<span class="badge style-success" ><a href="/groups/?z=group&id='. $res->id .'" >Слушателей: '.$count.'</a></span><br>';

                                        if ( $res->moderator_id == get_current_user_id() ){
                                            $date = $groupArr->moderator_date;
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners_link_for_moder_teamleader;
                                            $user_role_FIO = "Модератор: {$res->moderator_surname} {$res->moderator_name} {$res->moderator_patronymic}";
                                        } elseif ( $res->trener_id == get_current_user_id() ){
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Тренер: {$res->t_surname} {$res->t_name} {$res->t_patronymic}";
                                        } elseif ( $res->expert_id == get_current_user_id() && $res->program_id == 14){
                                            $date = $groupArr->expert_date;
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Эксперт: {$res->expert_surname} {$res->expert_name} {$res->expert_patronymic}";
                                        } elseif ( $res->expert_id == get_current_user_id() && $res->program_id == 6  ){
                                            $date = $groupArr->expert_date;
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners_link_for_moder_teamleader;;
//                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Эксперт: {$res->expert_surname} {$res->expert_name} {$res->expert_patronymic}";
                                        } elseif ( $res->expert_id == get_current_user_id() && $res->program_id == 16 ){
                                            $date = $groupArr->expert_date;
                                            $mainModul = '';
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners;;
//                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Эксперт: {$res->expert_surname} {$res->expert_name} {$res->expert_patronymic}";
                                        } elseif ( $res->independent_trainer_id == get_current_user_id() && $res->program_id == 14){
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners_null;
                                            $user_role_FIO = "Наблюдатель: ".nameUser($res->independent_trainer_id,5) . "<br> Тренер: {$res->t_surname} {$res->t_name} {$res->t_patronymic}";
                                        } elseif ( $res->independent_trainer_id == get_current_user_id() && ($res->program_id == 6 || $res->program_id == 16)){
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners;
                                            $user_role_FIO = "Независимый тренер: ".nameUser($res->independent_trainer_id,5) . "<br> Тренер: {$res->t_surname} {$res->t_name} {$res->t_patronymic}";
                                        } elseif ( $res->teamleader_id == get_current_user_id() && $res->program_id == 14){
                                            $date = $groupArr->teamleader_date;
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners_link_for_moder_teamleader;
                                            $user_role_FIO = "Тимлидер: ".nameUser($res->teamleader_id,5);
                                        } elseif ( $res->teamleader_id == get_current_user_id() && ($res->program_id == 6 || $res->program_id == 16)){

                                            $date = $groupArr->teamleader_date;
                                            $mainModul = $assessment_sheet;
                                            $download_all_files = $download_all;
//                                            $download_all_files_portfolio = $download_portfolio;
                                            $listeners_link = $listeners_link = $listeners_link_for_moder_teamleader;
                                            $user_role_FIO = "Тимлидер: ".nameUser($res->teamleader_id,5);
                                        }
                                        //

                                    }elseif( $res->trener_id == get_current_user_id() ){
                                        //
                                        $date = $groupArr->trener_date;
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
                                        $listeners_link = $listeners;

                                    }elseif( $res->expert_id == get_current_user_id() ){
                                        $date = $groupArr->expert_date;
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
                                            $mainModul = "";
                                        }else{
                                            $mainModul = $proforma;
                                        }
                                        $download_all_files = $download_all;
                                        $listeners_link = $listeners;

                                    }elseif ( $res->moderator_id == get_current_user_id() ){
                                        $date = $groupArr->moderator_date;
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
                                            $mainModul = "";
                                        }else{
                                            $mainModul = $proforma;
                                        }
                                        $listeners_link = $listeners;

                                    }elseif ( $res->teamleader_id == get_current_user_id() ){
                                        $date = $groupArr->teamleader_date;
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
                                            $mainModul = "";
                                        }else{
                                            $mainModul = $proforma;
                                        }
                                        $listeners_link = $listeners;

                                    }else{
                                        //$date = $groupArr->teamleader_date;
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
                                        //$independent_trainer = $hidden;
                                        $listeners_link = $hidden;
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
                                                <?php if($access == 1 && $res->program_id != 15): ?>
                                                    <b>Тренер:</b> <?= $res->t_surname ?> <?= $res->t_name ?> <?= $res->t_patronymic ?><br>
                                                    <b>Эксперт:</b> <?= $res->expert_surname ?> <?= $res->expert_name ?> <?= $res->expert_patronymic ?><br>
                                                    <b>Модератор:</b> <?= $res->moderator_surname ?> <?= $res->moderator_name ?> <?= $res->moderator_patronymic ?><br>
                                                    <?= $independent_trainer; ?>
                                                    <b>Тимлидер:</b> <?= $res->teamleader_surname ?> <?= $res->teamleader_name ?> <?= $res->teamleader_patronymic ?><br>
                                                <?php else: ?>
                                                    <b><?= $user_role_FIO ?></b>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <code class="text-medium">
                                                    <?=$link?>
                                                </code>
                                            </td>
                                            <td>
                                                <?= $listeners_link ?>
                                                <?= $mainModul ?>
                                                <?php if( $access == 1 || $res->expert_id == get_current_user_id() ): ?>
                                                    <?= $download_all_files ?>
                                                    <?= $download_all_files_portfolio ?>
                                                    <?php if(($res->program_id == 14 || $res->program_id == 6 || $res->program_id == 16) && $access == 1): ?>
                                                        <span class="badge style-primary-bright">
                                                                <a href="/export_to_word/?form=assessment_p_<?= $res->program_id ?>&&group=<?= $res->id ?>&tm=all" ><i class="fa fa-download"></i> Скачать все обоснования тимлидеров</a>
                                                            </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
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
                                    $res->potok = "";
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