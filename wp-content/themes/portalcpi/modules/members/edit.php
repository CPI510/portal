<?php 
global $wpdb;
?>

<?php // Редактирование предоставляется только админу и пользователю = пользователю  
if(getAccess(get_current_user_id())->access == 1 || get_current_user_id() == $_GET['id']):?>
<?php $result = $wpdb->get_row($wpdb->prepare( "SELECT `user_id`, `surname`, `name`, `patronymic`, `iin`, `tel`, `email`, `access`, `date_create`, `user_id_attached`, `program_id` FROM p_user_fields WHERE user_id = %d", $_GET['id'])); ?>

<?php if($result): ?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="text-primary">Редактирование пользователя</h2>
    </div><!--end .col -->
    <div class="col-lg-8">
        <p class="lead">
            Измените поля пользователя
        </p>
    </div><!--end .col -->
</div>
<div class="box"></div>
<div class="col-lg-12">
	<form class="form-horizontal" method="POST" id="formreg" data-form="edit">
	<input type="hidden" data-user name="statement" value="2">
		<div class="card">
			<div class="card-head style-primary">
				<header>Форма для редактирования пользователя</header>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label for="Firstname5" class="col-sm-3 control-label">Фамилия</label>
							<div class="col-sm-8">
								<input type="text" required class="form-control" data-user name="u_surname" value="<?= $result->surname ?>"><div class="form-control-line"></div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="Lastname5" class="col-sm-3 control-label">Имя</label>
							<div class="col-sm-8">
								<input type="text" required class="form-control" data-user name="u_name" value="<?= $result->name ?>"><div class="form-control-line"></div>
							</div>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group">
							<label for="Lastname5" class="col-sm-3 control-label">Отчество</label>
							<div class="col-sm-8">
								<input type="text"  class="form-control" data-user  name="u_patronymic" value="<?= $result->patronymic ?>"><div class="form-control-line"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="Username5" class="col-sm-2 control-label">ИИН</label>
					<div class="col-sm-10">
						<input type="text" required pattern="[0-9]{12}" data-user placeholder="ИИН" minlength="12" maxlength="12" value="<?= $result->iin ?>" class="form-control"  name="u_iin"><div class="form-control-line"></div>
					</div>
				</div>
				<div class="form-group ">
					<label for="Username5" class="col-sm-2 control-label">E-mail</label>
					<div class="col-sm-10">
						<input type="email" required <?php if(getAccess(get_current_user_id())->access != 1) echo "readonly"; ?>  class="form-control" data-user  name="u_email" value="<?= $result->email ?>"><div class="form-control-line"></div>
					</div>
				</div>
				<div class="form-group">
					<label for="Username5" class="col-sm-2 control-label">Контактный телефон</label>
					<div class="col-sm-10">
						<input type="text" required class="form-control" data-user  name="u_tel" value="<?= $result->tel ?>"><div class="form-control-line"></div>
					</div>
				</div>
				<div class="form-group <?php if(getAccess(get_current_user_id())->access != 1) echo "hidden"; ?>">
					<label for="password13" class="col-sm-2 control-label">Уровень доступа</label>
					<div class="col-sm-10">
						<select class="form-control" name="u_access" data-user  required>
						<option></option>
						<?php $results = $wpdb->get_results("SELECT * FROM p_access WHERE active = 1;") ?>
						<?php foreach ($results as $value):?>
							<?php if($result->access === $value->value): ?>
								<option value="<?= $value->value ?>" selected><?= $value->{"name_" . $_SESSION['lang']} ?></option>
							<?php else: ?>
								<option value="<?= $value->value ?>"><?= $value->{"name_" . $_SESSION['lang']} ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
						</select>
					</div>
				</div>



                <div class="form-group">
                    <label for="Password5" class="col-sm-2 control-label">Пароль</label>
                    <div class="input-group">
                        <div class="input-group-content">
                            <input type="text" id="password" data-user  class="form-control" name="u_pass" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*"><div class="form-control-line"></div>
                        </div>
                        <div class="input-group-btn">
                            <button class="btn btn-default" id="passchange"  type="button">Скрыть</button>
                        </div>
                    </div>
                    <p align="center" class="text-info">Требование к паролю: Минимум 8 символов, одна цифра, одна буква в верхнем регистре и одна в нижнем</p>
                </div>

                <input type="hidden" data-user name="user_id" value="<?= $_GET['id'] ?>">

                <?php if($result->access == 1): ?>
                <div class="form-group">
                    <label for="password13" class="col-sm-2 control-label">Выбор программы (по умолчанию будет выбранна в списке групп)</label>
                    <div class="col-sm-10">
                        <select class="form-control" data-user name="program_id" >
                            <option></option>
                            <?php $programms = $wpdb->get_results("SELECT * FROM p_programs"); ?>
                            <?php foreach ($programms as $programm):?>
                                <?php if($programm->id == $result->program_id): ?>
                                    <option value="<?= $programm->id ?>" selected><?= $programm->p_name ?></option>
                                <?php else: ?>
                                    <option value="<?= $programm->id ?>"><?= $programm->p_name ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
			</div><!--end .card-body -->

<?php if($result->access == 5): ?>
<?php $subjectRegion = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_user_fields_listeners WHERE user_id = %d", $result->user_id)) ?>
    <div class="form-group">
        <label for="Username5" class="col-sm-2 control-label">Предмет</label>
        <div class="col-sm-10">
            <select class="form-control" name="subject" data-user >
                <option></option>
                <?php $subjects = $wpdb->get_results("SELECT * FROM p_subject"); ?>
                <?php foreach ($subjects as $sbj):?>
                    <?php if($sbj->id == $subjectRegion->subject_id): ?>
                        <option value="<?= $sbj->id ?>" selected><?= $sbj->name ?></option>
                    <?php else: ?>
                        <option value="<?= $sbj->id ?>"><?= $sbj->name ?></option>
                    <?php endif;?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="Username5" class="col-sm-2 control-label">Регион</label>
        <div class="col-sm-10">
            <select class="form-control" name="region" data-user >
                <option></option>
                <?php $regions = $wpdb->get_results("SELECT * FROM p_region"); ?>
                <?php foreach ($regions as $region):?>
                    <?php if($region->id == $subjectRegion->region_id): ?>
                        <option value="<?= $region->id ?>" selected><?= $region->name ?></option>
                    <?php else: ?>
                        <option value="<?= $region->id ?>"><?= $region->name ?></option>
                    <?php endif;?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
<?php endif; ?>


			<div class="card-actionbar">
				<div class="card-actionbar-row">

					<input type="submit"  class="btn btn-success" value="Обновить пользователя">
				</div>
			</div>
		</div><!--end .card -->
	</form>
</div>

<?php else: ?>

    <?php alertStatus('danger','Нет данных!');?>

<?php endif; ?>


<script>
	
</script>


<?php else: ?>
	<br><?php alertStatus('warning', 'Нет доступа!', true) ?>
<?php endif; ?>