<?php
/* 
Template Name: front_page
Template Post Type: post, page, product 
*/?>

<?php if(is_user_logged_in()):?>
	<?php
    if(getAccess(get_current_user_id())->access == 5)
        echo'<meta http-equiv="refresh" content="0;url=/users/?z=folders" />';
    else
        echo'<meta http-equiv="refresh" content="0;url=/groups/?z=list" />';
exit();?>
	
<?php else: ?>
<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if lt IE 7 ]> <html lang="en" class="ie6">    <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7">    <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8">    <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="ie9">    <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Система для координации процедур оценивания «Центра Педагогических Измерений»</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		
 		<!-- google font  -->
		<link href='https://fonts.googleapis.com/css?family=Oxygen:400,700,300' rel='stylesheet' type='text/css'>
        <!-- Favicon
		============================================ -->
		<link rel="shortcut icon" href="<?= bloginfo('template_url') ?>/assets/img/favicon.ico" type="image/x-icon">       

		<!-- Bootstrap CSS
		============================================ -->      
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/bootstrap.min.css">
		<!-- Add venobox -->
		<link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/venobox/venobox.css" type="text/css" media="screen" />      
		<!-- owl.carousel CSS
		============================================ -->      
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/owl.carousel.css">
        
		<!-- owl.theme CSS
		============================================ -->      
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/owl.theme.css">
           	
		<!-- owl.transitions CSS
		============================================ -->      
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/owl.transitions.css">
        
		<!-- font-awesome.min CSS
		============================================ -->      
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/font-awesome.min.css">
		<!-- Nivo Slider CSS -->
		<link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/nivo-slider.css">        
 		<!-- animate CSS
		============================================ -->         
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/animate.css">

 		<!-- normalize CSS
		============================================ -->        
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/normalize.css">      
        <!-- main CSS
		============================================ -->          
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/main.css">
        
        <!-- style CSS
		============================================ -->          
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/style.css">
        
        <!-- responsive CSS
		============================================ -->          
        <link rel="stylesheet" href="<?= bloginfo('template_url') ?>/front_page/css/responsive.css">
		<link rel="stylesheet" media="screen" href="<?= bloginfo('template_url') ?>/front_page/css/particles.css">
		<link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        
        <script src="<?= bloginfo('template_url') ?>/front_page/js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">Вы используете страую версию браузера!</p>
        <![endif]-->
    <!--Start nav  area --> 
	<div class="nav_area" id="sticker">
		<div class="container">
			<div class="row">
				<!--logo area-->
				<div class="col-md-3 col-sm-3 col-xs-4">
					<div class="logo"><a href="/"><img height="54px" src="https://courses.cpi-nis.kz/pluginfile.php/1/theme_moove/logo/1610698934/logo_eng_cpi300.png" alt="" /></a></div>
				</div>
				<!--end logo area-->
				<!--nav area-->
				<div class="col-md-9 col-sm-9 col-xs-8">
					<!--  nav menu-->
					<nav class="menu">
						<ul class="navid">
							<li><a href="#home">Главная</a></li>
							<li><a href="#about">Программы</a></li>
							<li><a href="#contact">Контакты</a></li>
						</ul>
					</nav>
					<!--end  nav menu-->
					<!--moblie menu area-->
						<div class="dropdown mabile_menu">
							<a data-toggle="dropdown" class="mabile-menu" href="#"><span> МЕНЮ </span><i class="fa fa-bars"></i></a>
                              <ul class="dropdown-menu mabile_menus drop_mabile navid">
                                    <li><a href="#home">Главная</a></li>
                                    <li><a href="#about">Программы</a></li>
                                    <li><a href="#contact">Контакты</a></li>
                              </ul>
						</div>	
						<!--end moblie menu-->						
				</div>
				<!--end nav area-->
			</div>	
		</div>
	</div>
	<!--end header  area -->
<div class="slider-wrap home-1-slider" id="home">
    <section>
        <div class="col-sm-7 col-md-7 col-lg-7 names"><h1>Система для координации процедур оценивания «Центра Педагогических Измерений»</h1></div>
        <div class="col-sm-5 col-md-5 col-lg-5">
                <form method="post" action="/login/">
                    <div class="contract_us">
                    <div class="inputt input_change">
                            <span class="message_icon"><i class="fa fa-envelope-o"></i></span>
                            <input type="text" name="log" class="form-control" id="email" placeholder="Email" required="">
                        </div>
                        <div class="inputt input_change">
                            <span class="message_icon"><i class="fa fa-user"></i></span>
                            <input type="password" maxlength="30" name="pwd" class="form-control" id="name" placeholder="Пароль" required="" autocomplete="on">
                        </div>
                        <input type="hidden" name="redirect_to" value="http://cpi.nis.edu.kz/" />
                        <div class="sunmite_button">
                            <button type="submit" name="ok">Кіру \ Войти</button>
                            <button type="submit"> <a href="/login/?action=lostpassword" >Восстановить пароль \ <br>Құпия сөзді қалпына келтіру</a></button>
                        </div>
                    </div>
                </form>
          </div>
    </section>

    <div id="particles-js" >

    </div>
</div>	

	
	<!-- about  area -->
	<div class="about_area" id="about">
		<div class="container">
			<div class="row">
				<!--section title-->
				<div class="col-md-12 col-sm-12 col-lg-12">	
					<div class="section_title">
						<h2 class="title"><span>Программы</span></h2>
					</div>
				</div>
				<!--end section title-->
            </div>
            <?php $allcontents = $wpdb->get_results("SELECT * FROM p_front_page ORDER BY ordercontent") ?>

            <div class="row" style="display:block;">
                <div id="accordion">
                    <?php foreach ($allcontents as $allcontent): ?>
                        <?php ++$q ?>
                        <div class="card">
                            <div class="card-header" id="heading<?=$q?>">
                                <p style="cursor: pointer; color: #0a6ebd" class="text-sm-left" data-toggle="collapse" data-target="#collapse<?=$q?>" aria-expanded="true" aria-controls="collapse<?=$q?>">
                                    <?= str_replace('\"', '"', mb_strtoupper($allcontent->name_content)) ?>
                                </p>
                            </div>

                            <div id="collapse<?=$q?>" class="collapse" aria-labelledby="heading<?=$q?>" data-parent="#accordion">
                                <div class="card-body">
                                    <?= base64_decode($allcontent->content) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
	<!-- end about  area -->

<br><br><br><br><br><br><br><br>
	<div class="footer_area " id="contact">
		<div class="container">
			<div class="row">
				<!--section title-->
				<div class=" col-sm-12 col-md-12 col-lg-12">	
					<div class="section_title service_color">
						<h2 class="title"><span>Контакты</span></h2>
					</div>
				</div>
				<!--end section title-->
			</div>		
			<div class="row">
				<div class="col-sm-4 col-md-4 col-lg-4">
					<div class="single_address fix">
						<div class="address_icon"><span><i class="fa fa-map-marker"></i></span></div>
						<div class="address_text"><p><span>Адрес:</span> ул. Хусейн бен Талал, 21/1 010000, г.Астана, Республика Казахстан</p></div>
					</div>
					<div class="single_address fix">
						<div class="address_icon"><span><i class="fa fa-phone"></i></span></div>
						<div class="address_text"><p><span>Телефон:</span>  +7 (7172) 23-57-66</p></div>
					</div>
					<div class="single_address fix">
						<div class="address_icon"><span><i class="fa fa-envelope-o"></i></span></div>
						<div class="address_text"><p><span>Email: </span> info@cpi.nis.edu.kz</p></div>
					</div>					
				</div>
				<div class="col-sm-8 col-md-8 col-lg-8">
					<div class="map">
					   <!-- Start contact-map -->
                            <div class="contact-map">
							<iframe src="https://yandex.kz/map-widget/v1/-/CBRdERTsoD" width="100%" height="360" frameborder="1" allowfullscreen="true"></iframe>
                            </div>
						<!-- End contact-map -->
					</div>				
				</div>
			
			</div>
		</div>			
	</div>
	<div class="footer_bottom_area" >
		<div class="container">
			<div class="row">
				<div class=" col-sm-12 col-md-12 col-lg-12">
					<div class="footer_text">
						<p>2021 <a href="http://cpi-nis.kz/">ЦПИ</a></p>
					</div>
				</div>
			</div>
		</div>	
	</div>
	<script src="<?= bloginfo('template_url') ?>/front_page/js/particles/particles.js"></script> 
	<script src="<?= bloginfo('template_url') ?>/front_page/js/particles/app.js"></script> 

        <!-- JS -->        
 		<!-- jquery-1.11.3.min js
		============================================ -->
        <script src="<?= bloginfo('template_url') ?>/front_page/js/vendor/jquery-1.11.3.min.js"></script>
        <!-- bootstrap js
       ============================================ -->
        <script src="<?= bloginfo('template_url') ?>/front_page/js/bootstrap.min.js"></script>      
   		<!-- owl.carousel.min js
		============================================ -->       
        <script src="<?= bloginfo('template_url') ?>/front_page/js/owl.carousel.min.js"></script>
	
		<!-- plugins js
		============================================ -->         
        <script src="<?= bloginfo('template_url') ?>/front_page/js/plugins.js"></script>	
        <!-- counterup js
		============================================ -->  		
        <script src="<?= bloginfo('template_url') ?>/front_page/js/jquery.counterup.min.js"></script>
		<script src="<?= bloginfo('template_url') ?>/front_page/js/waypoints.min.js"></script>
		<!-- MixItUp js-->        
		<script src="<?= bloginfo('template_url') ?>/front_page/js/jquery.mixitup.js"></script>
		 <!-- Nivo Slider JS -->
		<script src="<?= bloginfo('template_url') ?>/front_page/js/jquery.nivo.slider.pack.js"></script>       
		<script src="<?= bloginfo('template_url') ?>/front_page/js/jquery.nav.js"></script>           
   		<!-- wow js
		============================================ -->       
        <script src="<?= bloginfo('template_url') ?>/front_page/js/wow.js"></script>
		<!--Activating WOW Animation only for modern browser-->
        <!--[if !IE]><!-->
        <script type="text/javascript">new WOW().init();</script>
        <!--<![endif]-->
		<!-- Add venobox ja -->
		<script type="text/javascript" src="<?= bloginfo('template_url') ?>/front_page/venobox/venobox.min.js"></script>		
   		<!-- main js
		============================================ -->           
        <script src="<?= bloginfo('template_url') ?>/front_page/js/main.js"></script>
    
    
    </body>
</html>
<?php endif; ?>