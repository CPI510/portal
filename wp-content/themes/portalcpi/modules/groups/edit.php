<?php
global $wpdb;
?>

<?php $result = $wpdb->get_row($wpdb->prepare( "SELECT `id`, `lang_id`, `date_create`, `number_group`, `program_id`, `start_date`, `end_date`, `trener_id`, `trener_date`,
`training_center`, `active`, `moderator_id`, `expert_id`, `teamleader_id`, `admin_id`, `potok`, `expert_date`, `moderator_date`, `teamleader_date`, `independent_trainer_id`, `independent_trainer_date`, `program_subsection`  
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

                <?php if( ($result->program_id == 6 || $result->program_id == 16) ): ?>
                    <div class="form-group">
                        <label for="regular13" class="col-sm-2 control-label">Подраздел программы</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="program_subsection" data-user  required>
                                <option></option>
                                <option value="1" <?= ($result->program_subsection == 1) ? "selected" : "" ?>>ЭО</option>
                                <option value="2" <?= ($result->program_subsection == 2) ? "selected" : "" ?>>ЛУШ</option>
                                <option value="3" <?= ($result->program_subsection == 3) ? "selected" : "" ?>>ЛУПС</option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="password13" class="col-sm-2 control-label">Центр обучения</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="p_training_center" data-user  required>
                            <option></option>
                            <?php $results = $wpdb->get_results("SELECT a.id, a.name, a.region_id, b.name region_name 
                                    FROM p_training_center a
                                    LEFT OUTER JOIN p_region b ON b.id = a.region_id WHERE a.deleted = 0;") ?>
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


                    <!-- тренер-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Тренер</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="trener" data-user  required>
                                        <option value="0">-=Нет тренера=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '4';") ?>
                                        <?php foreach ($results as $value):?>
                                            <?php if($result->trener_id == $value->user_id): ?>
                                                <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php else: ?>
                                                <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для тренера)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickertrener">
                                        <input value="<?= $result->trener_date ?>" type="text" name="trener_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                



                <div class="form-group">
                    <label for="regular13" class="col-sm-2 control-label">Активный период загрузки файлов</label>
                    <div class="col-sm-10" >
                        <div class="input-daterange input-group">
                            <div class="input-group-content">
                                <div class="input-group date" id="datetimepicker1">
                                    <input value="<?= $result->start_date ?>" type="text" name="start_date" data-user class="form-control" autocomplete="off" required="">
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <span class="input-group-addon">по</span>
                            <div class="input-group-content">
                                <div class="input-group date" id="datetimepicker2">
                                    <input type="text" name="end_date" data-user class="form-control" autocomplete="off" required="" value="<?= $result->end_date ?>">
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
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

                <div class="form-group">
                    <label for="password13" class="col-sm-2 control-label">Язык обучения</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="lang_id" data-user  required="">
                            <option></option>
                            <?php $results = $wpdb->get_results("SELECT * FROM p_lang;") ?>
                            <?php foreach ($results as $value):?>
                                <?php if($result->lang_id == $value->id):?>
                                    <option value="<?= $value->id ?>" selected><?= $value->name_ru ?></option>
                                <?php else: ?>
                                    <option value="<?= $value->id ?>"><?= $value->name_ru ?></option>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <input type="hidden" data-user name="id" value="<?= $_GET['id'] ?>">

                <?php if($result->program_id == 7 || ($result->program_id == 6 || $result->program_id == 16)): //Для директоров свои поля и для ЭО ЛУШ ЛУПС?>

                    <!--Эксперт-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Эксперт</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="expert_id" data-user  required>
                                        <option value="0">-=Нет эксперта=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7' OR `user_id` = '349' ORDER BY `surname`  ASC, `name` ASC, `patronymic` ASC;") ?>
                                        <?php foreach ($results as $value):?>
                                            <?php if($result->expert_id == $value->user_id): ?>
                                                <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php else: ?>
                                                <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                        <option value="349">Тажибаев Асхат Куатович</option>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для эксперта)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickerexpert">
                                        <input value="<?= $result->expert_date ?>" type="text" name="expert_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Независимый тренер-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Независимый тренер</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="independent_trainer_id" data-user  required>
                                        <option value="0">-=Нет тренера=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '4';") ?>
                                        <?php foreach ($results as $value):?>
                                            <?php if($result->independent_trainer_id == $value->user_id): ?>
                                                <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php else: ?>
                                                <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для независимого тренера)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickerindependenttrainer">
                                        <input value="<?= $result->independent_trainer_date ?>" type="text" name="independent_trainer_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Модератор-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Модератор</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="moderator_id" data-user  required>
                                        <option value="0">-=Нет модератора=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7';") ?>
                                        <?php foreach ($results as $value):?>
                                            <?php if($result->moderator_id == $value->user_id): ?>
                                                <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php else: ?>
                                                <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для модератора)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickermoderator">
                                        <input value="<?= $result->moderator_date ?>" type="text" name="moderator_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--тимлидер-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Тимлидер</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="teamleader_id" data-user  required>
                                        <option value="0">-=Нет тимлидера=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7';") ?>
                                        <?php foreach ($results as $value):?>
                                            <?php if($result->teamleader_id == $value->user_id): ?>
                                                <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php else: ?>
                                                <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для тимлидера)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickerteamleader">
                                        <input value="<?= $result->teamleader_date ?>" type="text" name="teamleader_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                <?php if($result->program_id != 15): //Для Оценивание портфолио тренеров по образовательной программе курсов повышения квалификации «Инновационный менеджмент в управлении школой» свои поля ?>
                    <!--Эксперт-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Эксперт</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="expert_id" data-user  required>
                                        <option value="0">-=Нет эксперта=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7' OR `user_id` = '349' ORDER BY `surname`  ASC, `name` ASC, `patronymic` ASC;") ?>
                                        <?php foreach ($results as $value):?>
                                            <?php if($result->expert_id == $value->user_id): ?>
                                                <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php else: ?>
                                                <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для эксперта)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickerexpert">
                                        <input value="<?= $result->expert_date ?>" type="text" name="expert_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Модератор-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Модератор</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="moderator_id" data-user  required>
                                        <option value="0">-=Нет модератора=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7';") ?>
                                        <?php foreach ($results as $value):?>
                                            <?php if($result->moderator_id == $value->user_id): ?>
                                                <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php else: ?>
                                                <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для модератора)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickermoderator">
                                        <input value="<?= $result->moderator_date ?>" type="text" name="moderator_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($result->program_id == 14): ?>
                        <!--Независимый тренер становиться модератором 2 для программы 14-->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="Firstname5" class="col-sm-4 control-label">Наблюдатель</label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2-list" name="independent_trainer_id" data-user  required>
                                            <option value="0">-=Не выбран=-</option>
                                            <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '4';") ?>
                                            <?php foreach ($results as $value):?>
                                                <?php if($result->independent_trainer_id == $value->user_id): ?>
                                                    <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                                <?php else: ?>
                                                    <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                                <?php endif; ?>

                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-control-line"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для модератора 2)</label>
                                    <div class="col-sm-8">
                                        <div class="input-group date" id="datetimepickerindependenttrainer">
                                            <input value="<?= $result->independent_trainer_date ?>" type="text" name="independent_trainer_date" data-user class="form-control" autocomplete="off" >
                                            <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                        </div>
                                        <div class="form-control-line"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!--Тимлидер-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Тимлидер</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="teamleader_id" data-user  required>
                                        <option value="0">-=Нет тимлидера=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7';") ?>
                                        <?php foreach ($results as $value):?>
                                            <?php if($result->teamleader_id == $value->user_id): ?>
                                                <option value="<?= $value->user_id ?>" selected><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php else: ?>
                                                <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для тимлидера)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickerteamleader">
                                        <input value="<?= $result->teamleader_date ?>" type="text" name="teamleader_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
                <?php endif; ?>

                <div class="form-group">
                    <label for="password13" class="col-sm-2 control-label">Поток</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="potok" data-user  required>
                            <option></option>
                            <?php for ($i = 1; $i <= 35; $i++):?>
                                <?php if($i == $result->potok): ?>
                                    <option value="<?= $i ?>" selected><?= $i ?></option>
                                <?php else: ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

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
