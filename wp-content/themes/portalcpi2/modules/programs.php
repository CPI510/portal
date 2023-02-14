<?php
/* 
Template Name: programs 
Template Post Type: post, page, product 
*/

if(getAccess(get_current_user_id())->access != 1) wp_redirect(site_url());

authAll();

get_header();

pageCreate('/modules/programs/');

get_Footer();
?>
