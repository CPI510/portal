<?php
if(isset($_GET['authAnoherid'])){
    authAnoher($_GET['authAnoherid']);
    echo'<meta http-equiv="refresh" content="0;url=/" />';
    exit();
}

session_start();

//if($_GET['lang']) $_SESSION['lang'] = $_GET['lang'];
//if(!isset($_SESSION['lang'])) $_SESSION['lang'] = 'front_ru';
//include_once(get_stylesheet_directory() . '/assets/lang/' . $_SESSION['lang'] . '.php');

if( (is_page('users') && $_GET['z'] == 'folders') || is_page('members')){
    if($_GET['lang']) $_SESSION['lang'] = $_GET['lang'];
    if(!isset($_SESSION['lang'])) $_SESSION['lang'] = 'kz';
    include_once(get_stylesheet_directory() . '/assets/lang/' . $_SESSION['lang'] . '.php');
}else{
    if(isset($_GET['id'])){
        $groupid = $_GET['id'];
    } elseif (isset($_GET['group_id'])){
        $groupid = $_GET['group_id'];
    } elseif (isset($_GET['group'])){
        $groupid = $_GET['group'];
    }

    if($name_var = translateDir($groupid) != 'name'){
        $p_name = "name_kaz";
        $name = "name_kaz";
        $lang_name = 'lang_name_kz';
        $name_org = "name_org_kaz";
    }else{
        $p_name = "p_name";
        $name = 'name';
        $lang_name = 'lang_name_ru';
        $name_org = 'name_org';
    }
}


?><!DOCTYPE html>
<html lang="ru">
	<head>
		<title><?= MAIN_NAMES ?></title>

		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="cpi,nis,edu">
		<meta name="description" content="<?= MAIN_NAMES ?>">
		<!-- END META -->
		<link rel="shortcut icon" href="<?= bloginfo('template_url') ?>/assets/img/favicon.ico" type="image/x-icon">
		<!-- BEGIN STYLESHEETS -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/material-design-iconic-font.min.css?1421434286" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/libs/select2/select2.css?1424887856" />
<!--        <link type="text/css" rel="stylesheet" href="--><?//= bloginfo('template_url') ?><!--/assets/css/theme-1/libs/multi-select/multi-select.css?1424887857" />-->
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/libs/bootstrap-datepicker/datepicker3.css?1424887858" />

        <link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/bootstrap_datetimepicker/css/bootstrap-datetimepicker.min.css" />
		<!-- END STYLESHEETS -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="<?= bloginfo('template_url') ?>/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="<?= bloginfo('template_url') ?>/assets/js/libs/utils/respond.min.js?1403934956"></script>
        <![endif]-->
        <style>
            .short {
                display: none;
            }
            @media only screen and (max-width: 1100px) {
                .long {
                    display: none;
                }
                .short {
                    display: inline;
                }
            }
        </style>
	</head>
	<body class="menubar-hoverable header-fixed menubar-pin ">
		<!-- BEGIN HEADER-->
		<header id="header" class="header">
			<div class="headerbar">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="headerbar-left">
					<ul class="header-nav header-nav-options">
                    <li>
							<a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
								<i class="fa fa-bars"></i>
							</a>
						</li>
						<li class="header-nav-brand" >
							<div class="brand-holder">
								<a href="/">
									<span class="text-lg text-bold text-primary"><img src="https://courses.cpi-nis.kz/pluginfile.php/1/theme_moove/logo/1607054818/logo_eng_cpi300.png"><span class="title_text"><span class="long"><?= MAIN_NAMES ?></span><span class="short"><?= TITLESHORT ?></span></span></span>
								</a>
							</div>
						</li>
						
					</ul>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="headerbar-right">
					<ul class="header-nav header-nav-options">
                        <?php if( (is_page('users') && $_GET['z'] == 'folders') || is_page('members') ): ?>
                                    <li>
                                            <button type="submit" class="btn btn-icon-toggle ink-reaction <?= ($_SESSION['lang'] == 'kz') ? "active": "" ?>"><i class="fa "><a href="<?= (isset($_GET['lang'])) ? substr($_SERVER['REQUEST_URI'], 0, -8) : $_SERVER['REQUEST_URI'] ?>&lang=kz">KZ</a></i></button>
                                            <button type="submit" class="btn btn-icon-toggle ink-reaction <?= ($_SESSION['lang'] == 'ru') ? "active": "" ?>"><i class="fa "><a href="<?= (isset($_GET['lang'])) ? substr($_SERVER['REQUEST_URI'], 0, -8) : $_SERVER['REQUEST_URI'] ?>&lang=ru">RU</a></i></button>
                                    </li>
                        <?php endif; ?>

					</ul><!--end .header-nav-options -->
					<?php if(is_user_logged_in()): ?>
					<ul class="header-nav header-nav-profile">
						<li class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
								<img src="<?= bloginfo('template_url') ?>/assets/img/user-160x160-1.png" alt="" />
								<span class="profile-info">
									<?php nameUser(get_current_user_id(), 2); ?>
									<small><?php echo getAccess(get_current_user_id())->{"access_name_" . $_SESSION['lang']}; ?></small>
								</span>
							</a>
							<ul class="dropdown-menu animation-dock">
								<li><a href="/members/?z=user_page&id=<?= get_current_user_id() ?>"><?= MYPROFILE ?></a></li>
								<li class="divider"></li>
								<li><a href="<?php echo wp_logout_url(home_url()); ?>"><i class="fa fa-fw fa-power-off text-danger"></i> <?= EXITU ?></a></li>
							</ul><!--end .dropdown-menu -->
						</li><!--end .dropdown -->
					</ul><!--end .header-nav-profile -->
					<?php endif; ?>
				</div><!--end #header-navbar-collapse -->
			</div>
		</header>
		<!-- END HEADER-->

		<!-- BEGIN BASE-->
		<div id="base">

			<!-- BEGIN OFFCANVAS LEFT -->
			<div class="offcanvas">
			</div><!--end .offcanvas-->
			<!-- END OFFCANVAS LEFT -->

			<!-- BEGIN CONTENT-->
			<div id="content">
			<section>