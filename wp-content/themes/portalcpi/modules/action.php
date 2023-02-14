<?php
/*
Template Name: action
Template Post Type: post, page, product
*/

if(!is_user_logged_in()) {
    sessionexpired();
    //if($_GET['z'] == 'sheet') wp_redirect(site_url());
}else{
    pageCreate('/modules/action/');
}


?>
