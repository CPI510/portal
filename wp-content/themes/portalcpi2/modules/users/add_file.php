<?
global $wpdb;

if(!$_GET['user_id']) {$userid = get_current_user_id(); $accessUser = true;}
elseif($_GET['user_id'] == get_current_user_id()){$userid = get_current_user_id(); $accessUser = true; }
else {$userid = $_GET['user_id']; $accessUser = false;}
?>
<?php if(userAttach($_GET['user_id'], $_GET['trener'])):?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<h1 class="text-primary">Файлы</h1> <h3>Пользователя: <?php  nameUser($userid,3)?></h3>
<div class="card">

<?php if(!$_GET['folder_id']): ?>  <br><?php alertStatus('warning', 'Нет данных!') ?> <?php else:?>
<?php $results = $wpdb->get_results($wpdb->prepare("SELECT f.id, f.datecreate, f.user_id, f.folder, f.filename, f.filesize, l.name folder_name  
    FROM p_file f 
    INNER JOIN p_folder l ON l.id = f.folder WHERE user_id = %d AND f.folder = %d", $userid, $_GET['folder_id'])); ?>
    <div class="card-body">
        <table class="table no-margin table-hover" id="dataFile">
            <tbody>
                <tr>
                    <th>Папка</th>
                    <th>Название файла</th>
                    <th>Размер файла</th>
                    <th>Дата загрузки</th>
                    <th></th>
                </tr>
                <?php foreach($results as $res):?>
                <tr>
                    <td><?= $res->folder_name ?></td>
                    <td><a href="/server_file/?download=<?= $res->id ?>&uid=<?= $userid ?>" class="text-primary"><?= $res->filename ?></a></td>
                    <td><?= formatSizeUnits($res->filesize) ?></td>
                    <td><?= $res->datecreate ?></td>
                    <td><?php if($accessUser):?><a href="#" class="btn btn-icon-toggle" onclick="deleteFile('<?= $res->id ?>');" data-del="<?= $res->id ?>" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a><?php endif; ?>
</td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php if($accessUser):?>
        <br><br>
        <div id="ajax-respond" class="ajax-respond"></div>
        <input type="file" class="form-control" id="uploadInput" accept=".docx,.doc,.pptx,.ppt,.pdf">
        <button class="submit button btn ink-reaction btn-raised btn-primary" disabled="disabled" id="load">Загрузить файл</button>
        <?php endif; ?>
        
    </div>
    
</div>
<script src="<?= bloginfo('template_url') ?>/assets/js/core/actionsFile.js"></script>
<?php endif; ?>

<?php else:?>
<br><?php alertStatus('warning', 'Нет доступа!') ?>
<?php endif; ?>