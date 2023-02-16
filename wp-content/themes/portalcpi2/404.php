<?php
if(is_user_logged_in()) get_header();
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