<?php
if(is_user_logged_in()){
    get_header();
}else{
    ?>
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
    <?php
}
?>
<!-- BEGIN 404 MESSAGE -->
<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li><a href="/">Главная</a></li>
							<li class="active">404</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-lg-12 text-center">
								<h1><span class="text-xxxl text-light">404 <i class="md md-warning text-primary"></i></span></h1>
								<h2 class="text-light">Введен ошибочный адрес страницы!</h2>
							</div><!--end .col -->
						</div><!--end .row -->
					</div><!--end .section-body -->
				</section>
				<!-- END 404 MESSAGE -->


<?php
if(is_user_logged_in()) get_footer();
?>