<?php
global $wpdb;
$_POST = json_decode(file_get_contents("php://input"), true);
if (stripos(PHP_OS, 'WIN') === 0) {
    $server_path_to_folder = $_SERVER['DOCUMENT_ROOT'];
} else {
    $server_path_to_folder = '/var/www/html/';
}
?>
<?php if($_POST['action'] == 'add'): ?>
<?php printAll($_POST); ?>
<?php else: ?>
    <div class="boxcoding"></div>
    <?php include_once ($server_path_to_folder . '/wp-content/themes/portalcpi/modules/members/add.php');?>
    <input type="text" class="form-control" name="test">
    <div class="card-actionbar">
        <div class="card-actionbar-row">
            <button type="submit" class="btn btn-success">Сақтау/Сохранить</button>
            <input type="hidden" name="link" class="form-control" data-user value="portal_server/">
            <input type="hidden" name="group" class="form-control" data-user value="<?=$_GET['list_file_group_id']?>">
            <input type="hidden" name="action" class="form-control" data-user value="add">
            <input type="hidden" name="statement" class="form-control" data-user value="1">
        </div>
    </div><!--end .card-actionbar -->
<?php endif; ?>
