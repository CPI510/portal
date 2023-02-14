<?php 
global $wpdb;

if($_POST['p_name'] && $_POST['active'] && getAccess(get_current_user_id())->access == 1) {
    $wpdb->query($wpdb->prepare( "INSERT INTO p_programs (p_name, name_kaz, active) VALUES (%s, %s, %s)", $_POST['p_name'], $_POST['name_kaz'], $_POST['active']));
    echo'<meta http-equiv="refresh" content="0;url=/programs/?z=list" />'; exit();
    } ?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="text-primary">Добавление программы</h2>
    </div><!--end .col -->
    <div class="col-lg-8">
        <p class="lead">
            Заполните поля для создания программы
        </p>
    </div><!--end .col -->
</div>

<div class="card">
    <div class="card-body">
        <form class="form-horizontal" role="form" method="POST">
            <div class="form-group">
                <label for="regular13" class="col-sm-2 control-label">Название программы</label>
                <div class="col-sm-10">
                    <input type="text" name="p_name" class="form-control" id="regular13"><div class="form-control-line"></div>
                </div>
                <label for="regular13" class="col-sm-2 control-label">Название программы на казахском</label>
                <div class="col-sm-10">
                    <input type="text" name="name_kaz" class="form-control" id="regular13"><div class="form-control-line"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="password13" class="col-sm-2 control-label">Активность</label>
                <div class="col-sm-10">
                    <select class="form-control" name="active">
                        <option value="1">Активно</option>
                        <option value="2">Не активно</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-2">
                    <input type="submit" class="btn btn-success" value="Добавить">
                </div>
            </div>
        </form>
    </div><!--end .card-body -->
</div>