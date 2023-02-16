<?php
global $wpdb;

$access = getAccess(get_current_user_id())->access;
?>

    <h1 class="text-primary">Файлы</h1> <h3>Пользователя: <?php  nameUser($_GET['user_id'],3)?></h3>

    <div class="card">

        <div class="card-body">
            <?php $results = $wpdb->get_results($s=$wpdb->prepare("SELECT a.id, a.datecreate, a.filename, a.filedir, a.filesize, b.name folder_name, c.start_date, c.end_date
                        FROM p_file a
                        LEFT OUTER JOIN p_folder b ON b.id = a.folder
                        LEFT OUTER JOIN p_groups c ON c.id = a.group_id
                        WHERE a.group_id = %d AND a.user_id = %d",$_GET['group_id'], $_GET['user_id'] ));
            ?>
            <table class="table no-margin table-hover" id="dataFile">
                <tbody>
                <tr>
                    <th>Папка</th>
                    <th>Название файла</th>
                    <th>Размер файла</th>
                    <th>Дата загрузки</th>
                    <th></th>
                </tr>
                <?php if( ( getCourse($_GET['group_id'], 'end_date') > dateTime() && $access == 4 )
                    || ( getCourse($_GET['group_id'], 'expert_id') == get_current_user_id() && getCourse($_GET['group_id'], 'expert_date') > dateTime() )
                    || ( getCourse($_GET['group_id'], 'moderator_id') == get_current_user_id() && getCourse($_GET['group_id'], 'moderator_date') > dateTime() )
                    || $access == 1 ): ?>
                    <?php foreach($results as $res):?>
                        <tr>
                            <td><?= $res->folder_name ?></td>
                            <td><a href="/server_file/?download=<?= $res->id ?>&uid=<?= $_GET['user_id'] ?>" class="text-primary"><?= $res->filename ?></a></td>
                            <td><?= formatSizeUnits($res->filesize) ?></td>
                            <td><?= $res->datecreate ?></td>
                            </td>
                        </tr>
                    <?php endforeach;?>
                <?php else: ?>
                    <?php alertStatus("warning", "<p class='lead'>Истекло время, предоставленное для загрузки файлов</p>") ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <br><?php //alertStatus('warning', 'Нет доступа!') ?>





