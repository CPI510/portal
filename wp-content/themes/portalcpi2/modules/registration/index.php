<?php 
global $wpdb;
?>

<?php $result = $wpdb->get_row($wpdb->prepare( "SELECT g.id, g.date_create, g.number_group, g.program_id, g.start_date, g.end_date, g.trener_id, g.training_center, t.name t_center,
g.active, p.p_name, f.surname t_surname, f.name t_name, f.patronymic t_patronymic
FROM p_groups g
LEFT JOIN p_programs p ON g.program_id = p.id
LEFT JOIN p_user_fields f ON g.trener_id = f.user_id
LEFT JOIN p_training_center t ON g.training_center = t.id
WHERE g.active = 1 AND g.id = %d", $_GET['id'])); ?>	

<?php if($result): ?>

	<?php if(!is_user_logged_in()): ?>
	<style>
        .hide {
            display: none;
        }
        .show {
            display: block;
        }
        .fade{animation-name: fade;animation-duration: 1.5s;}@keyframes fade{from{opacity: 0.1;}to{opacity: 1;}}
    </style>
	<div class="spacer"></div>
	<div class="box"><br><?php if($_GET['login'] === 'failed') alertStatus('warning', 'Неверный логин или пароль!'); ?></div>
	<div class="card contain-sm style-transparent">
		<div class="card-body">
			<div class="row">
				<div class="col-sm-6">
					<br>
					<span class="text-lg text-bold text-primary">Вход в систему</span>
					<br><br>
					<form class="form floating-label" accept-charset="utf-8" method="post" action="/login/">
						<div class="form-group">
							<input type="text"  class="form-control" id="username" name="log" required>
							<label for="email">Email</label>
						</div>
						<div class="form-group">
							<input type="password" class="form-control" id="password" name="pwd" required>
							<label for="password">Пароль</label>
						</div><button class="btn ink-reaction btn-flat btn-accent" id="passchange"  type="button">Показать пароль</button>
						<br><br><br>
						<div class="row">
							<div class="col-xs-6 text-left">
								<div class="checkbox checkbox-inline checkbox-styled">
								<label>
									<input name="rememberme" type="checkbox" id="rememberme" value="forever"> <span>Запомнить</span>
								</label>
								</div>
							</div><!--end .col -->
							<div class="col-xs-6 text-right">
								<button class="btn btn-primary btn-raised" type="submit">Войти</button>
							</div><!--end .col -->
						</div><!--end .row -->
						<input type="hidden" name="redirect_to" value="http://cpi.nis.edu.kz/registration/?id=<?= $_GET['id'] ?>" />
						<input type="hidden" name="noGoToLogin" value="http://cpi.nis.edu.kz/registration/?id=<?= $_GET['id'] ?>" />
					</form>
				</div><!--end .col -->
				<div class="col-sm-5 col-sm-offset-1 text-center">
					<br><br>
						<h3 class="text-light">
							Нет учетной записи?
						</h3>
						<a class="btn btn-block btn-raised btn-primary" id="reg" href="#">Создание учетной записи</a>
						<br><br>
							
				</div><!--end .col -->
			</div><!--end .row -->
		</div><!--end .card-body -->
	</div><!--end .card -->
	<div class="registration hide">
		<div class="row">
			<div class="col-lg-12">
				<h2 class="text-primary">Регистрация пользователя</h2>
			</div><!--end .col -->
			<div class="col-lg-8">
				<p class="lead">
					Заполните поля для регистрации в группу <?= $result->number_group ?>, Тренер: <?= $result->t_surname ?> <?= $result->t_name ?> <?= $result->t_patronymic ?>
				</p>
			</div><!--end .col -->
		</div>
		<div class="col-lg-12">
			<form class="form-horizontal" method="POST" id="formreg" >
			<input type="hidden" data-user name="statement" value="1">
				<div class="card">
					<div class="card-head style-primary">
						<header>Форма для регистрации</header>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label for="Firstname5" class="col-sm-3 control-label">Фамилия</label>
									<div class="col-sm-8">
										<input type="text" required class="form-control" data-user name="u_surname"><div class="form-control-line"></div>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="Lastname5" class="col-sm-3 control-label">Имя</label>
									<div class="col-sm-8">
										<input type="text" required class="form-control" data-user name="u_name"><div class="form-control-line"></div>
									</div>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label for="Lastname5" class="col-sm-3 control-label">Отчество</label>
									<div class="col-sm-8">
										<input type="text" required class="form-control" data-user  name="u_patronymic"><div class="form-control-line"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="Username5" class="col-sm-2 control-label">ИИН</label>
							<div class="col-sm-10">
								<input type="text" required pattern="[0-9]{12}" data-user placeholder="ИИН" minlength="12" maxlength="12" class="form-control"  name="u_iin"><div class="form-control-line"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="Username5" class="col-sm-2 control-label">E-mail</label>
							<div class="col-sm-10">
								<input type="email" required class="form-control" data-user  name="u_email"><div class="form-control-line"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="Username5" class="col-sm-2 control-label">Контактный телефон</label>
							<div class="col-sm-10">
								<input type="text" required class="form-control" data-user  name="u_tel"><div class="form-control-line"></div>
							</div>
						</div>
						<div class="form-group">
						<label for="Password5" class="col-sm-2 control-label">Пароль</label>
							<div class="input-group">
								<div class="input-group-content">
									<input type="text" id="password2" data-user  class="form-control" value="<?= wp_generate_password( 12 ) ?>" name="u_pass" required><div class="form-control-line"></div>
								</div>
								<div class="input-group-btn">
									<button class="btn btn-default" id="passchange2"  type="button">Скрыть</button>
								</div>
							</div>
						</div>											
					</div><!--end .card-body -->
					<div class="card-actionbar">
						<div class="card-actionbar-row">
							<input type="submit"   class="btn btn-success" value="Зарегистрироваться">
						</div>
					</div>
				</div><!--end .card -->
				<input type="hidden" data-user name="activeStatus" value="<?= md5(date("Y-m-d") . "pass@realtime") ?>">
			</form>
		</div>
	</div>
	<?php else: ?>
	<div class="row"><br><div class="box"></div>
	<h2 class="text-light text-center">Регистрация</h2>
			<div class="card card-outlined style-success">
				<div class="card-head">
					<header><i class="fa fa-fw fa-tag"></i> Регистрация в группу</header>
				</div><!--end .card-head -->
				<div class="card-body style-default-bright">
					<ul>
						<li><b>Номер группы:</b> <?= $result->number_group ?></li>
						<li><b>Тренер:</b> <?= $result->t_surname ?> <?= $result->t_name ?> <?= $result->t_patronymic ?></li>
						<li><b>Место обучения:</b> <?= $result->t_center ?></li>
						<li><b>Программа:</b> <?= $result->p_name ?></li>
						<li><b>Дата начала загрузки файлов:</b> <?= $result->start_date ?></li>
						<li><b>Дата окончания загрузки файлов:</b> <?= $result->end_date ?></li>
					</ul>
					<form method="POST" id="formreg" >
						<input type="hidden" data-user name="statement" value="2">
						<input type="hidden" data-user name="activeStatus" value="<?= md5(date("Y-m-d") . "pass@realtime") ?>">
						<input type="hidden" data-user name="dataid" value="<?= $_GET['id'] ?>">
						<?php $res_group = $wpdb->get_row($wpdb->prepare("SELECT id FROM p_groups_users WHERE id_group = %d AND id_user = %d", $result->id, get_current_user_id())); ?>
						<?php if ($res_group):?>
						<?php alertStatus('warning', 'Вы уже записаны в группу!'); ?>
						<?php else: ?>
							<input type="submit" id="group_reg" value="Записаться в группу" class="btn ink-reaction btn-success">
						<?php endif; ?>
						
					</form>
				</div><!--end .card-body -->
			</div>
	</div>
	<?php endif; ?>

<?php else: ?>
<br>
<?php alertStatus('warning', 'Ссылка содержит ошибочные данные или группа не активна!'); ?>
<?php endif; ?>
