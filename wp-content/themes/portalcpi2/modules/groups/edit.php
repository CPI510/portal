<?php 
global $wpdb;
?>

<?php $result = $wpdb->get_row($wpdb->prepare( "SELECT `id`, `date_create`, `number_group`, `program_id`, `start_date`, `end_date`, `trener_id`, `training_center`, `active`  
FROM p_groups WHERE id = %d", $_GET['id'])); ?>	

<?php if($result): ?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="text-primary">Редактирование группы</h2>
    </div><!--end .col -->
    <div class="col-lg-8">
        <p class="lead">
            Заполните поля для изменения
        </p>
    </div><!--end .col -->
</div>

<div class="card">
    <div class="card-body">
    <div class="box"></div>
        <form class="form-horizontal" role="form" method="POST" id="formreg" data-form="edit">
        <input type="hidden" data-user name="statement" value="4">
            <div class="form-group">
                <label for="regular13" class="col-sm-2 control-label">Номер группы</label>
                <div class="col-sm-10">
                    <input type="text" value="<?= $result->number_group ?>" data-user name="number_group" class="form-control" required><div class="form-control-line"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password13" class="col-sm-2 control-label">Программа</label>
                <div class="col-sm-10">
                    <select class="form-control" name="program_id" data-user  required>
                    <option></option>
                    <?php $results = $wpdb->get_results("SELECT * FROM p_programs;") ?>
                    <?php foreach ($results as $value):?>

                        <?php if($result->program_id == $value->id): ?>
                            <option value="<?= $value->id ?>" selected><?= $value->p_name ?></option>
                        <?php else: ?>
                            <option value="<?= $value->id ?>"><?= $value->p_name ?></option>
                        <?php endif; ?>
                        
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="password13" class="col-sm-2 control-label">Центр обучения</label>
                <div class="col-sm-10">
                    <select class="form-control" name="p_training_center" data-user  required>
                    <option></option>
                    <?php $results = $wpdb->get_results("SELECT * FROM p_training_center;") ?>
                    <?php foreach ($results as $value):?>
                        <?php if($result->training_center == $value->id): ?>
                            <option value="<?= $value->id ?>" selected><?= $value->name ?></option>
                        <?php else: ?>
                            <option value="<?= $value->id ?>"><?= $value->name ?></option>
                        <?php endif; ?>
                        
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="password13" class="col-sm-2 control-label">Тренер</label>
                <div class="col-sm-10">
                    <select class="form-control select2-list" name="trener" data-user  required>
                    <option></option>
                    <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '4';") ?>
                    <?php foreach ($results as $value):?>
                        <?php if($result->trener_id == $value->user_id): ?>
                            <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                        <?php else: ?>
                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                        <?php endif; ?>
                        
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
            <label for="regular13" class="col-sm-2 control-label">Активный период загрузки файлов</label>
                <div class="col-sm-10" >
                    <div class="input-daterange input-group" id="demo-date-range">
                        <div class="input-group-content">
                            <input type="text" value="<?= $result->start_date ?>" name="start_date" data-user class="form-control">
                        </div>
                        <span class="input-group-addon">по</span>
                        <div class="input-group-content">
                            <input type="text" value="<?= $result->end_date ?>" name="end_date" data-user class="form-control">
                            <div class="form-control-line"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                        <label for="password13" class="col-sm-2 control-label">Активность</label>
                        <div class="col-sm-10">
                            <select class="form-control" data-user name="active">
                                <option value="1" <?= ($result->active == 1) ? "selected":"" ?>>Активно</option>
                                <option value="2" <?= ($result->active == 2) ? "selected":"" ?>>Не активно</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" data-user name="id" value="<?= $_GET['id'] ?>">
            <div class="form-group">
                <div class="col-lg-2">
                    <input type="submit" class="btn btn-success" value="Изменить">
                </div>
            </div>
        </form>
    </div><!--end .card-body -->
</div>
<?php else: ?>
<br>
<?php alertStatus('danger','Нет данных!');?>

<?php endif; ?>