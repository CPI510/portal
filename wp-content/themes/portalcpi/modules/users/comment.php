<?php 
global $wpdb;


$fileArr = $wpdb->get_row($wpdb->prepare("SELECT f.id, f.filename, u.email FROM p_file f LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id WHERE f.id = %d", $_GET['id']));


if($_GET['action'] == 'delete'){
    $commArr = $wpdb->get_row($wpdb->prepare("SELECT id, file_id, user_id, file_dir, year, comments FROM p_file_comments WHERE id = %d", $_GET['commid']));
    if($commArr->user_id != get_current_user_id()){ echo '<meta http-equiv="refresh" content="0;url=/users/?z=comment&id='.$_GET['id'].'&action=list" />'; exit(); }

    if(unlink('/var/www/uploads/' . $commArr->year . '/' . $commArr->user_id . '/comments/' . $commArr->file_dir)) {
        $wpdb->delete( 'p_file_comments', array( 'id' => $_GET['commid'] ), array( '%d' ) );
    }

    echo '<meta http-equiv="refresh" content="0;url=/users/?z=comment&id='.$_GET['id'].'&action=list" />'; 
}

if($_POST['comment'] && $_GET['id']){

    $year_folder = '/var/www/uploads/' . date('Y') . '/';
    if(!is_dir($year_folder))
        mkdir( $year_folder, 0777 );

    $uploaddircom = '/var/www/uploads/' . date('Y') . '/' . get_current_user_id() . '/';
    if(!is_dir($uploaddircom))
        mkdir( $uploaddircom, 0777 );

    $uploaddir = '/var/www/uploads/' . date('Y') . '/' . get_current_user_id() . '/comments/';


    //printAll($_FILES); exit;

    if($_GET['action'] == 'add'){
        $filename = time() . '.loc';


//        $upload_dir = wp_upload_dir();
//        $uploaddir = $upload_dir['basedir'] . '/users_file/' . get_current_user_id() . '/comments/';

        if(!is_dir($uploaddir))
            mkdir( $uploaddir, 0777 );

        if( move_uploaded_file( $_FILES['file']['tmp_name'], $uploaddir . basename($filename) ) ){    

    
        } else {
            $error = true;
        }

        $wpdb->insert(
            'p_file_comments',
            array( 'file_id' => $_GET['id'], 'user_id' => get_current_user_id(), 'comments' => $_POST['comment'], 'file_name' => $_FILES['file']['name'],
                'file_dir' => basename($filename), 'file_size' => $_FILES['file']['size'], 'year' => date('Y')),
            array( '%d', '%d', '%s', '%s', '%s', '%d', '%d' )
        ); 
    }

    if ($_GET['action'] == 'edit'){

        $filename = time() . '.loc';

        if(!is_dir($uploaddir))
            mkdir( $uploaddir, 0777 );

        if( move_uploaded_file( $_FILES['file']['tmp_name'], $uploaddir . basename($filename) ) ){    

    
        } else {
            $error = true;
        }

        $commArr = $wpdb->get_row($wpdb->prepare("SELECT id, file_id, user_id, file_dir, year, comments FROM p_file_comments WHERE id = %d", $_GET['commid']));
        if($commArr->user_id != get_current_user_id()){ echo '<meta http-equiv="refresh" content="0;url=/users/?z=comment&id='.$_GET['id'].'&action=list" />'; exit(); }
        if($_FILES['file']['tmp_name']) {

            if(unlink('/var/www/uploads/' . $commArr->year  . '/' . $commArr->user_id . '/comments/' . $commArr->file_dir)) {
                $wpdb->update(
                    'p_file_comments',
                    array( 'comments' => $_POST['comment'], 'time_update' => current_time('mysql', 1) , 'file_name' => $_FILES['file']['name'],
                        'file_dir' => basename($filename), 'file_size' => $_FILES['file']['size'], 'year' => date('Y')),
                    array( 'id' => $_GET['commid'] ),
                    array( '%s','%s', '%s', '%s', '%d' ),
                    array( '%d' )
                );
            }

        }else{
            $wpdb->update(
                'p_file_comments',
                array( 'comments' => $_POST['comment'], 'time_update' => current_time('mysql', 1) ),
                array( 'id' => $_GET['commid'] ),
                array( '%s' ),
                array( '%d' )
            );
        }
        
    }
    

    $attachments = '';//array(WP_CONTENT_DIR . '/uploads/attach.zip');
    $headers = array(
        'From: portal@cpi.nis.edu.kz <portal@cpi.nis.edu.kz>',
        'content-type: text/html',
    );

    $userMail = nameUser( get_current_user_id(),6 );
    $user_cn = nameUser( get_current_user_id(),5 );
    $contentMail = "<b>Комментарий к файлу:</b> $fileArr->filename<br>
        <b>Пользователь:</b> $user_cn / $userMail<br>
        <b>Комментарий:</b> $_POST[comment]  <br><br>
        <b>Файл:</b> " . $_FILES['file']['name'] . "  <br><br>
    
   Открыть страницу: <a href='" . site_url() . "/users/?" . $_SESSION['add_file'] . "'>" . site_url() . "/users/?" . $_SESSION['add_file'] .  "</a> <br><br>";

    wp_mail($fileArr->email, 'Комментарий к файлу', $contentMail, $headers);
    echo '<meta http-equiv="refresh" content="0;url=/users/?z=comment&id='.$_GET['id'].'&action=list" />'; exit();


} 
?>




<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Комментарии</h1>
        
    </div>
    
    <div class="col-md-8">
        <article class="margin-bottom-xxl">
        <h3> к файлу "<?= $fileArr->filename ?>"</h3>
        </article>
    </div><!--end .col -->

    <?php if($_GET['action'] == 'edit' || $_GET['action'] == 'add'): ?>
    <?php if($_GET['action'] == 'edit') {
        $commArr = $wpdb->get_row($wpdb->prepare("SELECT id, file_id, user_id, comments FROM p_file_comments WHERE id = %d", $_GET['commid']));
        if($commArr->user_id != get_current_user_id()) echo '<meta http-equiv="refresh" content="0;url=/users/?z=comment&id=' . $_GET['id'] . '&action=list" />';
    }
      ?>
        <div class="col-lg-12">
            <form enctype="multipart/form-data" class="form-horizontal" method="POST" action="/users/?z=comment&id=<?= $_GET['id'] ?><?= ($_GET['action'] == 'edit') ? "&action=edit&commid=" . $commArr->id : "&action=add" ?>">
                <div class="card">
                    <div class="card-head style-primary">
                        <header>Введите ваш комментарий</header>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="Username5" class="col-sm-2 control-label">Комментарий</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="comment"><?= ($_GET['action'] == 'edit') ? $commArr->comments : "" ?></textarea><div class="form-control-line"></div>
                            </div>
                        </div> 
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
            <a href="/users/?z=comment&id=<?= $_GET['id'] ?>&action=add" class="btn btn-success">Добавить комментарий</a>
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
                                <th>Комментарий</th>
                                <th>Файл</th>
                                <th>Автор</th>
                                <th>Дата создания</th>
                                <th>Дата редактирования</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $commentsArr = $wpdb->get_results($wpdb->prepare("SELECT f.file_id, f.file_name, f.id, f.comments, f.time, f.time_update, u.surname, u.name, u.patronymic, f.user_id
                            FROM p_file_comments f
                            LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id WHERE f.file_id = %d", $_GET['id']));?>
                            <?php foreach($commentsArr as $comm): ?>
                                <tr>
                                    <td><?= ++$i ?></td>
                                    <td><?= $comm->comments ?></td>
                                    <td><a href="/server_file/?comdownload=<?= $comm->id ?>" class="text-primary"><?= $comm->file_name ?></a></td>
                                    <td><?= $comm->surname ?> <?= $comm->name ?> <?= $comm->patronymic ?></td>
                                    <td><?= $comm->time ?></td>
                                    <td><?= $comm->time_update ?></td>
                                    <td>
                                    <?php if (get_current_user_id() == $comm->user_id):?>
                                        <a href="/users/?z=comment&id=<?= $comm->file_id ?>&commid=<?= $comm->id ?>&action=edit" class="btn btn-icon-toggle" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
                                        <a href="/users/?z=comment&id=<?= $comm->file_id ?>&commid=<?= $comm->id ?>&action=delete" class="btn btn-icon-toggle" onclick="return confirm('Вы действительно хотите удалить?');" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
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