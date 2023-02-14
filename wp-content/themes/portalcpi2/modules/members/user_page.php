<?php 
global $wpdb;
?>
<?php if(userAttach($_GET['id'], $_GET['trener'])):?>
<?php $result = $wpdb->get_row($wpdb->prepare( "SELECT u.user_id, u.surname, u.name, u.patronymic, u.iin, u.tel, u.email, u.access, u.date_create, a.name access_name FROM p_user_fields u
LEFT JOIN p_access a ON u.access = a.id
WHERE user_id = %d", $_GET['id'])); ?>	

<?php if($result): ?>

    <div class="row">
    <div class="col-lg-10">
        <h2 class="text-primary">Профиль пользователя</h2>
    </div><!--end .col -->
    <div class="col-lg-8">
        <p class="lead">
        </p>
    </div><!--end .col -->
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card card-tiles style-default-light">

            <!-- BEGIN BLOG POST HEADER -->
            <div class="row style-primary">
                <div class="col-sm-10">
                    <div class="card-body style-default-dark">
                        <h2><?= $result->surname ?> <?= $result->name ?> <?= $result->patronymic ?>
                        <?php if(getAccess(get_current_user_id())->access == 1 || get_current_user_id() == $result->user_id): ?><a href="/members/?z=edit&id=<?= $result->user_id ?>" class="btn btn-icon-toggle btn-warning" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a><?php endif; ?>
                        </h2>
                        <div class="text-default-light"><?= $result->access_name ?></div>
                    </div>
                </div><!--end .col -->
                <div class="col-sm-2">
                    <div class="card-body">
                        <div class="hidden-xs">
                            <h3 class="text-light"></h3>
                        </div>
                        <div class="visible-xs">
                            <strong></strong> 
                        </div>
                    </div>
                </div><!--end .col -->
            </div><!--end .row -->
            <!-- END BLOG POST HEADER -->

            <div class="row">

                <!-- BEGIN BLOG POST TEXT -->
                <div class="col-md-12">
                    <article class="style-default-bright">
                        <div>
                        </div>
                        <div class="card-body">
                            <p>
                                <b>ИИН:</b> <?= $result->iin ?><br>
                                <b>Email:</b> <?= $result->email ?><br>
                                <b>Дата регистрации:</b> <?= $result->date_create ?><br>
                                <b>Контактный телефон:</b> <?= $result->tel ?><br>
                            </p>
                        <div class="col-md-6">
                            <?php if($result->access === '4'): ?>
                                <h3 class="text-light">Группы</h3>
                                <ul class="nav nav-pills nav-stacked nav-transparent">
                                <?php $groupRes = $wpdb->get_results($wpdb->prepare("SELECT u.id_group, g.number_group, g.trener_id, g.start_date, g.end_date, COUNT(u.id_user) num_users
                                        FROM p_groups_users u 
                                        LEFT OUTER JOIN p_groups g ON g.id = u.id_group
                                        WHERE g.trener_id = %d GROUP BY u.id_group", $result->user_id)); ?>
                                <?php foreach($groupRes as $res): ?>
                                    <li><a href="/groups/?z=group&id=<?= $res->id_group ?>"><span class="badge pull-right"><?= $res->num_users ?></span>Номер группы: <?= $res->number_group ?> / C <?= $res->start_date ?> по <?= $res->end_date ?> </a></li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <?php if($result->access === '4' || $result->access === '3' || $result->access === '2'): ?>
                                <h3 class="text-light">Папки</h3>
                                
                                <ul class="nav nav-pills nav-stacked nav-transparent">
                                    <?php $dataFolders = $wpdb->get_results($wpdb->prepare("SELECT f.id folder_id, f.name folder_name, p.user_id, COUNT(p.id) Num_f
                                        FROM p_folder f
                                        LEFT OUTER JOIN p_file p ON p.folder = f.id AND p.user_id = %d
                                        WHERE f.id_program = 1
                                        GROUP BY f.id
                                        ",$result->user_id));?>
                                    <?php foreach($dataFolders as $folder): ?>
                                        <li><a href="/users/?z=add_file&folder_id=<?= $folder->folder_id ?>&user_id=<?= $result->user_id ?>"><span class="badge pull-right"><?= $folder->Num_f ?></span> <?= $folder->folder_name ?></a></li>
                                    <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if($result->access === '5'): ?>
                                <ul class="nav nav-pills nav-stacked nav-transparent">
                                <?php $dataCourses = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, r.program_id, r.number_group 
                                        FROM p_groups_users g 
                                        LEFT OUTER JOIN p_groups r ON r.id = g.id_group
                                        WHERE g.id_user = %d",$result->user_id ));?>

                                <?php foreach($dataCourses as $data): ?>
                                    <h3 class="text-light">Папки группы: <?= $data->number_group ?></h3>
                                    <?php $dataFolders = $wpdb->get_results($wpdb->prepare("SELECT f.id folder_id, f.name folder_name, p.user_id, COUNT(p.id) Num_f
                                        FROM p_folder f
                                        LEFT OUTER JOIN p_file p ON p.folder = f.id AND p.user_id = %d
                                        WHERE f.id_program = %d
                                        GROUP BY f.id
                                        ",$result->user_id, $data->program_id ));?>
                                    <?php foreach($dataFolders as $folder): ?>
                                        <li><a href="/users/?z=add_file&folder_id=<?= $folder->folder_id ?>&user_id=<?= $result->user_id ?>&trener=1"><span class="badge pull-right"><?= $folder->Num_f ?></span><?= $folder->folder_name ?></a></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                                </ul>
                                

                            <?php if($result->access === '3' || $result->access === '2'): ?>
                            <?php $numU = nameUser($result->user_id,4);
                            $attachedU = $wpdb->get_results($wpdb->prepare("SELECT u.user_id, u.surname, u.name, u.patronymic, u.email, a.name access_name
                            FROM p_user_fields u
                            INNER JOIN p_access a ON a.value = u.access WHERE user_id in ($numU)"));?>
                                <h3 class="text-light">Закрепленные пользователи</h3>
                                <ul class="nav nav-pills nav-stacked nav-transparent">
                                <?php foreach($attachedU as $user): ?>
                                    <li><a href="/members/?z=user_page&id=<?= $user->user_id ?>"> <?= $user->surname ?> <?= $user->name ?> <?= $user->patronymic ?> / <?= $user->access_name ?></a></li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div><!--end .col-md-6 -->
                        </div><!--end .card-body -->
                    </article>
                </div><!--end .col -->
                <!-- END BLOG POST TEXT -->


                <!-- END BLOG POST MENUBAR -->

            </div><!--end .row -->
        </div><!--end .card -->
    </div><!--end .col -->
</div>
<?php else: ?>
<br>
    <?php alertStatus('danger','Нет данных!');?>

<?php endif; ?>


<script>
	
</script>

<?php else:?>
<br><?php alertStatus('warning', 'Нет доступа!'); //redirectBack();
?>
<?php endif; ?>