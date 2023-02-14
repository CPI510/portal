<?php
global $wpdb;




$fileArr = $wpdb->get_row($wpdb->prepare("SELECT f.id, f.file_name, u.email FROM p_proforma_attached_file f LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id WHERE f.id = %d", $_GET['id']));


if($_GET['action'] == 'delete'){
    $commArr = $wpdb->get_row($wpdb->prepare("SELECT id, file_name, user_id, file_dir, year FROM p_proforma_attached_file WHERE id = %d", $_GET['id']));
    if($commArr->user_id != get_current_user_id()){ echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1af&group='.$_GET['group'].'" />'; exit(); }

    if(unlink('/var/www/uploads/' . $commArr->year . '/' . $commArr->user_id . '/proforma/' . $commArr->file_dir)) {
        $wpdb->delete( 'p_proforma_attached_file', array( 'id' => $_GET['id'] ), array( '%d' ) );
    }

    echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1af&group='.$_GET['group'].'" />';
}

if($_FILES && $_GET['group']){

//    echo "<pre>";
//    print_r($_FILES);
//    echo "</pre>";
//    exit();

    $year_folder = '/var/www/uploads/' . date('Y') . '/';
    if(!is_dir($year_folder))
        mkdir( $year_folder, 0777 );

    $uploaddircom = '/var/www/uploads/' . date('Y') . '/' . get_current_user_id() . '/';
    if(!is_dir($uploaddircom))
        mkdir( $uploaddircom, 0777 );

    $uploaddir = '/var/www/uploads/' . date('Y') . '/' . get_current_user_id() . '/proforma/';


    //printAll($_FILES); exit;

    if($_GET['action'] == 'add'){
        $filename = time() . '.loc';


//        $upload_dir = wp_upload_dir();
//        $uploaddir = $upload_dir['basedir'] . '/users_file/' . get_current_user_id() . '/comments/';

        if(!is_dir($uploaddir))
            mkdir( $uploaddir, 0777 );

        if($wpdb->insert(
            'p_proforma_attached_file',
            array( 'user_id' => get_current_user_id(), 'file_name' => $_FILES['file']['name'], 'group_id' => $_GET['group'],
                'file_size' => $_FILES['file']['size'], 'year' => date('Y'), 'file_dir' => $filename),
            array( '%d', '%s', '%d', '%d', '%d', '%s' )
        )){
            if( move_uploaded_file( $_FILES['file']['tmp_name'], $uploaddir . basename($filename) ) ){


            }

        }else{
            $error = "Вы уже загрузили файл!";
        }



    }


    echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1af&group='.$_GET['group'].'&e='.$error.'" />'; exit();


}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary"></h1>

    </div>

    <div class="col-md-8">
        <article class="margin-bottom-xxl">
            <?php if($_GET['e']):?>
                <div class="alert alert-warning" role="alert">
                    <strong>Warning!</strong> <?= $_GET['e'] ?>
                </div>
            <?php endif; ?>

        </article>
    </div><!--end .col -->

    <?php if($_GET['action'] == 'edit' || $_GET['action'] == 'add'): ?>
        <?php if($_GET['action'] == 'edit') {
            $commArr = $wpdb->get_row($wpdb->prepare("SELECT id, file_id, user_id, comments FROM p_proforma_attached_file WHERE id = %d", $_GET['commid']));
            if($commArr->user_id != get_current_user_id()) echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1af&group=' . $_GET['group'] . '" />';
        }
        ?>
        <div class="col-lg-12">
            <form enctype="multipart/form-data" class="form-horizontal" method="POST" action="/proforma/?form=1af&group=<?= $_GET['group'] ?><?= ($_GET['action'] == 'edit') ? "&action=edit&commid=" . $commArr->id : "&action=add" ?>">
                <div class="card">
                    <div class="card-head style-primary">
                        <header>Прикрепите скан</header>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="Username5" class="col-sm-2 control-label">Файл</label>
                            <div class="col-sm-10">
                                <input type="file" name="file" class="form-control" accept=".docx,.doc,.pptx,.ppt,.pdf">
                            </div>
                        </div>
                    </div><!--end .card-body -->
                    <div class="card-actionbar">
                        <div class="card-actionbar-row">
                            <button type="submit" class="btn btn-flat btn-primary ink-reaction">Сохранить</button>
                        </div>
                    </div>
                </div><!--end .card -->
            </form>
        </div>
    <?php else: ?>

        <div class="col-lg-12">
            <a href="/proforma/?form=1af&group=<?= $_GET['group'] ?>&action=add" class="btn btn-success">Добавить файл</a>
        </div>
        <div class="col-md-8">
            <article class="margin-bottom-xxl">

            </article>
        </div><!--end .col -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Файл</th>
                            <th>Автор</th>
                            <th>Дата создания</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $commentsArr = $wpdb->get_results($wpdb->prepare("SELECT f.id, f.file_name,  f.datetime, f.file_size, u.surname, u.name, u.patronymic, f.user_id
                            FROM p_proforma_attached_file f
                            LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id WHERE f.group_id = %d", $_GET['group']));?>
                        <?php foreach($commentsArr as $comm): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><a href="/server_file/?proformadownload=<?= $comm->id ?>" class="text-primary"><?= $comm->file_name ?></a></td>
                                <td><?= $comm->surname ?> <?= $comm->name ?> <?= $comm->patronymic ?></td>
                                <td><?= $comm->datetime ?></td>
                                <td>
                                    <?php if (get_current_user_id() == $comm->user_id):?>
                                        <a href="/proforma/?form=1af&group=<?= $_GET['group'] ?>&id=<?= $comm->id ?>&action=delete" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>


</div>