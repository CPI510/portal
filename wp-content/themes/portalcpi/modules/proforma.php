<?php
/* 
Template Name: proforma
Template Post Type: post, page, product 
*/

authAll();

get_header();

if(!is_user_logged_in()) {
    sessionexpired();

}else{
    pageCreate('/modules/proforma/');
}


get_Footer();
?>
