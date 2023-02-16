<?php 
global $wpdb;

$name_var = translateDir($_GET['id']);

if($name_var = translateDir($_GET['id']) == 'name'){
    $p_name = "p_name";
    $name = 'name';
    $lang_name = 'lang_name_ru';
    $name_org = 't_center';
    $langjs = 0;
}else{
    $p_name = "name_kaz";
    $name = "name_kaz";
    $lang_name = 'lang_name_kz';
    $name_org = "t_center_kaz";
    $langjs = 1;
}

$result = $wpdb->get_row($wpdb->prepare( "SELECT g.id, g.date_create, g.number_group, g.program_id, g.start_date, g.end_date, g.trener_id, g.training_center, t.name t_center,  t.name_kaz t_center_kaz,
g.active, p.p_name, p.name_kaz, f.surname t_surname, f.name t_name, f.patronymic t_patronymic
FROM p_groups g
LEFT JOIN p_programs p ON g.program_id = p.id
LEFT JOIN p_user_fields f ON g.trener_id = f.user_id
LEFT JOIN p_training_center t ON g.training_center = t.id
WHERE g.active = 1 AND g.id = %d", $_GET['id']));
$grinf = groupInfo($_GET['id']);
if($grinf->deleted == 1){
    alertStatus('warning','Регистрация в эту группу запрещена');
}else{

?>



<?php if ($result || ($result && $_GET['code'])): ?>
    <?php
    if ($_GET['code']){
        if( $metaKey = $wpdb->get_row($wpdb->prepare("SELECT user_id, meta_key FROM wp_usermeta WHERE meta_value = %s", $_GET['code'])) ){
            mailVeryficationMeta($metaKey->user_id);?>
            <br>
            <?php alertStatus('success', "<p class='lead'>".EMAIL_CONFIRMED." </p>");
        }else{
            alertStatus('warning', LINK_CODE_ERROR);
        }
    }
    ?>
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
	<div class="box"><br><?php if($_GET['login'] === 'failed') alertStatus('warning', WRONG_EMAIL_PASS); ?></div>
	<div class="card contain-sm style-transparent">
		<div class="card-body">
			<div class="row">
				<div class="col-sm-6">
					<br>
					<span class="text-lg text-bold text-primary"><?= LOGIN ?></span>
					<br><br>
					<form class="form floating-label" accept-charset="utf-8" method="post" action="/login/">
						<div class="form-group">
							<input type="text"  class="form-control" id="username" name="log" required>
							<label for="email">Email</label>
						</div>
						<div class="form-group">
							<input type="password" class="form-control" id="password" name="pwd" required>
							<label for="password"><?= PASSWORD ?></label>
						</div><button class="btn ink-reaction btn-flat btn-accent" id="passchange"  type="button"><?= PASS_SHOW_HIDE[0] ?></button>
						<br><br><br>
						<div class="row">
							<div class="col-xs-6 text-left">
								<div class="checkbox checkbox-inline checkbox-styled">
<!--								<label>-->
<!--									<input name="rememberme" type="checkbox" id="rememberme" value="forever"> <span>Запомнить</span>-->
<!--								</label>-->
								</div>
							</div><!--end .col -->
							<div class="col-xs-6 text-right">
								<button class="btn btn-primary btn-raised" type="submit"><?= COME_IN ?></button>
							</div><!--end .col -->
						</div><!--end .row -->
						<input type="hidden" name="redirect_to" value="<?= site_url() ?>/registration/?id=<?= $_GET['id'] ?>" />
						<input type="hidden" name="noGoToLogin" value="<?= site_url() ?>/registration/?id=<?= $_GET['id'] ?>" />
					</form>
				</div><!--end .col -->
				<div class="col-sm-5 col-sm-offset-1 text-center">
					<br><br>
						<h3 class="text-light">
							<?= DONT_HAVE_AN_ACCOUNT ?>
						</h3>
						<a class="btn btn-block btn-raised btn-primary" id="reg" href="#"><?= CREATE_AN_ACCOUNT ?></a>
						<br><br>
                        <h3 class="text-light">
                            <?= FORGOT_PASSWORD ?>
                        </h3>
                        <a class="btn btn-block btn-raised btn-primary" id="reg" href="https://portal.cpi-nis.kz/login/?action=lostpassword"><?= PASSWORD_RECOVERY ?></a>
                        <br><br>

				</div><!--end .col -->
			</div><!--end .row -->
		</div><!--end .card-body -->
	</div><!--end .card -->
	<div class="registration hide">
		<div class="row">
			<div class="col-lg-12">
				<h2 class="text-primary"><?= REGISTRATION[1] ?></h2>
			</div><!--end .col -->
			<div class="col-lg-8">
				<p class="lead">
                    <?= REGISTRATION[2] ?> <?= $result->number_group ?>, Тренер: <?= $result->t_surname ?> <?= $result->t_name ?> <?= $result->t_patronymic ?>
				</p>
			</div><!--end .col -->
		</div>
		<div class="col-lg-12">
			<form class="form-horizontal" method="POST" id="formreg" >
			<input type="hidden" data-user name="statement" value="1">
				<div class="card">
					<div class="card-head style-primary">
						<header><?= REGISTRATION[4] ?></header>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label for="Firstname5" class="col-sm-3 control-label"><?= REGISTRATION[5] ?></label>
									<div class="col-sm-8">
										<input type="text" required class="form-control" data-user name="u_surname"><div class="form-control-line"></div>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="Lastname5" class="col-sm-3 control-label"><?= REGISTRATION[6] ?></label>
									<div class="col-sm-8">
										<input type="text" required class="form-control" data-user name="u_name"><div class="form-control-line"></div>
									</div>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label for="Lastname5" class="col-sm-3 control-label"><?= REGISTRATION[7] ?></label>
									<div class="col-sm-8">
										<input type="text"  class="form-control" data-user  name="u_patronymic"><div class="form-control-line"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="Username5" class="col-sm-2 control-label"><?= REGISTRATION[8] ?></label>
							<div class="col-sm-10">
								<input type="text" required pattern="[0-9]{12}" data-user placeholder="ИИН" minlength="12" maxlength="12" class="form-control"  name="u_iin"><div class="form-control-line"></div>
							</div>
						</div>
                        <?php if ( $grinf->program_id == 11 ):?>
                            <div class="form-group">
                                <label for="Username5" class="col-sm-2 control-label">E-mail</label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <div class="input-group-content">
                                            <input type="text" id="mail_name" required class="form-control" data-user  name="u_email" pattern="^\S[a-zA-Z0-9_.-]+$">
                                            <div class="form-control-line"></div>
                                        </div>
                                        <div class="input-group-content">
                                            <select class="form-control" id="mail_domen" name="mailafter" data-mailafter required>
                                                <option value="@akt.nis.edu.kz">@akt.nis.edu.kz</option>
                                                <option value="@akb.nis.edu.kz">@akb.nis.edu.kz</option>
                                                <option value="@fmalm.nis.edu.kz">@fmalm.nis.edu.kz</option>
                                                <option value="@hbalm.nis.edu.kz">@hbalm.nis.edu.kz</option>
                                                <option value="@atr.nis.edu.kz">@atr.nis.edu.kz</option>
                                                <option value="@krg.nis.edu.kz">@krg.nis.edu.kz</option>
                                                <option value="@kt.nis.edu.kz">@kt.nis.edu.kz</option>
                                                <option value="@kst.nis.edu.kz">@kst.nis.edu.kz</option>
                                                <option value="@kzl.nis.edu.kz">@kzl.nis.edu.kz</option>
                                                <option value="@ib.nis.edu.kz">@ib.nis.edu.kz</option>
                                                <option value="@isa.nis.edu.kz">@isa.nis.edu.kz</option>
                                                <option value="@ast.nis.edu.kz">@ast.nis.edu.kz</option>
                                                <option value="@pvl.nis.edu.kz">@pvl.nis.edu.kz</option>
                                                <option value="@ptr.nis.edu.kz">@ptr.nis.edu.kz</option>
                                                <option value="@sm.nis.edu.kz">@sm.nis.edu.kz</option>
                                                <option value="@tk.nis.edu.kz">@tk.nis.edu.kz</option>
                                                <option value="@trz.nis.edu.kz">@trz.nis.edu.kz</option>
                                                <option value="@trk.nis.edu.kz">@trk.nis.edu.kz</option>
                                                <option value="@ura.nis.edu.kz">@ura.nis.edu.kz</option>
                                                <option value="@ukk.nis.edu.kz">@ukk.nis.edu.kz</option>
                                                <option value="@fmsh.nis.edu.kz">@fmsh.nis.edu.kz</option>
                                                <option value="@hbsh.nis.edu.kz">@hbsh.nis.edu.kz</option>
                                                <option value="@cpi.nis.edu.kz">@cpi.nis.edu.kz</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="form-group">
                                <label for="Username5" class="col-sm-2 control-label">E-mail</label>
                                <div class="col-sm-10">
                                    <input type="email" id="shortEmail" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" required class="form-control" data-user  name="u_email">
                                    <div class="col-md-9 inform" style="color: red;"></div>
                                    <div class="form-control-line"></div>
                                </div>
                            </div>
                        <?php endif;?>
						<div class="form-group">
							<label for="Username5" class="col-sm-2 control-label"><?= REGISTRATION[10] ?></label>
							<div class="col-sm-10">
								<input type="text" required class="form-control" data-user  name="u_tel"><div class="form-control-line"></div>
							</div>
						</div>
						<div class="form-group">
						<label for="Password5" class="col-sm-2 control-label"><?= REGISTRATION[11] ?></label>
							<div class="input-group">
								<div class="input-group-content">
									<input type="text" id="password2" data-user  class="form-control"  pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*" name="u_pass" required><div class="form-control-line"></div>
								</div>
								<div class="input-group-btn">
									<button class="btn btn-default" id="passchange2"  type="button"><?= REGISTRATION[13] ?></button>
								</div>
							</div>
                            <p align="center"><?= REGISTRATION[12] ?></p>
						</div>
					</div><!--end .card-body -->
                    
                    <input type="hidden" data-user name="activeStatus" value="<?= md5(date("Y-m-d") . "pass@realtime") ?>">
                    <input type="hidden" data-user name="groupid" value="<?= $_GET['id'] ?>">


                    <?php if($grinf->program_id != 12 && $grinf->program_id != 14 && $grinf->program_id != 17 && $grinf->program_id != 11 && $grinf->program_id != 6): ?>
                        <div class="form-group">
                            <label for="Username5" class="col-sm-2 control-label"><?= REGISTRATION[19] ?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="subject" data-user required>
                                    <option></option>
                                    <?php $subjects = $wpdb->get_results("SELECT * FROM p_subject"); ?>
                                    <?php foreach ($subjects as $sbj):?>
                                        <option value="<?= $sbj->id ?>"><?= $sbj->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="subject" data-user value="0">
                    <?php endif; ?>


                    <?php if( $grinf->program_id != 12 && $grinf->program_id != 11 && $grinf->program_id != 6): ?>
                        <div class="form-group">
                            <label for="Username5" class="col-sm-2 control-label"><?= REGISTRATION[14] ?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="region" data-user required>
                                    <option></option>
                                    <?php $regions = $wpdb->get_results("SELECT * FROM p_region"); ?>
                                    <?php foreach ($regions as $region):?>
                                        <option value="<?= $region->id ?>"><?= $region->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php else:?>
                        <input type="hidden" name="region" data-user value="0">
                    <?php endif; ?>

                    <?php if($grinf->program_id == 12): ?>
                        <div class="form-group">
                            <label for="Username5" class="col-sm-2 control-label"><?= REGISTRATION[21] ?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="lang" data-user required>
                                    <option></option>
                                    <?php $langs = $wpdb->get_results("SELECT * FROM p_lang"); ?>
                                    <?php foreach ($langs as $lang):?>
                                        <option value="<?= $lang->id ?>"><?= $lang->name_ru ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="lang" data-user value="0">
                    <?php endif; ?>

                    <div class="card-actionbar">
                        <div class="card-actionbar-row">
                            <p class="lead"><?= REGISTRATION[15] ?></p>
                            <input type="submit"  id="checkEmail" class="btn btn-success" value="<?= REGISTRATION[16] ?>"  disabled="disabled">

                        </div>
                    </div>
				</div><!--end .card -->


			</form>
		</div>
	</div>
	<?php else: ?>
    <?php
    if($grinf->trener_id == get_current_user_id()){
        //alertStatus('success', 'Вы являетесь тренером этой группы, регистрация не возможна! ');
    }else{
        if( $usercreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_groups_users ( `id_group`,`id_user` ) 
		VALUES (%d, %d)"
            ,$_GET['id']
            ,get_current_user_id()
        )) ){
            echo'<meta http-equiv="refresh" content="0;url=/registration/?id='.$_GET['id'].'" />';
        }
    }


        ?>
	<div class="row"><br><div class="box"></div>
	<h2 class="text-light text-center">Регистрация</h2>
			<div class="card card-outlined style-success">
				<div class="card-head">
					<header><i class="fa fa-fw fa-tag"></i> Регистрация в группу</header>
				</div><!--end .card-head -->
				<div class="card-body style-default-bright">
					<ul>
						<li><b><?= GROUP ?>:</b> <?= $result->number_group ?></li>
						<li><b>Тренер:</b> <?= $result->t_surname ?> <?= $result->t_name ?> <?= $result->t_patronymic ?></li>
						<li><b><?= PLACE_STUDY ?>:</b> <?= $result->$name_org ?></li>
						<li><b><?= PROGRAMM_NAME ?>:</b> <?= $result->$p_name ?></li>
						<li><b><?= DATE_BEGIN_DOWNLOAD ?>:</b> <?= $result->start_date ?></li>
						<li><b><?= DATE_END_DOWNLOAD ?>:</b> <?= $result->end_date ?></li>
					</ul>

                    <?php

                    if($grinf->trener_id == get_current_user_id()): ?>
                    <?php alertStatus('success', TRENER_MES); ?>
                    <?php else: ?>
					<form method="POST" id="formreg" >
						<input type="hidden" data-user name="statement" value="2">
						<input type="hidden" data-user name="activeStatus" value="<?= md5(date("Y-m-d") . "pass@realtime") ?>">
						<input type="hidden" data-user name="dataid" value="<?= $_GET['id'] ?>">
						<?php $res_group = $wpdb->get_row($wpdb->prepare("SELECT id FROM p_groups_users WHERE id_group = %d AND id_user = %d", $result->id, get_current_user_id())); ?>
						<?php if ($res_group):?>
                            <?php alertStatus('success', YOU_ARE_IN_THE_GROUP); ?> <a href="/users/?z=folders&id=<?= $_GET['id'] ?>" class="btn btn-info"><?= ($grinf->single_file == 1) ? DOWNLOAD_FILES2 : DOWNLOAD_FILES3 ?></a>
						<?php else: ?>
							<input type="submit" id="group_reg" value="<?= JOIN_GROUP ?>" class="btn ink-reaction btn-success">
						<?php endif; ?>
						
					</form>
                    <?php endif; ?>
				</div><!--end .card-body -->
			</div>
	</div>
	<?php endif; ?>

<?php else: ?>
<br>
<?php alertStatus('warning', LINK_ERROR); ?>
<?php endif; ?>
<input type="hidden" id="kz" value="<?= $langjs ?>">
<script>

    const mailDomen = document.querySelector("#mail_domen");
    const mailDomen2 = document.querySelector("#mail_domen2");
    const mailName = document.querySelector("#mail_name");
    const mailName2 = document.querySelector("#mail_name2");
    const errEmail = document.querySelector('#errEmail');

    const inform = document.querySelector('.inform'),
    submreg = document.querySelector("#checkEmail"),
    iemail = document.querySelector('#shortEmail');

    iemail.addEventListener('input', () => {
        const request = new XMLHttpRequest();
        request.open('POST','/server_user/?get_email=24');
        request.setRequestHeader('Content-type', 'application/json; charset=utf-8');
        request.send(JSON.stringify(iemail.value));

        request.addEventListener('load', () => {
            if (request.status === 200){
                //const data = JSON.parse(request.response);

                if(request.response > 0) {
                    iemail.style.border = '1px solid red';
                    inform.innerHTML = '<?= EMAIL_DUBL ?>';//`В системе уже есть ${request.response} запись`;
                    submreg.setAttribute("disabled", "disabled");
                } else {
                    inform.innerHTML = '';
                    iemail.style.border = 'none';
                    submreg.removeAttribute("disabled");
                }
            }else{
                inform.innerHTML = "Что-то пошло не так";
            }
        });
    });

</script>

<?php } ?>