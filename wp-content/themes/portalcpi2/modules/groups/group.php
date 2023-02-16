<?php 
global $wpdb;
if(!$_GET['id']) exit('Нет данных!');

$ResultsTr = $wpdb->get_row($wpdb->prepare("SELECT g.number_group, g.start_date, g.end_date, p.p_name, u.surname, u.name, u.patronymic, t.name name_org
FROM p_groups g
LEFT OUTER JOIN p_programs p ON p.id = g.program_id 
LEFT OUTER JOIN p_user_fields u ON u.user_id = g.trener_id
LEFT OUTER JOIN p_training_center t ON t.id = g.training_center WHERE g.id = %d", $_GET['id']));
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Группа <?= $ResultsTr->number_group ?></h1>
    </div>
    <div class="col-lg-12">
                <b>Период:</b> с <?= $ResultsTr->start_date ?> по <?= $ResultsTr->end_date ?><br>
                <b>Программа:</b> <?= $ResultsTr->p_name ?><br>
                <b>ФИО тренера:</b> <?= $ResultsTr->surname ?> <?= $ResultsTr->name ?> <?= $ResultsTr->patronymic ?><br>
                <b>Организация, проводившая курсы:</b> <?= $ResultsTr->name_org ?><br>
    </div><!--end .col -->
    <div class="col-md-8">
        <article class="margin-bottom-xxl">

        </article>
    </div><!--end .col -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ФИО</th>
                                <th>Email</th>
                                <th>Дата регистрации</th>

                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $results = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, p.start_date, p.end_date, u.surname, u.name, u.patronymic, u.email
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_groups p ON p.id = g.id_group
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user WHERE g.id_group = %d", $_GET['id'] ));
                        ?>
                        <?php foreach($results as $res): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><a href="/members/?z=user_page&id=<?= $res->id_user ?>&trener=1" class="text-primary"><?= $res->surname ?> <?= $res->name ?> <?= $res->patronymic ?></td>
                                <td><?= $res->email ?></td>
                                <td><?= $res->date_reg ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
</div>