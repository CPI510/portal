<?php
global $wpdb;
//$_POST = json_decode(file_get_contents("php://input"), true);
//printAll($_POST);
//printAll($_GET);
//printAll($_FILES);

//$fileArr = $wpdb->get_row($wpdb->prepare("SELECT f.id, f.file_name, u.email FROM p_assessment_attached_file f LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id WHERE f.id = %d", $_GET['id']));
if($_POST['category_id'] == 3){

}elseif ($_POST['category_id'] != 2){
    $_POST['category_id'] = 1;
    $_POST['rubric_user_id'] = 0;
}

if($_GET['action'] == 'delete'){
    $commArr = $wpdb->get_row($wpdb->prepare("SELECT id, file_name, user_id, file_dir, year, rubric_user_id FROM p_assessment_attached_file WHERE id = %d", $_GET['id']));
    if($commArr->user_id != get_current_user_id()){ echo '<meta http-equiv="refresh" content="0;url=/proforma/?form=1&group='.$_GET['group'].'" />'; exit(); }

    if($wpdb->update('p_assessment_attached_file',['deleted' => 1, 'user_id' => $commArr->user_id.'_deleted'.time().'_'.get_current_user_id()],['id' => $commArr->id],['%s'],['%d'])) {
        //$wpdb->delete( 'p_assessment_attached_file', array( 'id' => $_GET['id'] ), array( '%d' ) );
    }

    if($_GET['category_id'] == 2){
        echo '<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_GET['group'].'&download_rubric=1&category_id=2&rubric_user_id='.$commArr->rubric_user_id.'" />';
    }if($_GET['category_id'] == 3){
        echo '<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_GET['group'].'&download_rubric=1&category_id=3&rubric_user_id='.$commArr->rubric_user_id.'" />';
    }else{
        echo '<meta http-equiv="refresh" content="0;url=/assessment/?z=sheet&group='.$_GET['group'].'" />';
    }
}

if($_FILES && $_POST['group']){
//printAll($_POST);
//printAll($_GET);
//printAll($_FILES);
//    exit();
    $year_folder = '/var/www/uploads/' . date('Y') . '/';
    if(!is_dir($year_folder))
        mkdir( $year_folder, 0777 );

    $uploaddircom = '/var/www/uploads/' . date('Y') . '/' . get_current_user_id() . '/';
    if(!is_dir($uploaddircom))
        mkdir( $uploaddircom, 0777 );

    $uploaddir = '/var/www/uploads/' . date('Y') . '/' . get_current_user_id() . '/assessment/';

    //printAll($_FILES); exit;

    if($_POST['action'] == 'add'){
        $filename = time() . '.loc';


//        $upload_dir = wp_upload_dir();
//        $uploaddir = $upload_dir['basedir'] . '/users_file/' . get_current_user_id() . '/comments/';

        if(!is_dir($uploaddir))
            mkdir( $uploaddir, 0777 );

        if($wpdb->insert(
            'p_assessment_attached_file',
            array(
                'user_id' => get_current_user_id(),
                'file_name' => $_FILES['file']['name'],
                'group_id' => $_POST['group'],
                'file_size' => $_FILES['file']['size'],
                'year' => date('Y'),
                'file_dir' => $filename,
                'category_id' => $_POST['category_id'],
                'rubric_user_id' => $_POST['rubric_user_id']),
            array( '%d', '%s', '%d', '%d', '%d', '%s', '%d', '%d' )
        )){
            if( move_uploaded_file( $_FILES['file']['tmp_name'], $uploaddir . basename($filename) ) ){

            }
        }else{
            $error = "Вы уже загрузили файл!";
        }
    }
    if($_POST['category_id'] == 2){
        echo '<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['group'].'&download_rubric=1&rubric_user_id='.$_POST['rubric_user_id'].'&category_id=2&e='.$error.'" />'; exit();
    }elseif($_POST['category_id'] == 3){
        echo '<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['group'].'&download_rubric=1&rubric_user_id='.$_POST['rubric_user_id'].'&category_id=3&e='.$error.'" />'; exit();
    }else{
        echo '<meta http-equiv="refresh" content="0;url=/assessment/?z=sheet&group='.$_POST['group'].'&e='.$error.'" />'; exit();
    }
}


