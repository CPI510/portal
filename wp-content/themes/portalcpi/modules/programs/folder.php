<?php 
global $wpdb;

if($_POST['f_name'] && $_POST['action'] && $_POST['id_program']) {
    if($_POST['action'] == 'edit'){
        $wpdb->query($wpdb->prepare( "UPDATE p_folder set name = %s, id_program = %d, access_id = %s, date = NOW() WHERE id = %d", $_POST['f_name'], $_POST['id_program'], implode(',', $_POST['accessAttach']), $_POST['id']));
    }else{
        $wpdb->query($wpdb->prepare( "INSERT INTO p_folder (name, id_program, access_id) VALUES (%s, %s, %s)", $_POST['f_name'], $_POST['id_program'], implode(',', $_POST['accessAttach'])));
    }
    
    echo'<meta http-equiv="refresh" content="0;url=/portalcpi/program/?z=folder" />'; exit();
    printALL($_POST);
    } ?>

<?php if($_GET['add'] || $_GET['edit']):?>
<?php if($_GET['edit']) $resFolder = $wpdb->get_row($wpdb->prepare(" SELECT * FROM p_folder WHERE id = %d", $_GET['id'])); ?>
<!-- Добавление и редактирование папок -->
<div class="row">
    <div class="col-lg-12">
        <h2 class="text-primary"><?= ($_GET['edit']) ? 'Редактирование' : 'Добавление' ?> папок</h2>
    </div><!--end .col -->
    <div class="col-lg-8">
        <p class="lead">
            Заполните поля для <?= ($_GET['edit']) ? 'редактирование' : 'создание' ?> папок
        </p>
    </div><!--end .col -->
</div>

<div class="card">
    <div class="card-body">
        <form class="form-horizontal" role="form" method="POST">
            <div class="form-group">
                <label for="regular13" class="col-sm-2 control-label">Название паки</label>
                <div class="col-sm-10">
                    <input type="text" name="f_name" class="form-control" id="regular13" value="<?= ($_GET['edit']) ? $resFolder->name : '' ?>"><div class="form-control-line"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="password13" class="col-sm-2 control-label">Программа</label>
                <div class="col-sm-10">
                <?php $resPr = $wpdb->get_results("SELECT * FROM p_programs") ?>
                    <select class="form-control" name="id_program">
                    <?php foreach($resPr as $resp): ?>       
                        <?php if($resFolder->id_program == $resp->id ): ?>
                            <option value="<?= $resp->id ?>" selected="selected"><?= $resp->p_name ?></option>
                        <?php else:?>
                            <option value="<?= $resp->id ?>"><?= $resp->p_name ?></option>
                        <?php endif; ?>
                    <?php endforeach;?>
                    </select>
                </div>
            </div>
            <?php
            foreach(explode(",", $resFolder->access_id) as $accessid){
                $arrAccess[$accessid] = $accessid;
            }
            ?>
            <div class="form-group">
                <label for="password13" class="col-sm-2 control-label">Принадлежность папок</label>
                <div class="col-sm-10">
                    <select class="form-control select2-list " name="accessAttach[]" multiple="" data-attached id="attached">
                    <option></option>	
                    <?php $resultsAccess = $wpdb->get_results("SELECT * FROM p_access "); ?>					
                        <?php foreach ($resultsAccess as $value):?>
                                <?php if($arrAccess[$value->id] == $value->id): ?>
                                    <option value="<?= $value->id ?>" selected><?= $value->{"name_" . $_SESSION['lang']} ?></option>
                                <?php else: ?>
                                    <option value="<?= $value->id ?>"><?= $value->{"name_" . $_SESSION['lang']} ?></option>
                                <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-2">
                    <input type="hidden" name="action" value="<?= ($_GET['edit']) ? 'edit' : 'add' ?>">
                    <input type="hidden" name="id" value="<?= $resFolder->id ?>">
                    <input type="submit" class="btn btn-success" value="<?= ($_GET['edit']) ? 'Изменить' : 'Добавить' ?>">
                </div>
            </div>
        </form>
    </div><!--end .card-body -->
</div>
<!-- Конец добавления и редактирование папок -->


<?php else: ?>

<!-- Список папок -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Список папок</h1>
    </div>
    <div class="col-lg-12">
        <a href="/programs/?z=folder&add=true" class="btn btn-success">Добавить</a>
    </div><!--end .col -->
    <div class="col-md-8">
        <article class="margin-bottom-xxl">

        </article>
    </div><!--end .col -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="form-group">
                        <label for="password13" class="col-sm-2 control-label">Программа</label>
                        <div class="col-sm-10">
                            <form method="GET" action="/programs/">
                                <input type="hidden" name="z" value="folder">
                                <select class="form-control" name="p_program" onchange="this.form.submit();" >
                                <option></option>
                                <?php $results = $wpdb->get_results("SELECT * FROM p_programs;") ?>
                                <?php foreach ($results as $value):?>
                                    <?php if($_GET['p_program'] == $value->id): ?>
                                        <option value="<?= $value->id ?>" selected><?= $value->p_name ?></option>
                                    <?php else: ?>
                                        <option value="<?= $value->id ?>"><?= $value->p_name ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название папки</th>
                                <th>Программа</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if($_GET['p_program']) $where = "WHERE p.id = %d";
                        
                            $results = $wpdb->get_results($wpdb->prepare("SELECT  f.id, f.name, f.id_program, p.p_name
                            FROM p_folder f
                            LEFT OUTER JOIN p_programs p ON p.id = f.id_program $where", $_GET['p_program']));
                            if($_GET['id'] && $_GET['d']){ 
                                $resd = $wpdb->query($wpdb->prepare("DELETE FROM `p_folder` WHERE `p_folder`.`id` = %d", $_GET['id'])); 
                                if(!$resd) alertStatus('warning', 'Нет данных');
                                else echo'<meta http-equiv="refresh" content="0;url=/portalcpi/program/?z=folder" />'; 
                            }  
                            
                        ?>
                        <?php foreach($results as $res): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $res->name ?></td>
                                <td><?= ($res->p_name) ? $res->p_name : "Для тимлидеров, экспертов, тренеров" ?></td>
                                <td>
                                    <a href="/programs/?z=folder&edit=true&id=<?= $res->id ?>" class="btn btn-icon-toggle" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
                                    <a href="/programs/?z=folder&d=d&id=<?= $res->id ?>" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
					            </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
</div>
<!-- Конец список папок -->

<?php endif; ?>