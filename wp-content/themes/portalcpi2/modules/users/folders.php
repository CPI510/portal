<?php 
global $wpdb;
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Мои папки</h1>
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
                <?php $access = getAccess(get_current_user_id())->access;?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>Количество</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        <?php if($access === '4' || $access === '3' || $access === '2'): ?>

                                    <?php $dataFolders = $wpdb->get_results($wpdb->prepare("SELECT f.id folder_id, f.name folder_name, p.user_id, COUNT(p.id) Num_f
                                        FROM p_folder f
                                        LEFT OUTER JOIN p_file p ON p.folder = f.id AND p.user_id = %d
                                        WHERE f.id_program = 0
                                        GROUP BY f.id
                                        ",get_current_user_id()));?>
                                    <?php foreach($dataFolders as $folder): ?>
                                        <tr>
                                            <td><?= ++$i ?></td>
                                            <td><a href="/users/?z=add_file&folder_id=<?= $folder->folder_id ?>" class="text-primary"><?= $folder->folder_name ?></a></td>
                                            <td><span class="badge pull-right"><?= $folder->Num_f ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>

                            <?php elseif($access === '5'): ?>
                                <?php $dataCourses = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, r.program_id, r.number_group 
                                        FROM p_groups_users g 
                                        LEFT OUTER JOIN p_groups r ON r.id = g.id_group
                                        WHERE g.id_user = %d",get_current_user_id() ));?>

                                <?php foreach($dataCourses as $data): ?>
                                    
                                    <tr>
                                        <td colspan="3"><p align="center"><b>Папки группы: <?= $data->number_group ?></b></p></td>
                                    </tr>
                                    <?php $dataFolders = $wpdb->get_results($wpdb->prepare("SELECT f.id folder_id, f.name folder_name, p.user_id, COUNT(p.id) Num_f
                                        FROM p_folder f
                                        LEFT OUTER JOIN p_file p ON p.folder = f.id AND p.user_id = %d
                                        WHERE f.id_program = %d
                                        GROUP BY f.id
                                        ",get_current_user_id(), $data->program_id ));?>
                                    <?php foreach($dataFolders as $folder): ?>
                                        <tr>
                                            <td><?= ++$i ?></td>
                                            <td><a href="/users/?z=add_file&folder_id=<?= $folder->folder_id ?>" class="text-primary"><?= $folder->folder_name ?></a></td>
                                            <td><span class="badge pull-right"><?= $folder->Num_f ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
</div>