<?php
/* 
Template Name: server_file
Template Post Type: post, page, product 
*/

global $wpdb;

if(!is_user_logged_in()) {

	auth_redirect();

};

if( $_GET['download'] && userAttach($_GET['uid'])){

    $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_file WHERE id = %d", $_GET['download']));
    if(userAttach($res->user_id) && $res){
        $file = $res->filedir;
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($res->filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);

        exit;
        
    }else{
        exit('Доступ закрыт!');
    }

}elseif($_GET['id']) {

    $res = $wpdb->get_row($wpdb->prepare("SELECT f.id, f.datecreate, f.user_id, f.folder, f.filename, f.filesize, l.name folder_name  
    FROM p_file f 
    INNER JOIN p_folder l ON l.id = f.folder
    WHERE f.id = %d", $_GET['id']));

    echo json_encode( array('files' => $res ) );

}elseif($_POST['folder']){ 

    $data = array();
    if ($_FILES){
    
        $error = false;
        $files = array();
        $filename = time();
        
        $upload_dir = wp_upload_dir();

        $uploaddir = $upload_dir['basedir'] . '/users_file/' . get_current_user_id() . '/';
        if(!is_dir($uploaddir))
            mkdir( $uploaddir, 0777 );
    
        // переместим файлы из временной директории в указанную
        foreach( $_FILES as $file ){
            if( move_uploaded_file( $file['tmp_name'], $uploaddir . basename($filename) ) ){    

                $filecreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_file ( `user_id`,`folder`, `filename`, `filedir`, `filesize` ) 
				VALUES (%d, %s, %s, %s, %s)"
				,get_current_user_id()
				,$_POST['folder']
				,$file['name'] 
                ,$uploaddir . basename($filename)
                ,$file['size']
                ));
                $wpdb->insert_id;
            
                $files[] = realpath( $uploaddir . $filename );
            } else {
                $error = true;
            }
        }
        $data = $error ? array('error' => 'Ошибка загрузки файлов.') : array('files' => $files , 'idf' => $wpdb->insert_id);
        echo json_encode( $data );
    }
}elseif($_GET['del']){
    $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_file WHERE id = %d", $_GET['del']));
    if($res) {
        unlink($res->filedir);
        $id = $res->id;
        $del = $wpdb->query($wpdb->prepare("DELETE FROM p_file WHERE id = %d AND user_id = %d", $_GET['del'], get_current_user_id()));
        echo json_encode(  $id  );
    }
}


// Собираем архив пример
// $zip = new ZipArchive();
// $filenamezip = $uploaddir ."test112.zip";

// if ($zip->open($filenamezip, ZipArchive::CREATE)!==TRUE) {
//     exit("Невозможно открыть <$filenamezip>\n");
// }
// $zip->addFile($uploaddir . basename($filename), $file['name'] );         
// $zip->close();  