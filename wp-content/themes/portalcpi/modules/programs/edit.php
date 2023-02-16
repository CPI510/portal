<?php  global $wpdb; ?>

<?php if(!$_GET['id']): ?>

<?php alertStatus('danger','Нет данных!');?>

<?php else: ?>

    <?php $result = $wpdb->get_row($wpdb->prepare( "SELECT id, p_name, name_kaz, active FROM p_programs WHERE id = %d", $_GET['id'])); ?>

    <?php if(!$result):?>

        <?php alertStatus('danger','Нет данных!');?>

    <?php else: ?>
        <?php 
        if($_POST['p_name'] && $_POST['active'] && $_POST['id']) {
            $wpdb->query($wpdb->prepare( $s="UPDATE p_programs SET p_name = %s, name_kaz = %s, active = %s WHERE id = %d", $_POST['p_name'], $_POST['name_kaz'], $_POST['active'], $_POST['id']));
            echo'<meta http-equiv="refresh" content="0;url=/portalcpi/program/?z=list" />'; exit();
            } 
            ?>
            
        <div class="row">
            <div class="col-lg-12">
                <h2 class="text-primary">Редактирование программы</h2>
            </div><!--end .col -->
            <div class="col-lg-8">
                <p class="lead">
                    Измените данные в полях
                </p>
            </div><!--end .col -->
        </div>

        <div class="card">
            <div class="card-body">
                <form class="form-horizontal" role="form" method="POST">
                    <div class="form-group">
                        <label for="regular13" class="col-sm-2 control-label">Название программы</label>
                        <div class="col-sm-10">
                            <input type="text" name="p_name" class="form-control" value="<?= $result->p_name ?>"><div class="form-control-line"></div>
                        </div>
                        <label for="regular13" class="col-sm-2 control-label">Название программы на казахском</label>
                        <div class="col-sm-10">
                            <input type="text" name="name_kaz" class="form-control" value="<?= $result->name_kaz ?>"><div class="form-control-line"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password13" class="col-sm-2 control-label">Активность</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="active">
                                <option value="1" <?= ($result->active == 1) ? "selected":"" ?>>Активно</option>
                                <option value="2" <?= ($result->active == 2) ? "selected":"" ?>>Не активно</option>
                            </select>
                        </div>
                    </div>
                        <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
                    <div class="form-group">
                        <div class="col-lg-2">
                            <input type="submit" class="btn btn-success" value="Изменить">
                        </div>
                    </div>
                </form>
            </div><!--end .card-body -->
        </div>

    <?php endif; ?>

<?php endif; ?>

