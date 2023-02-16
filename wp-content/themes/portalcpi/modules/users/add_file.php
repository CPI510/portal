<?php
global $wpdb;

if($name_var = translateDir($_GET['group_id']) != 'name'){
    $p_name = "name_kaz";
    $name = "name_kaz";
    $lang_name = 'lang_name_kz';
    $name_org = "name_org_kaz";
}else{
    $p_name = "p_name";
    $name = 'name';
    $lang_name = 'lang_name_ru';
    $name_org = 'name_org';
}

$grinf = groupInfo($_GET['group_id']);
if(!$_GET['user_id']) {$userid = get_current_user_id(); $accessUser = true;}
elseif($_GET['user_id'] == get_current_user_id()){$userid = get_current_user_id(); $accessUser = true; }
else {$userid = $_GET['user_id']; $accessUser = false;}

if( $grinf->program_id == 15 && isset($_GET['portfolio'])){
$files = $wpdb->get_results($wpdb->prepare('SELECT * FROM p_file WHERE user_id = %d AND group_id = %d AND portfolio = 1 AND folder = %d', get_current_user_id(), $_GET['group_id'], $_GET['folder_id']));

if(count($files) >= 1){
    foreach ($files as $file){
        $wpdb->update('p_file',
            [ 'portfolio' => '0' ],
            [ 'id' => $file->id ],
            ['%d'],['%d']);
    }
    $wpdb->update('p_file',
        [ 'portfolio' => '1' ],
        [ 'id' => $_GET['portfolio'] ],
        ['%d'],['%d']);
}else{
    $wpdb->update('p_file',
        [ 'portfolio' => '1' ],
        [ 'id' => $_GET['portfolio'] ],
        ['%d'],['%d']);
    echo'<meta http-equiv="refresh" content="0;url=/users/?z=add_file&folder_id='.$_GET['folder_id'].'&group_id='.$_GET['group_id'].'" />';
}


}elseif ($grinf->program_id == 14 && isset($_GET['notportfolio'])){
    $wpdb->update('p_file',
        [ 'portfolio' => '0' ],
        [ 'id' => $_GET['notportfolio'] ],
        ['%d'],['%d']);
    echo'<meta http-equiv="refresh" content="0;url=/users/?z=add_file&folder_id='.$_GET['folder_id'].'&group_id='.$_GET['group_id'].'" />';
}else{
    if($_GET['portfolio']){
        $wpdb->update('p_file',
            [ 'portfolio' => '1' ],
            [ 'id' => $_GET['portfolio'] ],
            ['%d'],['%d']);
        echo'<meta http-equiv="refresh" content="0;url=/users/?z=add_file&folder_id='.$_GET['folder_id'].'&group_id='.$_GET['group_id'].'" />';
    }elseif($_GET['notportfolio']){
        $wpdb->update('p_file',
            [ 'portfolio' => '0' ],
            [ 'id' => $_GET['notportfolio'] ],
            ['%d'],['%d']);
        echo'<meta http-equiv="refresh" content="0;url=/users/?z=add_file&folder_id='.$_GET['folder_id'].'&group_id='.$_GET['group_id'].'" />';
    }
}


?>

<?php

if(substr($grinf->end_date, 0,-9) == substr(dateTime(), 0,-9)){
    alertStatus('warning','<h3>Уважаемый слушатель. Сообщаем, что доступ к порталу ЦПИ будет закрыт сегодня в '.substr($grinf->end_date,10, -3).'. Напоминаем о  необходимости загрузки отчетов до указанного времени</h3>');
}
?>
<style>
    .field__wrapper {
        width: 100%;
        position: relative;
        margin: 15px 0;
        text-align: center;
    }

    .field__file {
        opacity: 0;
        visibility: hidden;
        position: absolute;
    }

    .field__file-wrapper {
        width: 100%;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }

    .field__file-fake {
        height: 60px;
        width: calc(100% - 130px);
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        padding: 0 15px;
        border: 1px solid #c7c7c7;
        border-radius: 3px 0 0 3px;
        border-right: none;
    }

    .field__file-button {
        width: 130px;
        height: 60px;
        background: #1bbc9b;
        color: #fff;
        font-size: 1.125rem;
        font-weight: 700;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        border-radius: 0 3px 3px 0;
        cursor: pointer;
    }
</style>
<?php if(userAttach($userid, $_GET['trener'])):?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script src="<?= bloginfo('template_url') ?>/assets/js/core/countDown.js"></script>
<h1 class="text-primary"><?= FILES ?></h1>
    <h3><?= USER ?>: <?php  nameUser($userid,3)?></h3>
    <h3><?= DIR_NAME ?>: <?= $wpdb->get_var($wpdb->prepare('SELECT name FROM p_folder WHERE id = %d', $_GET['folder_id'])) ?></h3>

<div class="card">
<?php if(!$_GET['folder_id']): ?>  <br><?php alertStatus('warning', 'Нет данных!') ?> <?php else:?>
<?php $results = $wpdb->get_results($s=$wpdb->prepare("SELECT f.id, f.datecreate, f.user_id, f.folder, f.filename, f.filesize, l.name folder_name, COUNT(c.id) num_comm, f.portfolio
    FROM p_file f 
    LEFT OUTER JOIN p_folder l ON l.id = f.folder
    LEFT OUTER JOIN p_file_comments c ON c.file_id = f.id 
    WHERE f.user_id = %d AND f.folder = %d AND f.group_id = %d GROUP BY f.id", $userid, $_GET['folder_id'], $_GET['group_id']));?>
    <div class="card-body">
        <b><?= PROGRAMM_NAME ?>: </b><?= $grinf->$p_name ?><br>
        <b><?= GROUP ?>:</b> <?= $grinf->number_group ?><br>

        <table class="table no-margin table-hover" id="dataFile">
            <tbody>
                <tr>
                    <th><?= DIR_NAME ?></th>
                    <th><?= FILE_NAME ?></th>
                    <th><?= FILE_SIZE ?></th>
                    <th><?= DOWNLOADED_TIME ?></th>
                    <th></th>
                    <th></th>
                </tr>
                <?php foreach($results as $res):?>
                <tr>
                    <td><?= $res->folder_name ?></td>
                    <td>
                        <?php  if( $grinf->end_date > dateTime() ): ?>
                            <a href="/server_file/?download=<?= $res->id ?>&uid=<?= $userid ?>&trener=<?= $_GET['trener'] ?>">
                        <?php endif; ?>
                            <span class="badge style-primary-dark"><?= $res->filename ?></span>
                        </a>
                        <?php  if( $grinf->end_date > dateTime() ): ?>
                            <?php if( $grinf->program_id == 7 || $grinf->program_id == 15): ?>
                                <?php if($res->portfolio == 0): ?>
                                    <a href="/users/?z=add_file&folder_id=<?=$_GET['folder_id']?>&group_id=<?=$_GET['group_id']?>&portfolio=<?=$res->id?>" class="text-primary"><?= ADD_TO_PORTFOLIO ?></a>
                                <?php else: ?>
                                    <a href="/users/?z=add_file&folder_id=<?=$_GET['folder_id']?>&group_id=<?=$_GET['group_id']?>&notportfolio=<?=$res->id?>" class="text-primary"><?= REMOVE_FROM_PORTFOLIO ?></a>
                                <?php endif; ?>
                            <?php endif;?>
                        <?php endif; ?>

                    </td>
                    <td>
                        <?= formatSizeUnits($res->filesize) ?>
                    </td>
                    <td><?= $res->datecreate ?></td>
                    <td>
                        <!--<span class="badge"><a href="/users/?z=comment&id=<?= $res->id ?>"><i class="fa fa-comment"></i> Комментарии <?= $res->num_comm ?></a></span>-->
                    </td>
                    <td>
                        <?php  if( $grinf->end_date > dateTime() ): ?>
                            <?php if($accessUser):?>
                                <a href="#" class="btn btn-icon-toggle" onclick="deleteFile('<?= $res->id ?>');" data-del="<?= $res->id ?>" data-original-title="Удалить">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <input type="hidden" id="id_program" value="<?= $grinf->program_id ?>">
        <?php
        function p_extension_access($userid, $groupid){
            global $wpdb;
            $get_res = $wpdb->get_row($wpdb->prepare("SELECT * FROM `p_extension_access` WHERE user_id = %d AND group_id = %d", $userid, $groupid));
            if(!empty($get_res) && $get_res->extension_time > dateTime()){
                return true;
            }else{
                return false;
            }
        }

        ?>
        <?php
        if( (getCourse($_GET['group_id'], 'end_date') > dateTime() || p_extension_access(get_current_user_id(), $_GET['group_id']))
            || $_GET['folder_id'] == 4
            || $_GET['folder_id'] == 5
            || $_GET['folder_id'] == 6
        ): ?>
            <?php if($accessUser):?>
                <br><br>
                <div id="ajax-respond" class="ajax-respond"></div>

                <div class="field__wrapper">

                    <input name="file" type="file" name="file" id="uploadInput" class="field field__file" accept=".docx,.doc,.pptx,.ppt,.pdf">

                    <label class="field__file-wrapper" for="uploadInput">
                        <div class="field__file-fake"><?= FILE_NOT_SELECTED ?></div>
                        <div class="field__file-button"><?= FILE_SELECTED ?></div>
                    </label>

                </div>
                <button class="submit button btn ink-reaction btn-raised btn-primary" disabled="disabled" id="load"><?= FILE_UPLOAD ?></button>
        <br><br>
        <input type="hidden" id="extension" value="<?= $extension = $wpdb->get_var($wpdb->prepare("SELECT extension FROM p_folder WHERE id = %d", $_GET['folder_id'])) //Для проверки расширения ?>">
            <div class="alert alert-warning" role="alert">
                <?= ($extension == 'ppt') ? ATTENTION_POWEPOINT : ATTENTION_WORD ?>
                <p><?= MAX_UPLOADED_SIZE ?>: 10 МБ! </p>
            </div>
            <?php endif; ?>
            <h3 class="text-primary-dark"><?= TIME_LEFT ?>: <span id="display"></span></h3>
            <script>
                const display<?= $q ?> = document.querySelector('#display<?= $q ?>');
                countDown('<?= $grinf->end_date ?>', display<?= $q ?>);
            </script>
        <?php else:?>
        <?php alertStatus("warning", "<p class='lead'>".TIME_EXPIRED."</p>") ?>

        <?php endif;?>
    </div>
    
</div>
<input type="hidden" id="kz" value="<?= LANGID ?>">


<script src="<?= bloginfo('template_url') ?>/assets/js/core/actionsFile.js" ></script>
<?php endif; ?>

<?php else:?>
<br><?php alertStatus('warning', 'Нет доступа!') ?>
<?php endif; ?>

<script>
    let fields = document.querySelectorAll('.field__file');
    Array.prototype.forEach.call(fields, function (input) {
        let label = input.nextElementSibling,
            labelVal = label.querySelector('.field__file-fake').innerText;

        input.addEventListener('change', function (e) {
            let countFiles = '';
            if (this.files && this.files.length >= 1)
                countFiles = this.files.length;

            if (countFiles)
                label.querySelector('.field__file-fake').innerText = '<?= SELECTED_FILES ?>: ' + countFiles;
            else
                label.querySelector('.field__file-fake').innerText = labelVal;
        });
    });
</script>