<?php 
global $wpdb;
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Список пользователей</h1>
    </div>
    <div class="col-lg-12">
        <?php if(getAccess(get_current_user_id())->access == 1): ?><a href="/members/?z=add" class="btn btn-success">Добавить</a><?php endif;?>
    </div><!--end .col -->
    <div class="col-md-8">
        <article class="margin-bottom-xxl">

        </article>
    </div><!--end .col -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                <?php
                        if(getAccess(get_current_user_id())->access == 1){
                            $results = $wpdb->get_results("SELECT u.user_id, u.surname, u.name, u.patronymic, u.email, a.name access_name
                                FROM p_user_fields u
                                INNER JOIN p_access a ON a.value = u.access");
                        }else{
                            $numU = nameUser(get_current_user_id(),4);
                            $results = $wpdb->get_results($wpdb->prepare("SELECT u.user_id, u.surname, u.name, u.patronymic, u.email, a.name access_name
                                FROM p_user_fields u
                                INNER JOIN p_access a ON a.value = u.access WHERE u.user_id in ($numU)"));
                        }
                            
                            if($_GET['id'] && $_GET['d']){ 
                                $resd = $wpdb->query($wpdb->prepare("UPDATE p_user_fields SET active FROM `p_programs` WHERE `p_programs`.`id` = %d", $_GET['id'])); 
                                if(!$resd) alertStatus('warning', 'Нет данных');
                                else echo'<meta http-equiv="refresh" content="0;url=/portalcpi/program/?z=list" />'; 
                            }  
                        ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ФИО</th>
                                <th>Email</th>
                                <th>Статус</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        <?php foreach($results as $res): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><a href="/members/?z=user_page&id=<?= $res->user_id ?>" class="text-primary" ><?= $res->surname ?> <?= $res->name ?> <?= $res->patronymic ?></a></td>
                                <td><?= $res->email ?></td>
                                <td><?= $res->access_name ?></td>
                                <td><?php if(getAccess(get_current_user_id())->access == 1): ?>
                                    <a href="/members/?z=edit&id=<?= $res->user_id ?>" class="btn btn-icon-toggle" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
                                    <a href="/members/?z=list&d=d&id=<?= $res->user_id ?>" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
                                    <?php endif;?>
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

