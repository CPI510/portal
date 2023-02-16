<?php
/* 
Template Name: groups 
Template Post Type: post, page, product 
*/

authAll();

get_header();
accessUser(getAccess(get_current_user_id())->access);
pageCreate('/modules/groups/');

get_Footer();
?>
