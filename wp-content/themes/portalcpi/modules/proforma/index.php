<?php
global $wpdb;
if ($_GET['form'] && $_GET['group']) require_once( __DIR__ . "/forms/form_{$_GET['form']}.php");
else echo "Нет данных!";
