<?php 
global $wpdb;

if(isset($_GET['paid']) && $_GET['paid'] != 'all'){
    $dopsql = "AND u.access = %d";
    $paid = $_GET['paid'];
}else{
    $dopsql = "";
    $paid = '';
}

//Pagination
// Переменная хранит число сообщений выводимых на станице
$num = 10;
// Извлекаем из URL текущую страницу
$page = $_GET['pageurl'];
// Определяем общее число сообщений в базе данных
if(getAccess(get_current_user_id())->access == 1){
    $posts = $wpdb->get_row($s2=$wpdb->prepare("SELECT COUNT(*) count  FROM p_user_fields u WHERE active = 1 $dopsql ", $paid))->count;
}else{
    $numU = nameUser(get_current_user_id(),4);
    $posts = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) count  FROM p_user_fields u WHERE active = 1 AND user_id in ($numU) $dopsql ", $paid))->count;
}
//echo $s2;
// Находим общее число страниц
$total = intval(($posts - 1) / $num) + 1;
// Определяем начало сообщений для текущей страницы
$page = intval($page);
// Если значение $page меньше единицы или отрицательно
// переходим на первую страницу
// А если слишком большое, то переходим на последнюю
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
// Вычисляем начиная к какого номера
// следует выводить сообщения
$start = $page * $num - $num;
// Выбираем $num сообщений начиная с номера $start

if($_GET['confirmemail'] && $_GET['user_id']){
    if(mailVeryficationMeta($_GET['user_id'])){
        //$text = "Email пользователя " . nameUser($_GET['user_id'], 5) . " подтвержден!";
        //alertStatus('success', $text);
        //echo'<meta http-equiv="refresh" content="0;url=/members/?z=list&searchtext='.$_GET['searchtext'].'confirmemaildone=1" />';
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Список пользователей</h1>
    </div>
    <div class="col-lg-12">
        <?php if(getAccess(get_current_user_id())->access == 1): ?><a href="/members/?z=add" class="btn btn-success">Добавить</a><?php endif;?>
    </div><!--end .col -->
    <div class="col-md-8">
        <article class="margin-bottom-xxl">
<?php if($_GET['confirmemail']){
    echo "<br>";
    $text = "Email пользователя " . nameUser($_GET['user_id'], 5) . " подтвержден!";
    alertStatus('success', $text);
} ?>
        </article>
    </div><!--end .col -->

    <?php if (getAccess(get_current_user_id())->access == 1): ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6">
                        <label for="regular13" class="col-sm-2 control-label">Поиск</label>
                        <input type="text" class="form-control" id="ifio" value="<?=$_GET['searchtext']?>">
                    </div>
                    <div class="col-md-9"><br>
                        <button class="btn btn-primary" id="searchbtn">Поиск</button>
                        <a href="/members/?z=list" class="btn btn-warning">Сбросить</a>
                    </div>


                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="col-sm-6">
                    <form>
                        <label><b>Уровень доступа (статус пользователя в системе)</b></label>
                        <select name="paid" class="form-control" onchange="this.form.submit()">
                            <option></option>
                            <option value="all" <?php if($_GET['paid'] == 'all') echo 'selected'; ?>>Все</option>
                            <?php $access = $wpdb->get_results("SELECT * FROM p_access WHERE active = 1"); ?>
                            <?php foreach ($access as $item): ?>
                                <?php if($_GET['paid'] == $item->value): ?>
                                    <option value="<?= $item->value ?>" selected><?= $item->name_ru ?></option>
                                <?php else: ?>
                                    <option value="<?= $item->value ?>"><?= $item->name_ru ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="z" value="<?= $_GET['z']?>">
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="col-md-12 inform">
                        <?php

                        if(getAccess(get_current_user_id())->access == 1){
                            $results = $wpdb->get_results($s=$wpdb->prepare("SELECT u.user_id, u.surname, u.name, u.patronymic, u.email, a.name_ru access_name_ru, a.name_kz access_name_kz
                                FROM p_user_fields u
                                INNER JOIN p_access a ON a.value = u.access WHERE u.active = 1
                                $dopsql
                                LIMIT $start, $num",$paid ));
                        }else{
                            $results = $wpdb->get_results($wpdb->prepare("SELECT u.user_id, u.surname, u.name, u.patronymic, u.email, a.name_ru access_name_ru, a.name_kz access_name_kz
                                FROM p_user_fields u
                                INNER JOIN p_access a ON a.value = u.access WHERE u.user_id in ($numU) and u.active = 1
                                LIMIT $start, $num"));
                        }
                        //echo $s;

                        if($_GET['id'] && $_GET['d'] && getAccess(get_current_user_id())->access == 1){
                            //$resd = $wpdb->query($wpdb->prepare("UPDATE p_user_fields SET active = 0 WHERE `user_id` = %d", $_GET['id']));
                            $wpdb->delete( 'p_user_fields', [ 'user_id'=>$_GET['id'] ], [ '%d' ] );
                            $wpdb->delete( 'p_groups_users', [ 'id_user'=>$_GET['id'] ], [ '%d' ] );
                            $wpdb->delete( 'wp_users', [ 'ID'=>$_GET['id'] ], [ '%d' ] );
                            $wpdb->delete( 'wp_usermeta', [ 'user_id'=>$_GET['id'] ], [ '%d' ] );
                            $wpdb->delete( 'p_file', [ 'user_id'=>$_GET['id'] ], [ '%d' ] );
                            $wpdb->delete( 'p_file_comments', [ 'user_id'=>$_GET['id'] ], [ '%d' ] );
                            $wpdb->delete( 'p_user_fields_listeners', [ 'user_id'=>$_GET['id'] ], [ '%d' ] );

                            $upload_dir = (object) wp_upload_dir();
                            $dir_comment = $upload_dir->basedir . "/users_file/" . $_GET['id'] . "/comments";
                            $dir_user = $upload_dir->basedir . "/users_file/" . $_GET['id'];

                            RDir($dir_comment);
                            RDir($dir_user);

                            echo'<meta http-equiv="refresh" content="0;url=/portalcpi/members/?z=list" />';
                        }
                        ?>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>ФИО</th>
                                <th>Email</th>
                                <th>Статус</th>
                                <th></th>
                                <th>Последний вход</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php $i  = ($_GET['pageurl']) ? ($_GET['pageurl'] - 1) * $num : 0;
                            foreach($results as $res): ?>
                                <tr>
                                    <td><?= ++$i ?></td>
                                    <td><a href="/members/?z=user_page&id=<?= $res->user_id ?>" class="text-primary" ><?= $res->surname ?> <?= $res->name ?> <?= $res->patronymic ?></a></td>
                                    <td><?= $res->email ?> <?php if(get_the_author_meta('mailveryfication', $res->user_id) == '0') echo "<code>не подтвержден</code> <a href='/members/?z=list&searchtext={$res->surname}&user_id={$res->user_id}&confirmemail=1' class='btn btn-primary btn-xs'>Подтвердить email</a>" ?></td>
                                    <td><?= $res->{"access_name_" . $_SESSION['lang']} ?></td>
                                    <td>
                                        <?php if(getAccess(get_current_user_id())->access == 1 && $res->user_id != 1 && (get_current_user_id() == 1 || get_current_user_id() == 349 || get_current_user_id() == 340 )): ?>
                                            <a href="/members/?z=list&authAnoherid=<?= $res->user_id ?>" class="text-success">Войти под учеткой</a>
                                        <?php endif;?>
                                        <?php if(getAccess(get_current_user_id())->access == 1 && $res->user_id != 1): ?>
                                            <a href="/members/?z=edit&id=<?= $res->user_id ?>" class="btn btn-icon-toggle" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
                                            <a href="/members/?z=list&d=d&id=<?= $res->user_id ?>" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
                                        <?php endif;?>
                                    </td>
                                    <td><?= lastlogin($res->user_id)  ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <ul class="pagination">
                            <?php
                            if (isset($_GET['paid']) && !empty($_GET['paid'])) {
                                $paidtext = "&paid={$_GET['paid']}";
                            } else {
                                $paidtext = "";
                            }
                            // Проверяем нужны ли стрелки назад
                            if ($page != 1) $pervpage = "<li><a href='/members/?z=$_GET[z]&pageurl=1$paidtext'> << </a></li><li><a href='/members/?z=$_GET[z]&pageurl=".($page - 1)."$paidtext'> < </a></li>";
                            // Проверяем нужны ли стрелки вперед
                            if ($page != $total) $nextpage = "<li><a href='/members/?z=$_GET[z]&pageurl=".($page + 1)."$paidtext'> > </a></li><li><a href='/members/?z=$_GET[z]&pageurl=$total$paidtext'> >> </a></li>";

                            // Находим две ближайшие станицы с обоих краев, если они есть
                            if($page - 2 > 0) $page2left = "<li><a href='/members/?z=$_GET[z]&pageurl=".($page - 2)."$paidtext'>".($page - 2)."</a></li>";
                            if($page - 1 > 0) $page1left = "<li><a href='/members/?z=$_GET[z]&pageurl=".($page - 1)."$paidtext'>".($page - 1)."</a></li>";
                            if($page + 2 <= $total) $page2right = "<li><a href='/members/?z=$_GET[z]&pageurl=".($page + 2)."$paidtext'>".($page + 2)."</a></li>";
                            if($page + 1 <= $total) $page1right = "<li><a href='/members/?z=$_GET[z]&pageurl=".($page + 1)."$paidtext'>".($page + 1)."</a></li>";

                            // Вывод меню
                            echo $pervpage.$page2left.$page1left.'<li class="active"><a href="#">'.$page.'</a></li>'.$page1right.$page2right.$nextpage;
                            ?>
                        </ul>
                    </div><!--end .table-responsive -->
                </div>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
</div>

<script>
    const ifio = document.querySelector('#ifio'),
        inform = document.querySelector('.inform'),
        searchbtn = document.querySelector('#searchbtn');

    <?php if($_GET['searchtext']){
            echo "window.onload = function(){
            searchFunc();
        }";
    } ?>

    const message = {
        loading: `${document.location.origin}/wp-content/themes/portalcpi/assets/img/spinner.svg`,
        success: "Спасибо, все данные внесены",
        failure: "Что-то пошло не так, попробуйте зайти позднее!"
    };

    searchbtn.addEventListener('click', searchFunc);

    function searchFunc(){
        const spinner = document.createElement('img');
        spinner.src = message.loading;
        inform.innerHTML = `<img src=${message.loading}>`;

        if(ifio.value.length > 2){
            const request = new XMLHttpRequest();
            request.open('POST','/server_user/?get_user=1');
            request.setRequestHeader('Content-type', 'application/json; charset=utf-8');
            request.send(JSON.stringify(ifio.value.toUpperCase()));

            request.addEventListener('load', () => {
                if (request.status === 200){
                //const data = JSON.parse(request.response);
                //console.log(request.response);
                inform.innerHTML = `<br>${request.response}`;
            }else{
                inform.innerHTML = "Что-то пошло не так";
            }
        });
        }else{
            inform.innerHTML = "<br>Текст поиска должен быть больше 2 символов!";
        }
    }
</script>