<?php
global $wpdb;

if($_POST['action_t']){
    //printAll($_POST); exit();

    if($_POST['action_t'] == 1){
        if($wpdb->insert('p_training_center', [
            'region_id' => $_POST['region_id'],
            'name' => $_POST['p_name_center'],
            'name_kaz' => $_POST['p_name_center_kaz'],
            'admin_id_create' => get_current_user_id(),
            'date_create' => dateTime()
        ],[
            '%d','%s','%s','%d','%s'
        ])) echo'<meta http-equiv="refresh" content="0;url=/training_center/" />';
    }elseif($_POST['action_t'] == 2){
        if($wpdb->update('p_training_center', [
            'region_id' => $_POST['region_id'],
            'name' => $_POST['p_name_center'],
            'name_kaz' => $_POST['p_name_center_kaz']
        ],[
                'id' => $_GET['id']
        ])){
            echo'<meta http-equiv="refresh" content="0;url=/training_center/" />';
        }
    }

}
?>
<div class="row">
    <?php
    if( $get_training = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_training_center WHERE id = %d", $_GET['id']))){
        $text = [
            'Редактирование',
            2,
        ];
    }else{
        $text = [
            'Добавление',
            1,
        ];
    }
    ?>


    <?php if($_GET['q'] == 'add' || $_GET['q'] == 'edit'): ?>
        <div class="col-lg-12">
            <h1 class="text-primary"><?= $text[0] ?> центра обучения</h1>
        </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form class="form-horizontal" role="form" method="POST">
                    <div class="form-group">
                        <label for="regular13" class="col-sm-2 control-label">Название центра обучения</label>
                        <div class="col-sm-10">
                            <input type="text" name="p_name_center" class="form-control" id="regular13" value="<?= $get_training->name ?>"><div class="form-control-line"></div><div class="form-control-line"></div>
                        </div>
                        <label for="regular13" class="col-sm-2 control-label">Название центра обучения на казахском</label>
                        <div class="col-sm-10">
                            <input type="text" name="p_name_center_kaz" class="form-control" id="regular13" value="<?= $get_training->name_kaz ?>"><div class="form-control-line"></div><div class="form-control-line"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="regular13" class="col-sm-2 control-label">Регион</label>
                        <div class="col-sm-10">
                            <select name="region_id" class="form-control">
                                <option></option>
                                <?php
                                $regions = $wpdb->get_results("SELECT * FROM `p_region`");
                                foreach ($regions as $region){
                                    if($get_training->region_id == $region->id){
                                        echo "<option value='{$region->id}' selected>{$region->name}</option>";
                                    }else{
                                        echo "<option value='{$region->id}'>{$region->name}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-2">
                            <input type="submit" class="btn btn-success" value="Сохранить">
                            <input type="hidden" name="action_t" value="<?= $text[1] ?>">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="col-lg-12">
        <h1 class="text-primary">Список</h1>
    </div>
    <div class="col-lg-12">
        <a href="/training_center/?q=add" class="btn btn-success">Добавить</a>
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
                                <th>Кол-во созданных групп</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $results = $wpdb->get_results("SELECT a.id, a.name, a.name_kaz, a.region_id, b.name region_name, COUNT(c.id) num_group 
                                    FROM p_training_center a
                                    LEFT OUTER JOIN p_region b ON b.id = a.region_id 
                                    LEFT OUTER JOIN p_groups c ON c.training_center = a.id
                                    WHERE a.deleted = 0 GROUP BY a.id;");
                            if($_GET['id'] && $_GET['d']){
                                //$resd = $wpdb->query($wpdb->prepare("DELETE FROM `p_training_center` WHERE `p_training_center`.`id` = %d", $_GET['id']));
                                //$resd = $wpdb->query($wpdb->prepare("DELETE FROM `p_training_center` WHERE `p_training_center`.`id` = %d", $_GET['id']));
                                $getrow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM p_training_center WHERE id = %d", $_GET['id']));
                                $name_center_deleted = $getrow->name . '_deleted';
                                if( !$wpdb->query($s=$wpdb->prepare("UPDATE `p_training_center` SET `deleted` = 1 , `name` = %s WHERE `id` = %d", $name_center_deleted , $_GET['id'])) ){echo $s; alertStatus('warning', 'Нет данных');}
                                else echo'<meta http-equiv="refresh" content="0;url=/training_center/" />';
                            }

                            ?>
                            <?php foreach($results as $res): ?>
                                <tr>
                                    <td><?= ++$i ?></td>
                                    <td><?= $res->name ?></td>
                                    <td><?= $res->name_kaz ?></td>
                                    <td><?= $res->num_group ?></td>
                                    <td>
                                        <a href="/training_center/?q=edit&id=<?= $res->id ?>" class="btn btn-icon-toggle" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
                                        <a href="/training_center/?d=d&id=<?= $res->id ?>" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
</div>
