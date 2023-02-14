<?php 
global $wpdb;
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Список групп</h1>
    </div>
    <div class="col-lg-12">
        <a href="/groups/?z=add" class="btn btn-success">Добавить</a>
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
                                <th>Номер группы</th>
                                <th>Программа</th>
                                <th>Дата начала загрузки</th>
                                <th>Дата окончания загрузки</th>
                                <th>Тренер</th>
                                <th>Ссылка</th>
                                <th>Кол-во</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $results = $wpdb->get_results("SELECT g.id, g.number_group, g.trener_id, g.start_date, g.end_date, p.p_name, COUNT(u.id_group) num_g, f.surname t_surname, f.name t_name, f.patronymic t_patronymic 
                            FROM p_groups g
                            LEFT JOIN p_programs p ON p.id = g.program_id
                            LEFT JOIN p_groups_users u ON u.id_group = g.id
                            LEFT JOIN p_user_fields f ON f.user_id = g.trener_id
                            GROUP BY g.id");
                            if($_GET['id'] && $_GET['d']){ 
                                $resd = $wpdb->query($wpdb->prepare("DELETE FROM `p_programs` WHERE `p_programs`.`id` = %d", $_GET['id'])); 
                                if(!$resd) alertStatus('warning', 'Нет данных');
                                else echo'<meta http-equiv="refresh" content="0;url=/portalcpi/program/?z=list" />'; 
                            }  
                            
                        ?>
                        <?php foreach($results as $res): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $res->number_group ?></td>
                                <td><?= $res->p_name ?></td>
                                <td><?= $res->start_date ?></td>
                                <td><?= $res->end_date ?></td>
                                <td><a href="/members/?z=user_page&id=<?= $res->trener_id ?>" class="text-primary" ><?= $res->t_surname ?> <?= $res->t_name ?> <?= $res->t_patronymic ?></a></td>
                                <td><code><?= get_site_url() ?>/registration/?id=<?= $res->id ?></code></td>
                                <td><span class="badge"><a href="/groups/?z=group&id=<?= $res->id ?>"><?= $res->num_g ?></a></span></td>
                                <td>
                                    <a href="/groups/?z=edit&id=<?= $res->id ?>" class="btn btn-icon-toggle" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
                                    <a href="/groups/?z=list&d=d&id=<?= $res->id ?>" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
					            </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
</div>