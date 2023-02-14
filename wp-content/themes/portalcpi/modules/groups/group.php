<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="<?= bloginfo('template_url') ?>/assets/js/core/countDown.js"></script>
<?php

global $wpdb;

if(!$_GET['id']) exit('Нет данных!');

if($name_var = translateDir($_GET['id']) == 'name'){
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


$ResultsTr = groupInfo($_GET['id']);

if($ResultsTr->deleted == 1) exit('Del');
if($ResultsTr->active == 2) exit('Not active');
//if( getAccess(get_current_user_id())->access == 7 && (
//        $ResultsTr->moderator_id != get_current_user_id() ||
//        $ResultsTr->expert_id != get_current_user_id() ||
//        $ResultsTr->trener_id != get_current_user_id() ||
//        $ResultsTr->teamleader_id != get_current_user_id() ||
//        $ResultsTr->independent_trainer_id != get_current_user_id()
//    ) ){
//    echo "<br>";
//    alertStatus('warning','Нет доступа');
//exit();
//}

if($ResultsTr->moderator_id == get_current_user_id() && $ResultsTr->program_id == 7 && $ResultsTr->program_id == 14 ) alertStatus('warning', 'Доступ закрыт!', true);
//elseif($ResultsTr->teamleader_id == get_current_user_id() && $ResultsTr->program_id != 14) alertStatus('warning', 'Доступ закрыт!', true);

if(isset($_GET['id']) && isset($_GET['adduser']) && getAccess(get_current_user_id())->access == 1){
    $wpdb->insert( 'p_groups_users', [
        'id_user' => $_GET['adduser'],
        'id_group' => $_GET['id']
    ], [ '%d', '%d'] );
    echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id=' . $_GET['id'] .'" />';
}
if(isset($_GET['id']) && isset($_GET['exclude']) && getAccess(get_current_user_id())->access == 1){
    $wpdb->delete( 'p_groups_users', [
        'id_user'=>$_GET['exclude'],
        'id_group'=>$_GET['id'],
    ], [ '%d', '%d' ] );

//    $list_files = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_file WHERE group_id = %d AND user_id = %d ", $_GET['id'], $_GET['exclude']));
//    foreach ($list_files as $file){
//        unlink($file->filedir);
//    }
//    $wpdb->delete( 'p_file', [
//        'user_id'=>$_GET['exclude'],
//        'group_id'=>$_GET['id'],
//    ], [ '%d', '%d' ] );
    $wpdb->update('p_file', ['deleted' => current_time( 'Y-m-d H:i:s' )],[
        'user_id'=>$_GET['exclude'],
        'group_id'=>$_GET['id'],
    ],['%s'], [ '%d', '%d' ] );
    echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id=' . $_GET['id'] .'" />';

}

if ($linksGroup = $wpdb->get_results($wpdb->prepare('SELECT * FROM p_assessment_coding_group WHERE group_id = %d', $_GET['id']))) {

} else {

}

foreach ($linksGroup as $link) {
    if ($link->info == 'for_independent_trainer_id') $for_independent_trainer_id = $link->link;
    if ($link->info == 'for_moderator_id') $for_moderator_id = $link->link;
    if ($link->info == 'for_trener_p14') $for_trener_p14 = $link->link;
    if ($link->info == 'for_other_p14') $for_other_p14 = $link->link;
}
?>
<input type="hidden" name="" id="kz" value="<?= LANGID ?>">
<div class="row">
    <?php
    if( ($ResultsTr->independent_trainer_id == get_current_user_id() && $ResultsTr->program_id != 6) || ($ResultsTr->program_id == 7 && $ResultsTr->moderator_id == get_current_user_id()) ){
        echo '<div class="col-lg-12">
        <h1 class="text-primary">Группа скрыта</h1>
        </div>';
    }else{
        ?><div class="col-lg-12">
        <h1 class="text-primary"><?= GROUP ?>: <?= $ResultsTr->number_group ?></h1>
        </div>
        <div class="col-lg-12">
            <b><?= PERIOD ?>:</b> с <?= $ResultsTr->start_date ?> по <?= $ResultsTr->end_date ?><br>
            <b><?= PROGRAMM_NAME ?>:</b> <?= ($ResultsTr->lang_id == 1) ? $ResultsTr->name_kaz : $ResultsTr->p_name ?><br>
            <b><?= FULLNAME_TRENER ?>:</b> <?= $ResultsTr->surname ?> <?= $ResultsTr->name ?> <?= $ResultsTr->patronymic ?><br>
            <b><?= LANG_EDUCATION ?>:</b> <?= $ResultsTr->$lang_name ?><br>
            <h3 class="text-primary-dark"><?= TIME_LEFT ?>: <span id="display"></span></h3>
            <?php
            if(substr($ResultsTr->trener_date, 0,-9) == substr(dateTime(), 0,-9) && $ResultsTr->program_id == 7 && $ResultsTr->trener_id == get_current_user_id()){
                alertStatus('warning','<h3>'.TRENERS_NOTIFICATION_END_7.'</h3>');
            }
            if(substr($ResultsTr->independent_trainer_date, 0,-9) == substr(dateTime(), 0,-9) && $ResultsTr->program_id == 7 && $ResultsTr->independent_trainer_id == get_current_user_id()){
                alertStatus('warning','<h3>'.TRENERS_NOTIFICATION_END_7.'</h3>');
            }

            if(substr($ResultsTr->expert_date, 0,-9) == substr(dateTime(), 0,-9) && $ResultsTr->program_id == 7 && $ResultsTr->expert_id == get_current_user_id()){
                alertStatus('warning','<h3>'.EXPERTS_NOTIFICATION_END_7.'</h3>');
            }

            if(substr($ResultsTr->end_date, 0,-9) == substr(dateTime(), 0,-9)){
                alertStatus('warning','<h3>'.LISTENERS_NOTIFICATION_END_7.'</h3>');
            }
            ?>
            <script>
                const display<?= $q ?> = document.querySelector('#display<?= $q ?>');
                countDown('<?= $ResultsTr->end_date ?>', display<?= $q ?>);
            </script>
        </div><!--end .col -->

        <?php
    }
    ?>

<?php if($ResultsTr->trener_date < dateTime() && $ResultsTr->end_date > dateTime() && $ResultsTr->trener_id == get_current_user_id()): ?>
    <div>
        <?php alertStatus("warning", "<p class='lead'>Истекло время, предоставленное для работы</p>") ?>
    </div>

<?php elseif(  ($ResultsTr->program_id == 7 || $ResultsTr->program_id == 14) &&
    ($ResultsTr->trener_date > dateTime() && $ResultsTr->trener_id == get_current_user_id() )
    || ($ResultsTr->expert_id == get_current_user_id() && $ResultsTr->expert_date > dateTime() )
    || ($ResultsTr->moderator_id == get_current_user_id() && $ResultsTr->moderator_date > dateTime() )
    || ($ResultsTr->teamleader_id == get_current_user_id() && $ResultsTr->teamleader_date > dateTime() )
    || ($ResultsTr->independent_trainer_id == get_current_user_id() && $ResultsTr->independent_trainer_date > dateTime() )
    || ($ResultsTr->end_date > dateTime() && getAccess(get_current_user_id())->access == 4)
    || getAccess(get_current_user_id())->access == 1
    ): ?>
<?php
        //echo "<p>".$ResultsTr->trener_date . " > " . dateTime() . " && " . $ResultsTr->trener_id . " == " . get_current_user_id()
        ?>
        <div class="col-md-8"><hr>
            <article class="margin-bottom-xxl">
                <?php if($ResultsTr->program_id == 17):  ?>
                    <a href="/assessment/?z=sheet<?= $ResultsTr->program_id ?>&group=<?= $_GET['id'] ?>" class="btn btn-success">Проформа</a>
                <?php endif; ?>
                <?php if(
                        $ResultsTr->program_id == 7
                        || ($ResultsTr->program_id == 6 || $ResultsTr->program_id == 16) && (
                            $ResultsTr->expert_id != get_current_user_id()
                            && $ResultsTr->teamleader_id != get_current_user_id()
                            && $ResultsTr->moderator_id != get_current_user_id() )
                        || ($ResultsTr->program_id == 14 && (
                        $ResultsTr->teamleader_id != get_current_user_id()
                            && $ResultsTr->moderator_id != get_current_user_id()
                            && $ResultsTr->independent_trainer_id != get_current_user_id() )
                    ) ): ?>
                    <a href="/assessment/?z=sheet<?= ($ResultsTr->program_id == 14 || $ResultsTr->program_id == 6 || $ResultsTr->program_id == 16) ? $ResultsTr->program_id : "" ?>&group=<?= $_GET['id'] ?>" class="btn btn-success"><?= LIST_ASSESMENT_NAME ?></a>
                <?php elseif (getAccess(get_current_user_id())->access == 1 || getAccess(get_current_user_id())->access == 7 && $ResultsTr->program_id != 14 && $ResultsTr->program_id != 6 && $ResultsTr->program_id != 16): ?>

                    <?php if($ResultsTr->proforma_id == 0): ?>
<!--                        <a href="#" class="btn" disabled="">Проформа</a>-->
                    <?php else: ?>
                        <a href="/proforma/?form=<?= $ResultsTr->proforma_id ?>&group=<?= $_GET['id'] ?>" class="btn btn-success">Проформа</a>
                    <?php endif; ?>

                <?php endif; ?>
                <?php if(getAccess(get_current_user_id())->access == 1 || $ResultsTr->trener_id == get_current_user_id()): ?>
                    <a href="#" id="fileu" data-id="adduser" data-link="assessment/?z=usercreate&list_file_group_id=<?=$_GET['id']?>" data-toggle="modal" data-target="#Modal" class="btn btn-info"><?= ADD_USER ?></a>
                <?php endif; ?>
            </article>
        </div><!--end .col -->

        <?php if ( getAccess(get_current_user_id())->access == 1  ): ?>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-6">
                            <label class="form-control"><?= FIND_ADD_USER_SLUSHATEL ?></label>
                            <input type="text" class="form-control" id="ifio"><button class="btn btn-primary btn-xs" id="searchbtn"><?= FIND ?></button>
                        </div>
                        <div class="col-md-9 inform"></div>

                    </div>
                </div>
            </div>


            <?php if($ResultsTr->program_id == 7): ?>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            //printAll($_POST);
                            if(isset($_POST['enter_links'])){

                                if(isset($_POST['for_independent_trainer_id']) && $ResultsTr->independent_trainer_id != 0){
                                    $sql = "INSERT INTO p_assessment_coding_group (group_id,create_user_id,for_user_id,link,info) VALUES (%d,%d,%d,%s,%s) ON DUPLICATE KEY UPDATE link = %s";
                                    $sql = $wpdb->prepare($sql,$_GET['id'],get_current_user_id(),$ResultsTr->independent_trainer_id,$_POST['for_independent_trainer_id'],'for_independent_trainer_id',$_POST['for_independent_trainer_id']);
                                    $wpdb->query($sql);
                                }else{
                                    alertStatus('warning','В группе не указан независимый тренер',true);
                                }
                                if(isset($_POST['for_moderator_id']) && $ResultsTr->moderator_id != 0){
                                    $sql = "INSERT INTO p_assessment_coding_group (group_id,create_user_id,for_user_id,link,info) VALUES (%d,%d,%d,%s,%s) ON DUPLICATE KEY UPDATE link = %s";
                                    $sql = $wpdb->prepare($sql,$_GET['id'],get_current_user_id(),$ResultsTr->moderator_id,$_POST['for_moderator_id'],'for_moderator_id',$_POST['for_moderator_id']);
                                    $wpdb->query($sql);

                                }else{
                                    alertStatus('warning','В группе не указан модератор',true);
                                }
                                //echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id=' . $_GET['id'] .'" />';
                            }

                            ?>
                            <form method="post">
                                <div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1" aria-expanded="false">
                                    <span class="btn btn-default btn-xs">Ссылки на группу</span>
                                    <div class="tools">
                                        <a class="btn btn-icon-toggle"><i class="fa fa-angle-up"></i></a>
                                    </div>
                                </div>
                                <div id="accordion1-1" class="collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="form-group">
                                        <label for="textarea3">Ссылка на файлы группы для независимого тренера</label>
                                        <input type="text" name="for_independent_trainer_id" class="form-control"  required="" value="<?= $for_independent_trainer_id ?>" placeholder="Не заполнено">
                                    </div>
                                    <div class="form-group">
                                        <label for="textarea3">Ссылка на файлы группы для модератора</label>
                                        <input type="text" name="for_moderator_id" class="form-control" value="<?= $for_moderator_id ?>" placeholder="Не заполнено">
                                    </div>
                                    <input type="submit" name="enter_links" value="Сохранить" class="btn btn-info">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($ResultsTr->program_id == 14 || $ResultsTr->program_id == 6 || $ResultsTr->program_id == 16 || $ResultsTr->program_id == 17): ?>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <?php
//                            printAll($_POST);
                            if(isset($_POST['enter_links'])){

                                if(isset($_POST['for_trener_p14']) ){
                                    $sql = "INSERT INTO p_assessment_coding_group (group_id,create_user_id,for_user_id,link,info) VALUES (%d,%d,0,%s,%s) ON DUPLICATE KEY UPDATE link = %s";
                                    $sql = $wpdb->prepare($sql,$_GET['id'],get_current_user_id(),$_POST['for_trener_p14'],'for_trener_p14',$_POST['for_trener_p14']);
                                    $wpdb->query($sql);
                                }else{
                                    alertStatus('warning','Ошибка',true);
                                }

                                if(isset($_POST['for_other_p14']) ){
                                    $sql = "INSERT INTO p_assessment_coding_group (group_id,create_user_id,for_user_id,link,info) VALUES (%d,%d,0,%s,%s) ON DUPLICATE KEY UPDATE link = %s";
                                    $sql = $wpdb->prepare($sql,$_GET['id'],get_current_user_id(),$_POST['for_other_p14'],'for_other_p14',$_POST['for_other_p14']);
                                    $wpdb->query($sql);
                                }else{
                                    alertStatus('warning','Ошибка',true);
                                }


                                echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id=' . $_GET['id'] .'" />';
                            }

                            ?>
                            <form method="post">
                                <div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1" aria-expanded="false">
                                    <span class="btn btn-default btn-xs">Ссылка на группу</span>
                                    <div class="tools">
                                        <a class="btn btn-icon-toggle"><i class="fa fa-angle-up"></i></a>
                                    </div>
                                </div>
                                <div id="accordion1-1" class="collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="form-group">
                                        <label for="textarea3">Ссылка на файлы для тренера</label>
                                        <input type="text" name="for_trener_p14" class="form-control"  required="" value="<?= $for_trener_p14 ?>" placeholder="Не заполнено">
                                    </div>
                                    <div class="form-group">
                                        <label for="textarea3">Ссылка на файлы для эксперта, модератора и тимлидера</label>
                                        <input type="text" name="for_other_p14" class="form-control" value="<?= $for_other_p14 ?>" placeholder="Не заполнено">
                                    </div>
                                    <input type="submit" name="enter_links" value="Сохранить" class="btn btn-info">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php
                    if ( $ResultsTr->independent_trainer_id == get_current_user_id() && $ResultsTr->program_id == 7 ) echo "<h3>" . LINK_TO_FILE . ": <a href='{$for_independent_trainer_id}' class='text-primary' target='_blank'>" . $for_independent_trainer_id . "</a></h3>";
                    if ( $ResultsTr->moderator_id == get_current_user_id() && $ResultsTr->program_id == 7 ) echo "<h3>" . LINK_TO_FILE . ": <a href='{$for_moderator_id}' class='text-primary' target='_blank'>" . $for_moderator_id . "</a></h3>";
                    if ( $ResultsTr->program_id == 14 && $ResultsTr->trener_id == get_current_user_id())
                        echo "<h3> ".PLAGIAT_LINK." : <a href='{$for_trener_p14}' class='text-primary' target='_blank'>" . $for_trener_p14 . "</a></h3>";
                    if (  $ResultsTr->program_id == 17 && $ResultsTr->trener_id == get_current_user_id())
                        echo "<h3> Ссылка на OneDrive : <a href='{$for_trener_p14}' class='text-primary' target='_blank'>" . $for_trener_p14 . "</a></h3>";
                    if ( ($ResultsTr->program_id == 14) && (
                        $ResultsTr->moderator_id == get_current_user_id()
                        || $ResultsTr->independent_trainer_id == get_current_user_id()
                        || $ResultsTr->expert_id == get_current_user_id()
                        || $ResultsTr->teamleader_id == get_current_user_id()
                        )) echo "<h3>Ссылка на сравнительные таблицы по плагиату: <a href='{$for_other_p14}' class='text-primary' target='_blank'>" . $for_other_p14 . "</a></h3>";
                    if ( ($ResultsTr->program_id == 17) && (
                            $ResultsTr->moderator_id == get_current_user_id()
                            || $ResultsTr->independent_trainer_id == get_current_user_id()
                            || $ResultsTr->teamleader_id == get_current_user_id()
                            || $ResultsTr->expert_id == get_current_user_id()
                            || $ResultsTr->teamleader_id == get_current_user_id()
                        )) echo "<h3>Ссылка на OneDrive: <a href='{$for_other_p14}' class='text-primary' target='_blank'>" . $for_other_p14 . "</a></h3>";
                    ?>

                    <?php
                    if ( ($ResultsTr->program_id == 6 || $ResultsTr->program_id == 16) && $ResultsTr->trener_id == get_current_user_id())
                        echo "<h3> Ссылка для тренера : <a href='{$for_trener_p14}' class='text-primary' target='_blank'>" . $for_trener_p14 . "</a></h3>";
                    if ( ($ResultsTr->program_id == 6 || $ResultsTr->program_id == 16) && (
                            $ResultsTr->moderator_id == get_current_user_id()
                            || $ResultsTr->independent_trainer_id == get_current_user_id()
                            || $ResultsTr->expert_id == get_current_user_id()
                            || $ResultsTr->teamleader_id == get_current_user_id()
                        )) echo "<h3>Ссылка: <a href='{$for_other_p14}' class='text-primary' target='_blank'>" . $for_other_p14 . "</a></h3>";
                    ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <?= ( getAccess(get_current_user_id())->access == 1 && $ResultsTr->program_id == 7) ? '<th>
                                    <i class="fa fa-info-circle fa-fw text-info" data-toggle="tooltip" data-placement="right" data-original-title="Прикрепить сотрудника ЦПИ" style="cursor: pointer"></i>
                                </th>' : "" ?>
                                <th><?= FIO ?></th>
                                <th>Email</th>
                                <th><?= DATE_REGISTRATION ?></th>

                                <th><?= NUM_DOWNLOADED_FILES ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rubric_name = ASSESSMENT_SECOND[2];
                            if($ResultsTr->moderator_id == get_current_user_id() && ($ResultsTr->program_id == 14 )){ //если модератор выводим только тех у кого эксперт поставил 4(неудовлетварительно)
                                $results = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, p.start_date, p.end_date, u.surname, u.name, u.patronymic, u.email, r.grading_solution
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_assessment_rubric r ON r.listener_id = g.id_user AND r.group_id = %d AND r.create_user_id = %d 
                                LEFT OUTER JOIN p_groups p ON p.id = g.id_group
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user WHERE r.grading_solution = 4/*неуд*/ AND g.id_group = %d
                                ORDER BY u.surname, u.name, u.patronymic", $_GET['id'], $ResultsTr->expert_id, $_GET['id'] ));
                            }elseif($ResultsTr->independent_trainer_id == get_current_user_id() && ($ResultsTr->program_id == 14 || $ResultsTr->program_id == 17)){ //если модератор 2 выводим только тех у кого эксперт поставил 4(неудовлетварительно)  а модератор1 поставил 2(удовлетварительно)
//                                $results = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, p.start_date, p.end_date, u.surname, u.name, u.patronymic, u.email, r.grading_solution grading_solution_expert, t.grading_solution grading_solution_moderator2
//                                FROM p_groups_users g
//                                INNER JOIN p_assessment_rubric r ON r.listener_id = g.id_user AND r.group_id = g.id_group AND r.create_user_id = %d /*expert*/ AND r.grading_solution = 4 /*неуд*/
//                                INNER JOIN p_assessment_rubric t ON t.listener_id = g.id_user AND t.group_id = g.id_group AND t.create_user_id = %d /*moderator*/ AND t.grading_solution = 2 /*хорошо*/
//                                INNER JOIN p_groups p ON p.id = g.id_group
//                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
//                                WHERE g.id_group = %d /*группа*/
//                                ORDER BY u.surname, u.name, u.patronymic", $ResultsTr->expert_id, $ResultsTr->moderator_id, $_GET['id'] )); // independent_trainer_id это модератор2

                                $results = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, p.start_date, p.end_date, u.surname, u.name, u.patronymic, u.email
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_groups p ON p.id = g.id_group
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user WHERE g.id_group = %d ORDER BY u.surname, u.name, u.patronymic", $_GET['id'] ));

                            }elseif($ResultsTr->teamleader_id == get_current_user_id() &&  $ResultsTr->program_id == 17){ //если модератор 2 выводим только тех у кого эксперт поставил 4(неудовлетварительно)  а модератор1 поставил 2(удовлетварительно)
//                                $results = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, p.start_date, p.end_date, u.surname, u.name, u.patronymic, u.email, r.grading_solution grading_solution_expert, t.grading_solution grading_solution_moderator2
//                                FROM p_groups_users g
//                                INNER JOIN p_assessment_rubric r ON r.listener_id = g.id_user AND r.group_id = g.id_group AND r.create_user_id = %d /*expert*/ AND r.grading_solution = 4 /*неуд*/
//                                INNER JOIN p_assessment_rubric t ON t.listener_id = g.id_user AND t.group_id = g.id_group AND t.create_user_id = %d /*moderator*/ AND t.grading_solution = 2 /*хорошо*/
//                                INNER JOIN p_groups p ON p.id = g.id_group
//                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user
//                                WHERE g.id_group = %d /*группа*/
//                                ORDER BY u.surname, u.name, u.patronymic", $ResultsTr->expert_id, $ResultsTr->moderator_id, $_GET['id'] )); // independent_trainer_id это модератор2

                                $results = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, p.start_date, p.end_date, u.surname, u.name, u.patronymic, u.email
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_groups p ON p.id = g.id_group
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user WHERE g.id_group = %d ORDER BY u.surname, u.name, u.patronymic", $_GET['id'] ));

                            }elseif($ResultsTr->teamleader_id == get_current_user_id() && ($ResultsTr->program_id == 14 || $ResultsTr->program_id == 17)){
                                /*Тимлидер видит слушателей получивших неудовлетворительно, видит лист оценивания и рубрики Эксперта Модератора и Тренера,
                            выбирает оценку и рубрику либо эксперта либо модератора либо Модератора 2(если такой был) далее может изменять рубрику дописывать что то*/
                                $results = $wpdb->get_results($s=$wpdb->prepare("SELECT a.id, p.start_date, p.end_date, 
                                e.date_reg, f.surname, f.name, f.patronymic, f.email, e.id_user
                                FROM  p_assessment_rubric a
                                LEFT OUTER JOIN p_groups_users e ON e.id_group = a.group_id AND a.listener_id = e.id_user
                                LEFT OUTER JOIN p_user_fields f ON f.user_id = a.listener_id
                                LEFT OUTER JOIN p_groups p ON p.id = a.group_id
                                WHERE a.group_id = %d AND a.create_user_id = %d AND a.grading_solution = 4/*неуд*/", $_GET['id'], $ResultsTr->expert_id ));
                            }elseif( ($ResultsTr->expert_id == get_current_user_id() || $ResultsTr->teamleader_id == get_current_user_id()) &&  $ResultsTr->program_id == 6){
                                /*Эксперт видит слушателей получивших неудовлетворительно и пороговый, видит лист оценивания и рубрики Эксперта Модератора и Тренера,
                            выбирает оценку и рубрику либо эксперта либо модератора либо Модератора 2(если такой был) далее может изменять рубрику дописывать что то*/
                                $results = $wpdb->get_results($s=$wpdb->prepare("SELECT a.id, p.start_date, p.end_date, 
                                e.date_reg, f.surname, f.name, f.patronymic, f.email, e.id_user
                                FROM  p_assessment_rubric a
                                LEFT OUTER JOIN p_groups_users e ON e.id_group = a.group_id AND a.listener_id = e.id_user
                                LEFT OUTER JOIN p_user_fields f ON f.user_id = a.listener_id
                                LEFT OUTER JOIN p_groups p ON p.id = a.group_id
                                WHERE a.group_id = %d AND (a.create_user_id = %d OR a.create_user_id = %d) AND a.grading_solution IN (3,4)/*неуд*/ GROUP BY  e.id_user", $_GET['id'], $ResultsTr->trener_id, $ResultsTr->independent_trainer_id ));

//                                $results2 = $wpdb->get_results($s=$wpdb->prepare("SELECT a.id, p.start_date, p.end_date,
//                                e.date_reg, f.surname, f.name, f.patronymic, f.email, e.id_user
//                                FROM  p_assessment_rubric a
//                                LEFT OUTER JOIN p_groups_users e ON e.id_group = a.group_id AND a.listener_id = e.id_user
//                                LEFT OUTER JOIN p_user_fields f ON f.user_id = a.listener_id
//                                LEFT OUTER JOIN p_groups p ON p.id = a.group_id
//                                WHERE a.group_id = %d AND a.create_user_id = %d AND a.grading_solution IN (3,4)/*неуд*/", $_GET['id'], $ResultsTr->independent_trainer_id ));
////                                array_push($results, $results2);
//
//                                foreach ($results2 as $res){
//                                    $results[] = $res;
//                                }
//                                printAll($results);
                            }else{
                                $results = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, p.start_date, p.end_date, u.surname, u.name, u.patronymic, u.email
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_groups p ON p.id = g.id_group
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user WHERE g.id_group = %d ORDER BY u.surname, u.name, u.patronymic", $_GET['id'] ));
                            }


                            $linksGroupAll = $wpdb->get_results($wpdb->prepare('SELECT * FROM p_assessment_coding_group WHERE group_id = %d', $_GET['id']));

                            //if(!$results) echo'<meta http-equiv="refresh" content="0;url=/groups/?z=list" />';
                            ?>
                            <?php foreach($results as $res): ?>
                                <?php
                                if( $code = $wpdb->get_row($wpdb->prepare("SELECT code, linktext FROM p_assessment_coding_user WHERE group_id = %d AND listener_id = %d", $_GET['id'], $res->id_user)) ) $codetext = $code->code;
                                else $codetext = "Код не записан";

                                if( $ResultsTr->program_id == 7 ){
                                    $rubriclink7 = '<a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=rubric&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">
                                                '.$rubric_name.'
                                            </a>';
                                    $file7 = '<a href="#" id="fileu" data-id="' . $res->id_user . '" data-link="portal_server/?list_file_group_id=' . $_GET['id'] . '" data-toggle="modal" data-target="#Modal" class="btn btn-success">
                                             ' . $num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM p_file WHERE group_id = %d AND user_id = %d", $_GET['id'], $res->id_user)) . '</a>';
                                    if($ResultsTr->independent_trainer_id == get_current_user_id()){
                                        $res->surname = $codetext;
                                        $res->name = '';
                                        $res->patronymic = '';
                                        $res->email = '';
                                        $res->date_reg = '';
                                        $filelink = "";//"<br>".LINK_TO_FILE.": <a href='{$for_independent_trainer_id}' class='text-primary' target='_blank'>{$for_independent_trainer_id}</a><br>";
                                        $rubric = $rubriclink7;
                                    } elseif ($ResultsTr->moderator_id == get_current_user_id() ){
                                        $res->surname = $codetext;
                                        $res->name = '';
                                        $res->patronymic = '';
                                        $res->email = '';
                                        $res->date_reg = '';
                                        $filelink = "";//"<br>".LINK_TO_FILE.": <a href='{$for_moderator_id}' class='text-primary' target='_blank'>{$for_moderator_id}</a><br>";
                                        $rubric = $rubriclink7;
                                    } elseif ($ResultsTr->expert_id == get_current_user_id()){
                                        $res->surname;
                                        $res->name;
                                        $res->patronymic;
                                        $res->email;
                                        $res->date_reg;
                                        $filelink = $file7;
                                        $rubric = $rubriclink7;
                                    } elseif ($ResultsTr->trener_id == get_current_user_id()){
                                        $res->surname;
                                        $res->name;
                                        $res->patronymic;
                                        $res->email;
                                        $res->date_reg;
                                        $filelink = $file7;
                                        $rubric = $rubriclink7;
                                    } elseif ($ResultsTr->teamleader_id == get_current_user_id()){
                                        $res->surname;
                                        $res->name;
                                        $res->patronymic;
                                        $res->email;
                                        $res->date_reg;
                                        $filelink = $file7;
                                        $rubric = '<a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=rubricall&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">
                                                '.$rubric_name.'
                                            </a>';
                                    } elseif ( getAccess(get_current_user_id())->access == 1 ){
                                        $res->surname;
                                        $res->name;
                                        $res->patronymic;
                                        $res->email;
                                        $res->date_reg;
                                        $filelink = $file7;
                                        $rubric = '<a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=rubricall&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">
                                                '.$rubric_name.'
                                            </a>
                                            <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=coding&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">
                                                Код
                                            </a>';
                                    }
                                } elseif ( $ResultsTr->program_id == 14 || $ResultsTr->program_id == 17 || $ResultsTr->program_id == 6  ) // ОТОБРАЖЕНИЕ ВСЕХ РУБРИК ДЛЯ ПРОГРАММЫ
                                {
                                    $resRubricsql = "SELECT * FROM p_assessment_rubric WHERE group_id = %d AND create_user_id = %d AND listener_id = %d";
                                    $res->surname;
                                    $res->name;
                                    $res->patronymic;
                                    $res->email;
                                    $res->date_reg;
                                    $btnName = "";
                                    $filelink = '<a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="portal_server/?list_file_group_id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-success">
                                             '. $num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM p_file WHERE group_id = %d AND user_id = %d", $_GET['id'], $res->id_user)) .'</a>';
                                    $trenerassessment = ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=trenerassessment' . $ResultsTr->program_id . '&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary btn-xs">
                                                Оценка <br>тренера
                                            </a>';
                                    $rubriclink = ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=assessment_p_' . $ResultsTr->program_id . '&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary-dark btn-xs">Оценка ';
                                    $rubriclink2 = ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=rubric_p_' . $ResultsTr->program_id . '&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary btn-xs">
                                                Рубрика';

                                    $rubric = "";

                                    if($ResultsTr->trener_id == get_current_user_id()){
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->trener_id, $res->id_user));

                                        $rubric = $rubriclink . "<br>тренера</a>";
                                        if( $resRubric->grading_solution == 4 || $resRubric->grading_solution == 3){
                                            $rubric .= $rubriclink2 . "<br>тренера</a>";
                                        }
                                    }elseif( $ResultsTr->expert_id == get_current_user_id() && $ResultsTr->program_id == 14 ){
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->expert_id, $res->id_user));

                                        $rubric = $trenerassessment;
                                        $rubric .= $rubriclink . "<br>эксперта</a>";
                                        if( $resRubric->grading_solution == 4){
                                            $rubric .= $rubriclink2 . "<br>эксперта</a>";

                                        }

                                    }elseif( $ResultsTr->expert_id == get_current_user_id() && $ResultsTr->program_id == 17){
                                        $rubric = $rubriclink . "<br>эксперта</a>";
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->expert_id, $res->id_user));
                                        if(
                                            $resRubric->section_a_grade == 4 ||
                                            $resRubric->section_b_grade == 4 ||
                                            $resRubric->section_c_grade == 4 ||
                                            $resRubric->section_d_grade == 4 ||
                                            $resRubric->section_e_grade == 4
                                        ) {
                                            $rubric .= str_replace("Рубрика","",$rubriclink2) . "Обоснование<br> эксперта</a>";
                                        }
                                    }elseif( $ResultsTr->moderator_id == get_current_user_id() && $ResultsTr->program_id == 17){
                                        $rubric = $rubriclink . "<br>модератора</a>";
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->moderator_id, $res->id_user));
                                        if(
                                            $resRubric->section_a_grade == 4 ||
                                            $resRubric->section_b_grade == 4 ||
                                            $resRubric->section_c_grade == 4 ||
                                            $resRubric->section_d_grade == 4 ||
                                            $resRubric->section_e_grade == 4
                                        ) {
                                            $rubric .= str_replace("Рубрика","",$rubriclink2) . "Обоснование<br> эксперта</a>";
                                        }
                                    }elseif( $ResultsTr->teamleader_id == get_current_user_id() && $ResultsTr->program_id == 17){
                                        $rubric = $rubriclink . "<br>тимлидера</a>";
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->teamleader_id, $res->id_user));
                                        if(
                                            $resRubric->section_a_grade == 4 ||
                                            $resRubric->section_b_grade == 4 ||
                                            $resRubric->section_c_grade == 4 ||
                                            $resRubric->section_d_grade == 4 ||
                                            $resRubric->section_e_grade == 4
                                        ) {
                                            $rubric .= str_replace("Рубрика","",$rubriclink2) . "Обоснование<br> тимлидера</a>";
                                        }
                                    }elseif( $ResultsTr->expert_id == get_current_user_id() && ($ResultsTr->program_id == 6 || $ResultsTr->program_id == 16)){
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->expert_id, $res->id_user));

                                        $rubric = $trenerassessment;
                                        $rubric .= $rubriclink2 . "<br>эксперта</a>";
//                                        $rubric .= $rubriclink . "<br>эксперта</a>";
//                                        if( $resRubric->grading_solution == 4){
//                                            $rubric .= $rubriclink2 . "<br>эксперта</a>";
//
//                                        }

                                    }elseif( $ResultsTr->moderator_id == get_current_user_id() ){
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->moderator_id, $res->id_user));

                                        $rubric = ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=trenerassessment14&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary btn-xs">
                                                Оценка <br>тренера и эксперта
                                            </a>';
//                                        $rubric .= $rubriclink . "<br>модератора</a>";
                                        $rubric .= $rubriclink2  . "<br>модератора</a>";

                                    }elseif( $ResultsTr->independent_trainer_id == get_current_user_id() && ($ResultsTr->program_id == 14 || $ResultsTr->program_id == 17)){ // Это наблюдатель (модератор2)
                                        $trenerassessment = ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=trenerassessment'.$ResultsTr->program_id.'&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary btn-xs">
                                                Оценка <br>тренера
                                            </a>';
//                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->independent_trainer_id, $res->id_user));

                                        $rubric = $trenerassessment;
//                                        if( $resRubric->grading_solution == 4){
//                                            $rubric .= $rubriclink2  . "<br>модератора</a>";
//
//                                        }
                                    }elseif( $ResultsTr->independent_trainer_id == get_current_user_id() && ($ResultsTr->program_id == 6 || $ResultsTr->program_id == 16)){ // Это независимый тренер
                                        $trenerassessment = ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=trenerassessment'.$ResultsTr->program_id.'&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary btn-xs">
                                                Оценка <br>тренера
                                            </a>';
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->independent_trainer_id, $res->id_user));

                                        $rubric = $rubriclink . "<br>независимого тренера</a>";
//                                        $rubric .= $rubriclink2  . "<br>независимого тренера</a>";
                                    }elseif( $ResultsTr->teamleader_id == get_current_user_id() && ($ResultsTr->program_id == 14 || $ResultsTr->program_id == 17)){
                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->teamleader_id, $res->id_user));

                                        $rubric = ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=for_teamleader_p_' . $ResultsTr->program_id . '&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">
                                                Оценка 
                                            </a>';

                                        $teamleader_info = $wpdb->get_row($s=$wpdb->prepare("SELECT a.rubric_id, a.create_user_id teamleaderid, a.info, b.listener_id, b.create_user_id
                                            FROM p_assessment_rubric_for_teamleader a
                                            LEFT OUTER JOIN p_assessment_rubric b ON b.id = a.rubric_id
                                            WHERE a.create_user_id = %d AND a.group_id = %d AND b.listener_id = %d", get_current_user_id(), $_GET['id'], $res->id_user) );
                                        if(!empty($teamleader_info)){
                                            $rubric .= $rubriclink2  . "<br>тимлидера</a>";
                                        }else{
                                            $rubric .= " <button type='button' class='btn ink-reaction btn-raised btn-default-bright btn-xs disabled' data-toggle='tooltip' data-placement='top' data-original-title='Выберите оценку'>Рубрика <br>тимлидера</button>";
                                        }


                                    } elseif( $ResultsTr->teamleader_id == get_current_user_id() && ($ResultsTr->program_id == 6 || $ResultsTr->program_id == 16)){

                                        $trenerassessment = ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=trenerassessment' . $ResultsTr->program_id . '&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary btn-xs">
                                                Оценка <br>тренера и эксперта
                                            </a>';
                                        $rubric .= $trenerassessment;

//                                        $resRubric = $wpdb->get_row($wpdb->prepare($resRubricsql, $_GET['id'], $ResultsTr->teamleader_id, $res->id_user));

                                        $rubric .= ' <a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=rubric_p_' . $ResultsTr->program_id . '&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">
                                                Тимлидер 
                                            </a>';



                                    } elseif(getAccess(get_current_user_id())->access == 1){

                                        $rubric = '<a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="assessment/?z=rubricall_p_' . $ResultsTr->program_id . '&id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-primary">
                                                '.$rubric_name.'
                                            </a>';
                                    }
                                }  else {

                                    $res->surname;
                                    $res->name;
                                    $res->patronymic;
                                    $res->email;
                                    $res->date_reg;
                                    $filelink = '<a href="#" id="fileu" data-id="'.$res->id_user.'" data-link="portal_server/?list_file_group_id='.$_GET['id'].'" data-toggle="modal" data-target="#Modal" class="btn btn-success">
                                             '. $num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM p_file WHERE group_id = %d AND user_id = %d", $_GET['id'], $res->id_user)) .'</a>';

                                }

                                ?>

                            <?= ( getAccess( get_current_user_id() )->access == 1 && $ResultsTr->program_id == 7) ? '<form method="POST" action="/assessment/?z=appointed7">' : "" ?>
                                <tr>
                                    <td><?= ++$i ?></td>
                                    <?= ( getAccess( get_current_user_id() )->access == 1 && $ResultsTr->program_id == 7) ? "<td><input type='checkbox' name='addmoder[{$res->id_user}]'></td>" : "" ?>
                                    <td>
                                        <h4><?= $res->surname ?> <?= $res->name ?> <?= $res->patronymic ?></h4>
                                        <?= ( getAccess(get_current_user_id())->access == 1 ) ? " <a href='/groups/?z=group&id={$_GET['id']}&exclude={$res->id_user}' class='text-danger' onclick=\"return confirm('Вы действительно хотите исключить?');\">Исключить</a>
                                        <br>$codetext " : null ?>

                                    </td>
                                    <td><?= $res->email ?></td>
                                    <td><?= $res->date_reg ?></td>
                                    <td>
                                        <?= $filelink ?>
                                        <?= $rubric ?>

                                    </td>
                                </tr>
                                <?php unset($key); endforeach; ?>
                            </tbody>
                        </table>
                    </div><!--end .table-responsive -->
                </div><!--end .card-body -->
            </div><!--end .card -->
        </div><!--end .col -->



    <?php else: ?>
        <div>
            <?php alertStatus("warning", "<p class='lead'>Истекло время, предоставленное для работы</p>") ?>
        </div>
    <?php endif; ?>

</div>


    <?php if ( getAccess(get_current_user_id())->access == 1 && $ResultsTr->program_id == 7 ): // Присвоить для каждого слушателя независимого тренера, модератора и эксперта ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-head">
                        <header>Прикрепление к слушателю независимого тренера, модератора или эксперта</header>
                    </div>
                    <div class="card-body">
                        <!-- BEGIN SELECT2 -->
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Ссылка</label>
                                            <input type="text" class="form-control" name="link" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Закрепление сотрудника ЦПИ</label>
                                            <select class="form-control select2-list" data-placeholder="Независимый тренер, модератор или эксперт" name="appointed_user_id">
                                                <option></option>
                                                <?php
                                                foreach ( $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '4';") as $trener ) {
                                                    echo "<option value='{$trener->user_id}'> {$trener->surname} {$trener->name} {$trener->patronymic} </option>";
                                                }

                                                ?>
                                                <?php
                                                foreach ( $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7';") as $moder ) {
                                                    echo "<option value='{$moder->user_id}'> {$moder->surname} {$moder->name} {$moder->patronymic} </option>";
                                                }

                                                ?>
                                            </select>
                                        </div>
                                        <input type="hidden" name="group_id" value="<?= $_GET['id'] ?>">
                                        <input type="submit" class="btn btn-success" value="Сохранить">
                                    </div><!--end .card-body -->
                                </div><!--end .card -->
                            </div><!--end .col -->
                    </div>
                </form>
                        <h2>Прикрепленные слушатели:</h2>
                        <form method="POST" action="/assessment/?z=appointed7">
                        <table class="table table-striped no-margin">
                            <tr>
                                <th>#</th>
                                <th>ФИО слушателя</th>
                                <th>Прикрепленный сотрудник</th>
                                <th>Ссылка</th>
                                <th>Дата создания</th>
                            </tr>
                            <?php
                            $q = 0;
                            foreach ( $wpdb->get_results( $wpdb->prepare("SELECT a.id, a.user_id, b.surname, b.name u_name, b.patronymic, a.group_id,  c.surname f_surname , c.name f_name, c.patronymic f_patronymic, a.date_create, a.appointed_user_id, a.link
FROM p_appointed7 a
LEFT JOIN p_user_fields b ON a.user_id = b.user_id 
LEFT JOIN p_user_fields c ON a.appointed_user_id = c.user_id 
WHERE a.group_id = %d", $_GET['id'])  ) as $result) {
                                $q++;
                                if ( $result->appointed_user_id == $ResultsTr->independent_trainer_id ){
                                    $role = "Независимый тренер";
                                } elseif ($result->appointed_user_id == $ResultsTr->moderator ) {
                                    $role = "Модератор";
                                } elseif ($result->appointed_user_id == $ResultsTr->expert ) {
                                    $role = "Эксперт";
                                }
                                echo "
                                <tr>
                                    <td>$q</td>
                                    <td> <input type='checkbox' data-appointed name='appointed_for_del[{$result->user_id}]' value='{$result->appointed_user_id}'> {$result->surname} {$result->u_name} {$result->patronymic}</td>
                                    <td>{$result->f_surname} {$result->f_name} {$result->f_patronymic}</td>
                                    <td>{$result->link}</td>
                                    <td>{$result->date_create}</td>
                                </tr>";
                            }
                            ?>
                            <tr>
                                <td></td>
                                <td><h4><input type="checkbox" id="select-all"> Выбрать все</h4></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table><br>
                            <input type="hidden" name="group_id" value="<?= $_GET['id'] ?>">
                            <input type="submit" class="btn btn-info" value="Удалить отмеченые">
                        </form>
                </div>
            </div>
        </div>
        <!-- END SELECT2 -->
    <?php endif; ?>


<form id="formreg">
    <div class="modal fade" id="Modal" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="box"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close" class="btn btn-default" data-dismiss="modal"><?= ASSESSMENT_SECOND[1] ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</form>


<div class="modal fade" id="textModal" tabindex="-1" role="dialog" aria-labelledby="textModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php $codes = $wpdb->get_row($s=$wpdb->prepare("SELECT code, linktext FROM p_assessment_coding_user WHERE group_id = %d AND listener_id = %d", $_GET['id'], $_GET['rubric_user_id'])); ?>
                <h4 class="modal-title" id="textModalLabel">Загрузить сравнительную таблицу. <?= LISTENER_TEXT ?>:
                    <?php if( $ResultsTr->program_id == 7 && ( $ResultsTr->independent_trainer_id == get_current_user_id() || $ResultsTr->moderator_id == get_current_user_id()) ){
                        echo $codes->code;
                    }else{
                        echo nameUser($_GET['rubric_user_id'], 5);
                    }?></h4>
            </div>
            <?php if($rubric_file = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_assessment_attached_file WHERE deleted = 0 AND rubric_user_id = %d AND user_id = %d AND group_id = %d AND category_id = %d",
                $_GET['rubric_user_id'], get_current_user_id(), $_GET['id'], $_GET['category_id'])) ): ?>
                <?php //printAll($rubric_file);?>

                <div class="modal-body">
                    <a href="/server_file/?assessment_sheet_file=<?= $rubric_file->id ?>&assessment_sheet=1" class="btn btn-info">Скачать файл <?= $rubric_file->file_name ?></a>
                    <a href="/assessment/?z=addfile&action=delete&id=<?= $rubric_file->id ?>&group=<?= $_GET['id'] ?>&category_id=3" class="btn btn-warning">Удалить файл <?= $rubric_file->file_name ?></a>
                </div>
                <div class="modal-footer">
                    <a href="/groups/?z=group&id=<?= $_GET['id'] ?>" class="btn btn-default">Закрыть</a>
                </div>
            <?php else: ?>
                <form enctype="multipart/form-data" class="form-horizontal" method="POST" action="/assessment/?z=addfile">
                    <div class="modal-body">
                        <p>
                            <input type="file" id="uploadInput" name="file" class="form-control" accept=".docx,.doc,.pptx,.ppt,.pdf">
                        </p>
                    </div>
                    <div class="modal-footer">
                        <a href="/groups/?z=group&id=<?= $_GET['id'] ?>" class="btn btn-default"><?= ASSESSMENT_SECOND[1] ?></a>
                        <button type="submit" class="btn btn-success"><?= SAVE_TEXT ?></button>
                    </div>
                    <input type="hidden" name="category_id" value="3">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="group" value="<?= $_GET['id'] ?>">
                    <input type="hidden" name="rubric_user_id" value="<?= $_GET['rubric_user_id'] ?>">
                </form>
            <?php endif; ?>


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<?php if($_GET['download_rubric'] == 1): ?>
    <script type="text/javascript">
        window.onload = function() {
            $('#textModal').modal('show');
        };
    </script>
<?php endif; ?>

<div class="modal fade" id="tmModal" tabindex="-1" role="dialog" aria-labelledby="textModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="textModalLabel">Внеcение корректировок в обоснование модератора: <?php nameUser($_GET['moderatorid']) ?> </h4>
            </div>
                <div class="modal-body">
                    <?php include_once '/var/www/html/wp-content/themes/portalcpi/modules/assessment/tm7.php'; ?>
                </div>
                <div class="modal-footer">
                    <a href="/groups/?z=group&id=<?= $_GET['id'] ?>" class="btn btn-default">Закрыть</a>
                </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<?php if($_GET['tmModal'] == 1): ?>
    <script type="text/javascript">
        window.onload = function() {
            $('#tmModal').modal('show');
        };
    </script>
<?php endif; ?>

<script>

    <?php if(getAccess(get_current_user_id())->access == 1 && $ResultsTr->program_id == 7): ?>
    document.getElementById('select-all').onclick = function() {
        var checkboxes = document.querySelectorAll('[data-appointed]');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
    <?php endif; ?>

    const allData = {};
    const fileu = document.querySelectorAll("#fileu"),
        boxdiv = document.querySelector('.box'),
        formreg = document.querySelector('#formreg')
    close = document.querySelector("#close");
    const message = {
        loading: `${document.location.origin}/wp-content/themes/portalcpi/assets/img/spinner.svg`,
        success: "Спасибо, все данные внесены",
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

    formreg.addEventListener('submit', saveOn);
    function saveOn(event) {
        event.preventDefault();
        //console.log(event);
        var target = event.currentTarget;
        const boxcoding = document.querySelector('.boxcoding'),
            savecodebtn = document.querySelector("#savecodebtn");

        let datacoding = "";
        if(allData.fileuserdata === 'adduser'){
            datacoding = document.querySelectorAll("[data-user]");
        }else{
            datacoding = document.querySelectorAll("[data-assessment]");
        }

        datacoding.forEach(item => {

            let keyName = item.getAttribute("name");
        allData[[keyName]] = item.value;

    });
        //console.log(allData);
        const request = new XMLHttpRequest();
        request.open('POST', `${document.location.origin}/${allData.link}`);
        request.setRequestHeader('Content-type', 'application/json');
        const json = JSON.stringify(allData);
        request.send(json);

        const spinner = document.createElement('img');
        spinner.src = message.loading;
        boxcoding.append(spinner);
        request.addEventListener('load', () => {
            if (request.status === 200){
            boxcoding.innerHTML = request.response;
            spinner.remove();
            allData.code = "";
            allData.section_a_grade = "";
            allData.section_a_description = "";
            allData.section_b_grade = "";
            allData.section_b_description = "";
            allData.section_c_grade = "";
            allData.section_c_description = "";
            allData.action = "";
            //for (var member in allData) delete allData[member];
        } else {
            spinner.remove();
            boxcoding.textContent = message.failure;
            allData.code = "";
            allData.section_a_grade = "";
            allData.section_a_description = "";
            allData.section_b_grade = "";
            allData.section_b_description = "";
            allData.section_c_grade = "";
            allData.section_c_description = "";
            allData.action = "";

        }
    });
    }


    function saveData(event) {

        function checkGrade() {

            <?php if ($ResultsTr->program_id == 17): ?>
            const
                n1 = document.querySelector('#n1'),
                n2 = document.querySelector('#n2'),
                n3 = document.querySelector('#n3'),
                n4 = document.querySelector('#n4'),
                n5 = document.querySelector('#n5'),
                n6 = document.querySelector('#n6'),
                n7 = document.querySelector('#n7'),
                n8 = document.querySelector('#n8'),
                n9 = document.querySelector('#n9'),
                n10 = document.querySelector('#n10'),
                n11 = document.querySelector('#n11'),
                n12 = document.querySelector('#n12'),
                n13 = document.querySelector('#n13'),
                n14 = document.querySelector('#n14'),
                n15 = document.querySelector('#n15'),
                n16 = document.querySelector('#n16'),
                n17 = document.querySelector('#n17'),
                n18 = document.querySelector('#n18'),
                n19 = document.querySelector('#n19'),
                n20 = document.querySelector('#n20'),
                n21 = document.querySelector('#n21'),
                n22 = document.querySelector('#n22'),
                n23 = document.querySelector('#n23'),
                n24 = document.querySelector('#n24'),
                n25 = document.querySelector('#n25'),
                n26 = document.querySelector('#n26'),
                n27 = document.querySelector('#n27'),
                n28 = document.querySelector('#n28'),
                n29 = document.querySelector('#n29'),
                n30 = document.querySelector('#n30'),
                n31 = document.querySelector('#n31'),
                textarea = document.querySelector('#textarea3');

            addLis(n1,textarea);
            addLis(n2,textarea);
            addLis(n3,textarea);
            addLis(n4,textarea);
            addLis(n5,textarea);
            addLis(n6,textarea);
            addLis(n7,textarea);
            addLis(n8,textarea);
            addLis(n9,textarea);
            addLis(n10,textarea);
            addLis(n11,textarea);
            addLis(n12,textarea);
            addLis(n13,textarea);
            addLis(n14,textarea);
            addLis(n15,textarea);
            addLis(n16,textarea);
            addLis(n17,textarea);
            addLis(n18,textarea);
            addLis(n19,textarea);
            addLis(n20,textarea);
            addLis(n21,textarea);
            addLis(n22,textarea);
            addLis(n23,textarea);
            addLis(n24,textarea);
            addLis(n25,textarea);
            addLis(n26,textarea);
            addLis(n27,textarea);
            addLis(n28,textarea);
            addLis(n29,textarea);
            addLis(n30,textarea);
            addLis(n31,textarea);

            function addLis($node, textarea){
                $node.addEventListener('click', () => {
                    textarea.textContent += $node.textContent;
                })
            }
            <?php endif;?>


            const allSelect = document.querySelectorAll("#select2");
            let section_a_grade = document.querySelector('[name="grade_a"]');
            let section_b_grade = document.querySelector('[name="grade_b"]');
            let section_c_grade = document.querySelector('[name="grade_c"]');
            let grading_solution = document.querySelector('[name="grade_solution"]');
            let checkButton = document.getElementById('checkButton');

            const cbox = document.querySelectorAll(".box");

            for (let i = 0; i < allSelect.length; i++) {
                allSelect[i].addEventListener("change", function() {
                    // console.log(`${allSelect[i].value} -- ${grading_solution.value}`);
                    if ((section_a_grade.value != 1 || section_b_grade.value != 1 || section_c_grade.value != 1) && grading_solution.value == 1) {
                        grading_solution.selectedIndex = 0;
                        checkButton.setAttribute('disabled', 'disabled');
                    }
                    else if ((section_a_grade.value == 4 || section_b_grade.value == 4 || section_c_grade.value == 4) && grading_solution.value != 4) {
                        grading_solution.selectedIndex = 4;
                    }
                    else if (grading_solution.value != 1) {
                        checkButton.removeAttribute('disabled');
                    }
                });
            }

        }

        setTimeout(checkGrade, 2000);

        statusMessage.innerHTML = "";
        var target = event.currentTarget;
        //var parent = target.parentElement.nodeName;
        //console.log(target.getAttribute('data-id'));
        allData.fileuserdata = target.getAttribute('data-id');

        //console.log(target.getAttribute('data-link'));
        let dataLink = target.getAttribute('data-link');

        const request = new XMLHttpRequest();
        request.open('POST', `${document.location.origin}/${dataLink}`);

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

<script>
    const ifio = document.querySelector('#ifio'),
        inform = document.querySelector('.inform'),
        searchbtn = document.querySelector('#searchbtn');
    if(searchbtn){
        searchbtn.addEventListener('click', () => {

            const spinner = document.createElement('img');
        spinner.src = message.loading;
        inform.innerHTML = `<img src=${message.loading}>`;

        if(ifio.value.length > 2){
            const request = new XMLHttpRequest();
            request.open('POST','/server_user/?get_user_group_id=<?= $_GET['id']?>');
            request.setRequestHeader('Content-type', 'application/json; charset=utf-8');
            request.send(JSON.stringify(ifio.value.toUpperCase()));

            request.addEventListener('load', () => {
                if (request.status === 200){
                //const data = JSON.parse(request.response);
                //console.log(request.response);
                inform.innerHTML = `<br>${request.response}`;
            }else{
                inform.innerHTML = "Что-то пошло не так";
            }
        });
        }else{
            inform.innerHTML = "<br>Текст поиска должен быть больше 2 символов!";
        }




    });
    }
    let lang;

    if(document.querySelector('#kz')){
        lang = 'Уақыт бітті!'
    }else{
        lang = 'Время вышло!'
    }


</script>

<script src="<?= bloginfo('template_url') ?>/assets/js/autosize-master/dist/autosize.js"></script>

<script>
    autosize(document.querySelectorAll('textarea'));
</script>
