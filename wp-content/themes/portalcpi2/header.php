<!DOCTYPE html>
<html lang="ru">
	<head>
		<title>Система для координации процедур оценивания «Центра Педагогических Измерений»</title>

		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="cpi,nis,edu">
		<meta name="description" content="Система для координации процедур оценивания «Центра Педагогических Измерений»">
		<!-- END META -->
		<link rel="shortcut icon" href="<?= bloginfo('template_url') ?>/assets/img/favicon.ico" type="image/x-icon">
		<!-- BEGIN STYLESHEETS -->
		<link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/material-design-iconic-font.min.css?1421434286" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/libs/select2/select2.css?1424887856" />
		<link type="text/css" rel="stylesheet" href="<?= bloginfo('template_url') ?>/assets/css/theme-1/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
		

		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="<?= bloginfo('template_url') ?>/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="<?= bloginfo('template_url') ?>/assets/js/libs/utils/respond.min.js?1403934956"></script>
        <![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed menubar-pin ">

		<!-- BEGIN HEADER-->
		<header id="header" class="header-inverse">
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
									<span class="text-lg text-bold text-primary"><img src="https://courses.cpi-nis.kz/pluginfile.php/1/theme_moove/logo/1607054818/logo_eng_cpi300.png"><span class="title_text">Система для координации процедур оценивания «Центра Педагогических Измерений»</span></span>
								</a>
							</div>
						</li>
						
					</ul>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="headerbar-right">
					<ul class="header-nav header-nav-options">
						<li>
								<button type="submit" class="btn btn-icon-toggle ink-reaction "><i class="fa ">KZ</i></button>
								<button type="submit" class="btn btn-icon-toggle ink-reaction active"><i class="fa ">RU</i></button>
						</li>
					

					</ul><!--end .header-nav-options -->
					<?php if(is_user_logged_in()): ?>
					<ul class="header-nav header-nav-profile">
						<li class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
								<img src="<?= bloginfo('template_url') ?>/assets/img/user-160x160-1.png" alt="" />
								<span class="profile-info">
									<?php nameUser(get_current_user_id(), 2); ?>
									<small><?php echo getAccess(get_current_user_id())->access_name; ?></small>
								</span>
							</a>
							<ul class="dropdown-menu animation-dock">
								<li><a href="/members/?z=user_page&id=<?= get_current_user_id() ?>">Мой профиль</a></li>
								<li class="divider"></li>
								<li><a href="<?php echo wp_logout_url(home_url()); ?>"><i class="fa fa-fw fa-power-off text-danger"></i> Выход</a></li>
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