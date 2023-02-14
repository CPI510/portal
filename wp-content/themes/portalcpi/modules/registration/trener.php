<?php
global $wpdb;

if (is_user_logged_in()){
    redirectBack();
}
?>


<?php
if ($_GET['code']){
    if( $metaKey = $wpdb->get_row($wpdb->prepare("SELECT user_id, meta_key FROM wp_usermeta WHERE meta_value = %s", $_GET['code'])) ){
        mailVeryficationMeta($metaKey->user_id);?>
        <br>
        <?php alertStatus('success', "<p class='lead'>Ваш Email подтвержден!</p>");
    }else{
        alertStatus('warning', 'Ссылка содержит ошибочные данные или код был подтвержден!');
    }
}
?>

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
        <div class="card contain-sm style-transparent <?=($_GET['code']) ? "":"hide" ?>">
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
        <div class="registration <?=($_GET['code']) ? "hide":"" ?>">
            <div class="row">
                <div class="col-lg-12">
                        <h2 class="text-primary">Регистрация тренера</h2>
                </div><!--end .col -->
                <div class="col-lg-8">
                    <p class="lead">
                        Заполните поля
                    </p>
                </div><!--end .col -->
            </div>
            <div class="col-lg-12">
                <form class="form-horizontal" method="POST" id="formreg" >
                    <input type="hidden" data-user name="statement" value="3">
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
                                            <input type="text"  class="form-control" data-user  name="u_patronymic"><div class="form-control-line"></div>
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
                                        <input type="email" id="shortEmail" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" required class="form-control" data-user  name="u_email">
                                        <div class="form-control-line"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Username5" class="col-sm-2 control-label">Подтвердите E-mail</label>
                                    <div class="col-sm-10">
                                        <input type="email" id="shortEmail2" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" required class="form-control"  name="u_email">
                                        <div class="form-control-line"></div><div id="errEmail" class="hide"><span class="text-danger">Поля E-mail не совпадают </span></div>
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
                                        <input type="text" id="password2" data-user  class="form-control" value="<?= wp_generate_password( 12 ) ?>" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*" name="u_pass" required><div class="form-control-line"></div>
                                    </div>
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" id="passchange2"  type="button">Скрыть</button>
                                    </div>
                                </div>
                            </div>
                        </div><!--end .card-body -->
                        <div class="card-actionbar">
                            <div class="card-actionbar-row">
                                <p class="lead">Требование к паролю: Минимум 8 символов, одна цифра, одна буква в верхнем регистре и одна в нижнем</p>
                                <p class="lead">Даю свое согласие на обработку персональных данных</p>
                                <input type="submit"  id="checkEmail" class="btn btn-success" value="Да я согласен(а), зарегистрироваться">

                            </div>
                        </div>
                    </div><!--end .card -->
                    <input type="hidden" data-user name="activeStatus" value="<?= md5(date("Y-m-d") . "pass@realtime") ?>">
                    <input type="hidden" data-user name="groupid" value="">
                </form>
            </div>
        </div>


        </div>

<script>
    const mailDomen = document.querySelector("#mail_domen");
    const mailDomen2 = document.querySelector("#mail_domen2");
    const mailName = document.querySelector("#mail_name");
    const mailName2 = document.querySelector("#mail_name2");
    const checkEmail = document.querySelector('#checkEmail');
    const errEmail = document.querySelector('#errEmail');

    if(mailDomen && mailDomen2 && mailName && mailName2){
        mailDomen.addEventListener('input', () => {
            if(mailDomen.value == mailDomen2.value && mailName.value == mailName2.value){
            checkEmail.removeAttribute('disabled');
            errEmail.classList.remove("show");
            errEmail.classList.add("hide");
        }else{
            checkEmail.setAttribute('disabled','disabled');
            errEmail.classList.remove("hide");
            errEmail.classList.add("show");
        }
    })
        mailDomen2.addEventListener('input', () => {
            if(mailDomen.value == mailDomen2.value && mailName.value == mailName2.value){
            checkEmail.removeAttribute('disabled');
            errEmail.classList.remove("show");
            errEmail.classList.add("hide");
        }else{
            checkEmail.setAttribute('disabled','disabled');
            errEmail.classList.remove("hide");
            errEmail.classList.add("show");
        }
    })
        mailName.addEventListener('input', () => {
            if(mailDomen.value == mailDomen2.value && mailName.value == mailName2.value){
            checkEmail.removeAttribute('disabled');
            errEmail.classList.remove("show");
            errEmail.classList.add("hide");
        }else{
            checkEmail.setAttribute('disabled','disabled');
            errEmail.classList.remove("hide");
            errEmail.classList.add("show");
        }
    })
        mailName2.addEventListener('input', () => {
            if(mailDomen.value == mailDomen2.value && mailName.value == mailName2.value){
            checkEmail.removeAttribute('disabled');
            errEmail.classList.remove("show");
            errEmail.classList.add("hide");
        }else{
            checkEmail.setAttribute('disabled','disabled');
            errEmail.classList.remove("hide");
            errEmail.classList.add("show");
        }
    })
    }



    const shortEmail = document.querySelector("#shortEmail");
    const shortEmail2 = document.querySelector("#shortEmail2");

    if(shortEmail && shortEmail2){
        shortEmail.addEventListener('input', () => {
            if(shortEmail.value == shortEmail2.value){
            checkEmail.removeAttribute('disabled');
            errEmail.classList.remove("show");
            errEmail.classList.add("hide");
        }else{
            checkEmail.setAttribute('disabled','disabled');
            errEmail.classList.remove("hide");
            errEmail.classList.add("show");
        }
    })
        shortEmail2.addEventListener('input', () => {
            if(shortEmail.value == shortEmail2.value){
            checkEmail.removeAttribute('disabled');
            errEmail.classList.remove("show");
            errEmail.classList.add("hide");
        }else{
            checkEmail.setAttribute('disabled','disabled');
            errEmail.classList.remove("hide");
            errEmail.classList.add("show");
        }
    })
    }

</script>