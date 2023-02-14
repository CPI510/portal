<?php 
global $wpdb;
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Список программ</h1>
    </div>
    <div class="col-lg-12">
        <a href="/program/?z=add" class="btn btn-success">Добавить</a>
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
                                <th>Название</th>
                                <th>Название на казахском</th>
                                <th>Активность</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $results = $wpdb->get_results("SELECT id, p_name, name_kaz,  active FROM p_programs");
                            if($_GET['id'] && $_GET['d']){ 
                                $resd = $wpdb->query($wpdb->prepare("DELETE FROM `p_programs` WHERE `p_programs`.`id` = %d", $_GET['id'])); 
                                if(!$resd) alertStatus('warning', 'Нет данных');
                                else echo'<meta http-equiv="refresh" content="0;url=/portalcpi/program/?z=list" />'; 
                            }  
                            
                        ?>
                        <?php foreach($results as $res): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $res->p_name ?></td>
                                <td><?= $res->name_kaz ?></td>
                                <td><?= ($res->active == 1) ? "Активно":"Неактивно" ?></td>
                                <td>
                                    <a href="/program/?z=folder&p_program=<?= $res->id ?>" class="btn btn-icon-toggle" data-original-title="Папки"><i class="md-folder"></i></a>
                                    <a href="/program/?z=edit&id=<?= $res->id ?>" class="btn btn-icon-toggle" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
                                    <a href="/program/?z=list&d=d&id=<?= $res->id ?>" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
					            </td>
                                <td>
                                    <?= $wpdb->get_var($wpdb->prepare('SELECT COUNT(id) FROM p_groups WHERE program_id = %d ', $res->id )) ?>
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
