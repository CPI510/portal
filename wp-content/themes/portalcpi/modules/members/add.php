<?php 
global $wpdb;
?>
<?php checkPermissons(['var_1' => 1, 'var_4' => 4]); ?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="text-primary">Добавление пользователя</h2>
    </div><!--end .col -->
    <div class="col-lg-8">
        <p class="lead">
            Заполните поля для создания пользователя
        </p>
    </div><!--end .col -->
</div>
<div class="box"></div>
<div class="col-lg-12">
	<form class="form-horizontal" method="POST" id="formreg" data-form="add" >
	<input type="hidden" data-user name="statement" value="1">
		<div class="card">
			<div class="card-head style-primary">
				<header>Форма для создании пользователя</header>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-4">
						<div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
							<label for="Firstname5" class="col-sm-3 control-label">Тегі/Фамилия</label>
							<div class="col-sm-8">
								<input type="text" required class="form-control" data-user name="u_surname"><div class="form-control-line"></div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
							<label for="Lastname5" class="col-sm-3 control-label">Аты/Имя</label>
							<div class="col-sm-8">
								<input type="text" required class="form-control" data-user name="u_name"><div class="form-control-line"></div>
							</div>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
							<label for="Lastname5" class="col-sm-3 control-label">Әкесінің аты/Отчество</label>
							<div class="col-sm-8">
								<input type="text"  class="form-control" data-user  name="u_patronymic"><div class="form-control-line"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
					<label for="Username5" class="col-sm-2 control-label">ИИН</label>
					<div class="col-sm-10">
						<input type="text" required pattern="[0-9]{12}" data-user placeholder="ИИН" minlength="12" maxlength="12" class="form-control"  name="u_iin"><div class="form-control-line"></div>
					</div>
				</div>
				<div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
					<label for="Username5" class="col-sm-2 control-label">E-mail</label>
					<div class="col-sm-10">
						<input type="email" required class="form-control" data-user  name="u_email"><div class="form-control-line"></div>
					</div>
				</div>
				<div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
					<label for="Username5" class="col-sm-2 control-label">Байланыс/Контактный телефон</label>
					<div class="col-sm-10">
						<input type="text" required class="form-control" data-user  name="u_tel"><div class="form-control-line"></div>
					</div>
				</div>

                <?php if(($_POST['fileuserdata'] != 'adduser') ): ?>
                    <div class="form-group">
                        <label for="password13" class="col-sm-2 control-label">Уровень доступа</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="u_access" data-user  required>
                                <option></option>
                                <?php $results = $wpdb->get_results("SELECT * FROM p_access WHERE active = 1;") ?>
                                <?php foreach ($results as $value):?>
                                    <option value="<?= $value->value ?>"><?= $value->name_ru ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php else:?>
                <input type="hidden" name="u_access" data-user value="5">
                <?php endif; ?>

				<div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
				<label for="Password5" class="col-sm-2 control-label">Құпия сөз/Пароль</label>
					<div class="input-group">
						<div class="input-group-content">
							<input type="text" id="password" data-user  class="form-control" value="<?= wp_generate_password( 12 ) ?>" name="u_pass" required><div class="form-control-line"></div>
						</div>
						<div class="input-group-btn">
							<button class="btn btn-default" id="passchange"  type="button">Скрыть</button>
						</div>
					</div>
				</div>
                    <div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
                        <label for="Username5" class="col-sm-2 control-label">Пән/Предмет</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="subject" data-user >
                                <option></option>
                                <?php $subjects = $wpdb->get_results("SELECT * FROM p_subject"); ?>
                                <?php foreach ($subjects as $sbj):?>
                                    <option value="<?= $sbj->id ?>"><?= $sbj->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>


                <div class="<?= ($_POST['fileuserdata'] == 'adduser') ? "":"form-group" ?>">
                    <label for="Username5" class="col-sm-2 control-label">Тұру аймағы/Регион проживания</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="region" data-user >
                            <option></option>
                            <?php $regions = $wpdb->get_results("SELECT * FROM p_region"); ?>
                            <?php foreach ($regions as $region):?>
                                <option value="<?= $region->id ?>"><?= $region->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
			</div><!--end .card-body -->
            <?php if(($_POST['fileuserdata'] != 'adduser') ): ?>
			<div class="card-actionbar">
				<div class="card-actionbar-row">
					<input type="submit"  class="btn btn-success" value="Создать пользователя">
				</div>
			</div>
            <?php endif; ?>
		</div><!--end .card -->
	</form>
</div>
