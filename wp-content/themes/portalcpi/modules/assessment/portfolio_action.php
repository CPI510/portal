<?php
global $wpdb;

checkPermissons([1]);

//printAll($_GET);

if( $_GET['fileid'] && $_GET['res'] == 0 ){

    $wpdb->update('p_file',[
        'portfolio' => 1
    ],[
        'id' => $_GET['fileid']
    ],[
        '%d'
    ]);

}elseif ( $_GET['fileid'] && $_GET['res'] == 1 ){

    $wpdb->update('p_file',[
        'portfolio' => 0
    ],[
        'id' => $_GET['fileid']
    ],[
        '%d'
    ]);
}

if($_SERVER['HTTP_REFERER']) $url = $_SERVER['HTTP_REFERER'];
else $url = site_url();

echo '<meta http-equiv="refresh" content="2;url=' . $url . '" />';
exit();
?>