<?php
/* 
Template Name: server_file
Template Post Type: post, page, product 
*/

global $wpdb;

if (stripos(PHP_OS, 'WIN') === 0) {
    $server_path_to_folder = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
} else {
    $server_path_to_folder = '/var/www/uploads/';
}

if(!is_user_logged_in()) {

	auth_redirect();

};

if($_GET['proformadownload']){

    $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_proforma_attached_file WHERE id = %d", $_GET['proformadownload']));
    if($res){

        $uid = $res->user_id;
        $file = $server_path_to_folder . $res->year . '/' . $res->user_id . '/proforma/'. $res->file_dir;
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='. str_ireplace(",","", $res->file_name));// . basename($res->file_name));
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

}elseif($_GET['comdownload']){

    $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_file_comments WHERE id = %d", $_GET['comdownload']));
    if($res){

        $uid = $res->user_id;
        $file = $server_path_to_folder . $res->year . '/' . $res->user_id . '/comments/'. $res->file_dir;
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='. str_ireplace(",","", $res->file_name));// . basename($res->file_name));
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

}elseif( $_GET['download'] ){

    $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_file WHERE id = %d", $_GET['download']));
    $groupInfo  = groupInfo($res->group_id);

    $programArr = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_programs WHERE id = %d", $groupInfo->program_id));
//printAll($groupInfo);
//($groupInfo->end_date > dateTime() && getAccess(get_current_user_id())->access == 4)
    if( ( $groupInfo->trener_id == get_current_user_id() && $groupInfo->trener_date > dateTime() )
        || ( $groupInfo->expert_id == get_current_user_id() && $groupInfo->expert_date > dateTime() )
        || ( $groupInfo->moderator_id == get_current_user_id() && $groupInfo->moderator_date > dateTime() )
        || ( $groupInfo->teamleader_id == get_current_user_id() && $groupInfo->teamleader_date > dateTime() )
        || ( $groupInfo->independent_trainer_id == get_current_user_id() && $groupInfo->independent_trainer_date > dateTime() && $groupInfo->program_id == 6)
        || getAccess(get_current_user_id())->access == 1
        || $res->user_id == get_current_user_id() ){

        if($res->user_id == get_current_user_id()
            || $groupInfo->expert_id == get_current_user_id()
            || $groupInfo->moderator_id == get_current_user_id()
            || $groupInfo->teamleader_id == get_current_user_id()
            || ( $groupInfo->independent_trainer_id == get_current_user_id() && $groupInfo->independent_trainer_date > dateTime() && $groupInfo->program_id == 6)
            || ( $groupInfo->trener_id == get_current_user_id() && $groupInfo->trener_date > dateTime() )
            || getAccess(get_current_user_id())->access == 1
        ){

            $file = $server_path_to_folder . $res->year . '/' . $res->user_id . '/'. $res->filedir;
            if (ob_get_level()) {
                ob_end_clean();
            }

            if($programArr->file_name_change == 1){
                $extension =  pathinfo($res->filename, PATHINFO_EXTENSION);
                $file_name = $programArr->file_name_change_text ."_". userInfo($res->user_id)->surname . "_" . userInfo($res->user_id)->name . "_" . userInfo($res->user_id)->patronymic.".".$extension;
            }else{
                $file_name = $res->filename;
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='. str_ireplace(",","", $file_name)); //. basename($res->filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);

            exit;

        }else{
            exit('Доступ закрыт!1');
        }
    }else{
        exit('Доступ закрыт!2');
    }


} elseif (isset($_GET['id'])) {

    $res = $wpdb->get_row($wpdb->prepare("SELECT f.id, f.datecreate, f.user_id, f.folder, f.filename, f.filesize, l.name folder_name  
    FROM p_file f 
    INNER JOIN p_folder l ON l.id = f.folder
    WHERE f.id = %d", $_GET['id']));

    echo json_encode( array('files' => $res ) );

} elseif (isset($_POST['folder'])){

    $grinf = groupInfo($_POST['group_id']);
    $check_file = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM p_file WHERE user_id = %d AND group_id = %d AND folder = %d",
            get_current_user_id(),
            $_POST['group_id'],
            $_POST['folder']
        )
    );
    if( $grinf->single_file == 1 && $check_file->folder == $_POST['folder']){
        $folder = $server_path_to_folder . $check_file->year . '/' . get_current_user_id() . '/';
        unlink($folder  . $check_file->filedir);
        $del = $wpdb->query($wpdb->prepare("DELETE FROM p_file WHERE id = %d AND user_id = %d", $check_file->id, get_current_user_id()));

        $data = array();
        if ($_FILES){

            $error = false;
            $files = array();
            $filename = time() . '.loc';

            $year_folder = $server_path_to_folder . date('Y') . '/';
            if(!is_dir($year_folder))
                mkdir( $year_folder, 0777 );

            $uploaddir = $server_path_to_folder . date('Y') . '/' . get_current_user_id() . '/';
            if(!is_dir($uploaddir))
                mkdir( $uploaddir, 0777 );

            // переместим файлы из временной директории в указанную
            foreach( $_FILES as $file ){
                if( move_uploaded_file( $file['tmp_name'], $uploaddir . basename($filename) ) ){

                    $filecreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_file ( `user_id`,`folder`, `filename`, `filedir`, `filesize`, `group_id`, `year` ) 
				VALUES (%d, %s, %s, %s, %s, %s, %d)"
                        ,get_current_user_id()
                        ,$_POST['folder']
                        ,$file['name']
                        ,basename($filename)
                        ,$file['size']
                        ,$_POST['group_id']
                        ,date('Y')
                    ));
                    $wpdb->insert_id;

                    $files[] = realpath( $uploaddir . $filename );
                    if(!$filecreate)$error = true;
                } else {
                    $error = true;
                }
            }
        }

        $data =  array('refresh' => '1');
        echo json_encode( $data );
    }else{
        $data = array();
        if ($_FILES){

            $error = false;
            $files = array();
            $filename = time() . '.loc';

            $year_folder = $server_path_to_folder . date('Y') . '/';
            if(!is_dir($year_folder))
                mkdir( $year_folder, 0777 );

            $uploaddir = $server_path_to_folder . date('Y') . '/' . get_current_user_id() . '/';
            if(!is_dir($uploaddir))
                mkdir( $uploaddir, 0777 );

            // переместим файлы из временной директории в указанную
            foreach( $_FILES as $file ){
                if( move_uploaded_file( $file['tmp_name'], $uploaddir . basename($filename) ) ){

                    $filecreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_file ( `user_id`,`folder`, `filename`, `filedir`, `filesize`, `group_id`, `year` ) 
				VALUES (%d, %s, %s, %s, %s, %s, %d)"
                        ,get_current_user_id()
                        ,$_POST['folder']
                        ,$file['name']
                        ,basename($filename)
                        ,$file['size']
                        ,$_POST['group_id']
                        ,date('Y')
                    ));
                    $wpdb->insert_id;

                    $files[] = realpath( $uploaddir . $filename );
                    if(!$filecreate)$error = true;
                } else {
                    $error = true;
                }
            }
            $data = $error ? array('error' => 'Ошибка загрузки файлов.') : array('files' => $files , 'idf' => $wpdb->insert_id);
            echo json_encode( $data );

        }
    }

}elseif($_GET['del']){
    $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_file WHERE id = %d", $_GET['del']));
    if($res) {
        $folder = $server_path_to_folder . $res->year . '/' . get_current_user_id() . '/';
        unlink($folder  . $res->filedir);
        $id = $res->id;
        $del = $wpdb->query($wpdb->prepare("DELETE FROM p_file WHERE id = %d AND user_id = %d", $_GET['del'], get_current_user_id()));
        echo json_encode(  $id  );
    }
}elseif(isset($_GET['groupid']) && $_GET['zip'] == 1 && (getAccess(get_current_user_id())->access == 1 || getAccess(get_current_user_id())->access == 7 ) ){

    $res = $wpdb->get_results($s=$wpdb->prepare("SELECT f.id, f.datecreate, f.user_id, f.folder, f.filename, f.filesize, l.name folder_name, f.filedir , u.surname, u.name u_name, u.patronymic, f.year, f.group_id
    FROM p_file f 
    LEFT OUTER JOIN p_folder l ON l.id = f.folder
    LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id
    WHERE f.group_id = %d
    ORDER BY f.datecreate
    ", $_GET['groupid']));

echo $s; exit();
    $uploaddir = $server_path_to_folder;


    //Собираем архив пример
    $filename = time() . '.zip';
    $zip = new ZipArchive();
    //$upload_dir = wp_upload_dir();
    //$uploaddir = $upload_dir['basedir'] . '/users_file/' . get_current_user_id() . '/';
    //$uploaddir = $server_path_to_folder . date('Y') . '/' . get_current_user_id() . '/';
    $filenamezip = $uploaddir . $filename ;


    if ($zip->open($filenamezip, ZipArchive::CREATE)!==TRUE) {
        exit("Невозможно открыть <$filenamezip>\n");
    }

    foreach($res as $file){
        //echo "{$file->filedir}, {$file->filename} <br>";
        $zip->addFile( $server_path_to_folder . $file->year . '/' . $file->user_id . '/' . $file->filedir, "{$file->surname}_{$file->u_name}_{$file->patronymic}_{$file->filename}" );
        //$zip->addFile($uploaddir . basename($filename), $file['name'] ); 
    }
      
    $zip->close();  

    if(file_exists($filenamezip)){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='. $filename); //. basename($res->filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filenamezip));
        readfile($filenamezip);

        unlink($filenamezip);
    }else{
        echo "!";
    }


}elseif($_GET['zip'] == 2 && $_GET['groupid'] && (getAccess(get_current_user_id())->access == 1 || getAccess(get_current_user_id())->access == 7 || ( getAccess(get_current_user_id())->access == 4 && groupInfo($_GET['groupid'])->program_id == 18))){
    $groupInfo  = groupInfo($_GET['groupid']);
    //printAll($groupInfo); exit();
    if($_GET['portfolio'] == 1){
        $sql_portfolio = " AND f.portfolio = 1";
        $file_portfolio_name = "Портфолио_";
    }else{
        $sql_portfolio = "";
        $file_portfolio_name = "";
    }

    $res = $wpdb->get_results($s=$wpdb->prepare("
SELECT f.id, u.user_id, f.datecreate, f.folder, f.filename, f.filesize, l.name folder_name, f.filedir , u.surname, u.name u_name, u.patronymic, f.group_id, f.year, f.portfolio
    FROM p_file f 
    LEFT OUTER JOIN p_folder l ON l.id = f.folder
    LEFT OUTER JOIN p_user_fields u ON u.user_id = f.user_id
    WHERE f.group_id = %d $sql_portfolio
    ORDER BY f.datecreate DESC
    ",$_GET['groupid']));

    if(empty($res)) {
        alertStatus('warning', 'Нет файлов!');
        echo'<meta http-equiv="refresh" content="0;url=/groups/?z=list" />'; exit();
    } else {
        //Собираем архив
        $filename = groupInfo($_GET['groupid'])->number_group . "_" . time() . "_" . $file_portfolio_name . '.zip';
        $zip = new ZipArchive();
//        $upload_dir = wp_upload_dir();
//        $uploaddir = $upload_dir['basedir'] . '/users_file/' . get_current_user_id() . '/';
//        $uploaddir = $server_path_to_folder . date('Y') . '/' . get_current_user_id() . '/';
//        if(!is_dir($uploaddir))
//            mkdir( $uploaddir, 0777 );
        $year_folder = $server_path_to_folder . date('Y') . '/';
        if(!is_dir($year_folder))
            mkdir( $year_folder, 0777 );

        $uploaddir = $server_path_to_folder . date('Y') . '/' . get_current_user_id() . '/';
        if(!is_dir($uploaddir))
            mkdir( $uploaddir, 0777 );
        $filenamezip = $uploaddir . $filename;


        if ($zip->open($filenamezip, ZipArchive::CREATE)!==TRUE) {
            exit("Невозможно открыть <$filenamezip>\n");
        }
//echo $s; exit();
//echo $server_path_to_folder;
//printAll($res); exit();
        $i = 0;
        foreach($res as $file){
            $i++;
            if($file->portfolio == 1){
                $portfolio_text = "Портфолио_";
            }else{
                $portfolio_text = "";
            }

            if($groupInfo->program_id == 14){
                $other_name = $groupInfo->name_org;
            }elseif($groupInfo->program_id == 6 || $groupInfo->program_id == 16 || $groupInfo->program_id == 7){
                $other_name = str_replace('/', '_', $file->folder_name);
            }else{
                $other_name = "";
            }

            $fio = trim($file->surname) . "_" . trim($file->u_name) . "_" . trim($file->patronymic);

            if(${'user_file_' . $file->user_id } != $file->filename){
                //echo "{$file->filedir}, {$file->filename} <br>";
                $info = new SplFileInfo($file->filename);
//                echo $server_path_to_folder . $file->year . '/' . $file->user_id . '/' . $file->filedir ." ----- ". "{$i}_{$portfolio_text}{$fio}_{$other_name}.{$info->getExtension()}";
//                exit();
                if(!$zip->addFile( $server_path_to_folder . $file->year . '/' . $file->user_id . '/' . $file->filedir, "{$i}_{$portfolio_text}{$fio}_{$other_name}.{$info->getExtension()}" )){
//                    printAll($zip);
                    exit('error');
                }
                //$zip->addFile($uploaddir . basename($filename), $file['name'] );
            }
            ${'user_file_' . $file->user_id } = $file->filename;

        }

        $zip->close();

        if(file_exists($filenamezip)){
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='. $filename); //. basename($res->filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filenamezip));
            readfile($filenamezip);

            unlink($filenamezip);
        }else{
//            printAll($res);
            printAll($res);
            echo "!";
        }
    }




}elseif($_GET['assessment_sheet'] == 1){
    $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_assessment_attached_file WHERE id = %d", $_GET['assessment_sheet_file']));
    if($res) {

        $uid = $res->user_id;
        $file = $server_path_to_folder . $res->year . '/' . $res->user_id . '/assessment/' . $res->file_dir;
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . str_ireplace(",", "", $res->file_name));// . basename($res->file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }

        exit;
}else{
    wp_redirect( site_url() );
}


function uploadFile($folder, $group_id, $user_id){
    $error = false;
    $files = array();
    $filename = time() . '.loc';

    $year_folder = $server_path_to_folder . date('Y') . '/';
    if(!is_dir($year_folder))
        mkdir( $year_folder, 0777 );

    $uploaddir = $server_path_to_folder . date('Y') . '/' . $user_id . '/';
    if(!is_dir($uploaddir))
        mkdir( $uploaddir, 0777 );

    // переместим файлы из временной директории в указанную
    foreach( $_FILES as $file ){
        if( move_uploaded_file( $file['tmp_name'], $uploaddir . basename($filename) ) ){

            $filecreate = $wpdb->query($wpdb->prepare( "INSERT INTO p_file ( `user_id`,`folder`, `filename`, `filedir`, `filesize`, `group_id`, `year` ) 
				VALUES (%d, %s, %s, %s, %s, %s, %d)"
                ,$user_id
                ,$folder
                ,$file['name']
                ,basename($filename)
                ,$file['size']
                ,$group_id
                ,date('Y')
            ));
            $wpdb->insert_id;

            $files[] = realpath( $uploaddir . $filename );
            if(!$filecreate)$error = true;
        } else {
            $error = true;
        }
    }
}