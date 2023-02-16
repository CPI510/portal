<?php 
global $wpdb;
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="text-primary">Добавление группы</h2>
    </div><!--end .col -->
    <div class="col-lg-8">
        <p class="lead">
            Заполните поля для создания группы
        </p>
    </div><!--end .col -->
</div>

<div class="card">
    <div class="card-body">
    <div class="box"></div>
        <form class="form-horizontal">
            
            <div class="form-group">
                <label for="password13" class="col-sm-2 control-label">Программа</label>
                <div class="col-sm-10">
                    <select class="form-control" name="program_id_f" required onchange="this.form.submit()">
                    <option></option>
                    <?php $results = $wpdb->get_results("SELECT * FROM p_programs;") ?>
                    <?php foreach ($results as $value):?>
                        <?php if($_GET['program_id_f'] == $value->id): ?>
                            <option value="<?= $value->id ?>" selected><?= $value->p_name ?></option>
                        <?php else: ?>
                            <option value="<?= $value->id ?>"><?= $value->p_name ?></option>
                        <?php endif; ?>

                    <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <input type="hidden" name="z" value="add">
        </form>

        <?php if($_GET['program_id_f']): ?>
            <form class="form-horizontal" role="form" method="POST" id="formreg" data-form="add" >

                <input type="hidden" data-user name="statement" value="3">
                <?php if($_GET['program_id_f'] == 6 || $_GET['program_id_f'] == 16): ?>
                    <div class="form-group">
                        <label for="regular13" class="col-sm-2 control-label">Подраздел программы</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="program_subsection" data-user  required>
                                <option></option>
                                <option value="1">ЭО</option>
                                <option value="2">ЛУШ</option>
                                <option value="3">ЛУПС</option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($_GET['program_id_f'] == 18): ?>
                    <div class="form-group">
                        <label for="regular13" class="col-sm-2 control-label">Тип оценивания</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="program_assessment" id="program_assessment" data-user  required>
                                <option selected value="1">Основное</option>
                                <option value="2">Повторное</option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>


                <div class="form-group">
                    <label for="regular13" class="col-sm-2 control-label">Номер группы</label>
                    <div class="col-sm-10">
                        <input id="number_group" type="text" data-user name="number_group" class="form-control" required><div class="form-control-line"></div>
                    </div>
                </div>

                <input type="hidden" data-user name="program_id" value="<?= $_GET['program_id_f'] ?>">

                <?php if($_GET['program_id_f'] == 18): ?>
                    <div class="form-group">
                        <label for="regular13" class="col-sm-2 control-label">Предмет</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="subject_id" data-user >
                                <option></option>
                                <?php $subjects = $wpdb->get_results("SELECT * FROM p_subject"); ?>
                                <?php foreach ($subjects as $sbj):?>
                                    <option value="<?= $sbj->id ?>"><?= $sbj->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="password13" class="col-sm-2 control-label">Центр обучения</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="p_training_center" data-user  required>
                            <option></option>
                            <?php $results = $wpdb->get_results("SELECT * FROM p_training_center;") ?>
                            <?php foreach ($results as $value):?>
                                <option value="<?= $value->id ?>"><?= $value->name ?></option>
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

                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>

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
                                        <input type="text" name="trener_date" data-user class="form-control" autocomplete="off" >
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
                        <div class="input-daterange input-group" >
                            <div class="input-group-content">
                                <div class="input-group date" id="datetimepicker1">
                                    <input type="text" name="start_date" data-user class="form-control" autocomplete="off" required="">
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <span class="input-group-addon">по</span>
                            <div class="input-group-content">
                                <div class="input-group date" id="datetimepicker2">
                                    <input type="text" name="end_date" data-user class="form-control" autocomplete="off" required="">
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
                    <label for="password13" class="col-sm-2 control-label">Язык обучения</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="lang_id" data-user  required>
                            <option></option>
                            <?php $results = $wpdb->get_results("SELECT * FROM p_lang;") ?>
                            <?php foreach ($results as $value):?>
                                <option value="<?= $value->id ?>"><?= $value->name_ru ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!--Наблюдатель-->
                <?php if($_GET['program_id_f'] == 18): ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Наблюдатель</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="observer_id" data-user  required>
                                        <option value="0">-=Нет наблюдателя=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7' OR `user_id` = '349' ORDER BY `surname`  ASC, `name` ASC, `patronymic` ASC;;") ?>
                                        <?php foreach ($results as $value):?>
                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Lastname5" class="col-sm-4 control-label">Дата завершения (для наблюдателя)</label>
                                <div class="col-sm-8">
                                    <div class="input-group date" id="datetimepickerobserver">
                                        <input value="<?= $result->observer_date ?>" type="text" name="observer_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if($_GET['program_id_f'] == 7 || $_GET['program_id_f'] == 6 || $_GET['program_id_f'] == 16): //Для директоров свои поля и для ЭО ЛУШ ЛУПС ?>

                    <!--эксперт-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Эксперт</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="expert_id" data-user  required>
                                        <option value="0">-=Нет эксперта=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7' OR `user_id` = '349' ORDER BY `surname`  ASC, `name` ASC, `patronymic` ASC;;") ?>
                                        <?php foreach ($results as $value):?>
                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
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

                    <!--Независимый тренер-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Независимый тренер</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list required" name="independent_trainer_id" data-user  required>
                                        <option value="0">-=Нет тренера=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '4';") ?>
                                        <?php foreach ($results as $value):?>
                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
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
                                        <input type="text" name="independent_trainer_date" data-user class="form-control" autocomplete="off" >
                                        <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--модератор-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Модератор</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="moderator_id" data-user  required>
                                        <option value="0">-=Нет модератора=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7';") ?>
                                        <?php foreach ($results as $value):?>
                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
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
                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
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
                    <?php if($_GET['program_id_f'] != 15): //Для Оценивание портфолио тренеров по образовательной программе курсов повышения квалификации «Инновационный менеджмент в управлении школой» свои поля ?>
                    <!--эксперт-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Эксперт</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="expert_id" data-user  required>
                                        <option value="0">-=Нет эксперта=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7' OR `user_id` = '349' ORDER BY `surname`  ASC, `name` ASC, `patronymic` ASC;;") ?>
                                        <?php foreach ($results as $value):?>
                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
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

                    <!--модератор-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Firstname5" class="col-sm-4 control-label">Модератор</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2-list" name="moderator_id" data-user  required>
                                        <option value="0">-=Нет модератора=-</option>
                                        <?php $results = $wpdb->get_results("SELECT `user_id`, `surname`, `name`, `patronymic` FROM p_user_fields WHERE `access` = '7';") ?>
                                        <?php foreach ($results as $value):?>
                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
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
                                            <option value="<?= $value->user_id ?>"><?= $value->surname ?> <?= $value->name ?> <?= $value->patronymic ?></option>
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
                        <select class="form-control" name="potok" data-user  required="">
                            <option></option>
                            <?php for ($i = 1; $i <= 35; $i++):?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-2">
                        <input type="submit" class="btn btn-success" value="Добавить">
                    </div>
                </div>

            </form>
        <?php endif; ?>



    </div><!--end .card-body -->
</div>