
            </section>
			<!-- BEGIN BLANK SECTION -->
            </div><!--end #content-->
            <!-- END CONTENT -->
            <div id="menubar" class="menubar">
				<div class="menubar-fixed-panel">
					<div>
						<a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
							<i class="fa fa-bars"></i>
						</a>
					</div>
					<div class="expanded">
						<a href="../../html/dashboards/dashboard.html">
							<span class="text-lg text-bold text-primary ">MATERIAL&nbsp;ADMIN</span>
						</a>
					</div>
				</div>
				<div class="menubar-scroll-panel">
					
					<!-- BEGIN MAIN MENU -->
					<ul id="main-menu" class="gui-controls">

					<?php if(is_user_logged_in()): ?>

						<!-- BEGIN DASHBOARD -->
						<li>
							<a href="/" class="<?= (is_page('Main')) ? "active":"" ?>">
								<div class="gui-icon"><i class="md md-home"></i></div>
								<span class="title">Главная</span>
							</a>
						</li><!--end /menu-li -->
						<!-- END DASHBOARD -->

						<?php if(getAccess(get_current_user_id())->access == 1):?><!-- BEGIN PROGRAMM -->
						<li class="gui-folder">
							<a>
                                <div class="gui-icon"><i class="md md-local-library"></i></div>
								<span class="title">Программы</span>
							</a>
							<!--start submenu -->
							<ul>
								<li><a href="/programs/?z=list" class="<?= (is_page('programs') && ($_GET['z'] == 'list' || $_GET['z'] == 'edit')) ? "active":"" ?>"><span class="title">Список программ</span></a></li>
								<li><a href="/programs/?z=folder" class="<?= (is_page('programs') && ($_GET['z'] == 'folder')) ? "active":"" ?>"><span class="title">Список папок</span></a></li>
							</ul><!--end /submenu -->
						</li><!--end /menu-li -->
						<!-- END PROGRAMM --><?php endif; ?>

						<!-- BEGIN MEMBERS -->
						<li class="gui-folder">
							<a>
								<div class="gui-icon"><i class="md md-group fa-fw"></i></div>
								<span class="title">Участники</span>
							</a>
							<!--start submenu -->
							<ul>
								<li><a href="/members/?z=list" class="<?= (is_page('members') && ($_GET['z'] == 'list'|| $_GET['z'] == 'edit' || $_GET['z'] == 'user_page')) ? "active":"" ?>"><span class="title">Список</span></a></li>
								<li><a href="/members/?z=add" class="<?= (is_page('members') && $_GET['z'] == 'add') ? "active":"" ?>"><span class="title">Добавить</span></a></li>
							</ul><!--end /submenu -->
						</li><!--end /menu-li -->
						<!-- END MEMBERS -->

						<!-- BEGIN GROUPS -->
						<li class="gui-folder">
							<a>
								<div class="gui-icon"><i class="md md-web"></i></div>
								<span class="title">Группы</span>
							</a>
							<!--start submenu -->
							<ul>
								<li><a href="/groups/?z=list" class="<?= (is_page('groups') && ($_GET['z'] == 'list'|| $_GET['z'] == 'edit')) ? "active":"" ?>"><span class="title">Список</span></a></li>
								<li><a href="/groups/?z=add" class="<?= (is_page('groups') && $_GET['z'] == 'add') ? "active":"" ?>"><span class="title">Добавить</span></a></li>
							</ul><!--end /submenu -->
						</li><!--end /menu-li -->
						<!-- END GROUPS -->

						<!-- BEGIN TABLES -->
						<li class="gui-folder">
							<a>
								<div class="gui-icon"><i class="fa fa-table"></i></div>
								<span class="title">Отчеты</span>
							</a>
							<!--start submenu -->
							<ul>
								<li><a href="#" ><span class="title">Первый отчет</span></a></li>
								<li><a href="#" ><span class="title">Второй отчет</span></a></li>
								<li><a href="#" ><span class="title">Третий отчет</span></a></li>
							</ul><!--end /submenu -->
						</li><!--end /menu-li -->
						<!-- END TABLES -->

						<!-- BEGIN FOR USER -->
						<li class="gui-folder">
							<a>
                                <div class="gui-icon"><i class="fa fa-folder-open fa-fw"></i></div>
								<span class="title">Загрузка файлов</span>
							</a>
							<!--start submenu -->
							<ul>
								<li><a href="/users/?z=folders" class="<?= (is_page('users') && ($_GET['z'] == 'folders' || $_GET['z'] == 'add_file')) ? "active":"" ?>"><span class="title">Папки</span></a></li>
							</ul><!--end /submenu -->
						</li><!--end /menu-li -->
						<!-- END FOR USER -->
					<?php endif; ?>
					
					<?php if(is_page('registration')): ?>
							<!-- BEGIN REGGROUP -->
							<li>
								<a href="" class="active">
									<div class="gui-icon"><i class="md md-pages"></i></div>
									<span class="title">Регистрация в группу</span>
								</a>

							</li><!--end /menu-li -->
							<!-- END REGGROUP -->
							<?php endif; ?>
							
					</ul><!--end .main-menu -->
					<!-- END MAIN MENU -->
					
					

<div class="menubar-foot-panel">
						<small class="no-linebreak hidden-folded">
							<span class="opacity-75">2020 &copy;</span> <strong>ЦПИ</strong>
						</small>
					</div>
				</div><!--end .menubar-scroll-panel-->
			</div><!--end #menubar-->
			<!-- END MENUBAR -->

		</div><!--end #base-->
		<!-- END BASE -->

		<!-- BEGIN JAVASCRIPT -->
		<script src="<?= bloginfo('template_url') ?>/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/libs/bootstrap/bootstrap.min.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/libs/spin.js/spin.min.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/libs/autosize/jquery.autosize.min.js"></script>
		<?php if ((is_page('groups') || is_page('members')) && ($_GET['z'] == 'add'|| $_GET['z'] == 'edit')): ?>
			<script src="<?= bloginfo('template_url') ?>/assets/js/libs/select2/select2.min.js"></script>
			<script src="<?= bloginfo('template_url') ?>/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
			<script src="<?= bloginfo('template_url') ?>/assets/js/libs/bootstrap-datepicker/locales/bootstrap-datepicker.ru.js"></script>
		<?php endif;?>
		<script src="<?= bloginfo('template_url') ?>/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/App.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppNavigation.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppCard.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppForm.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppNavSearch.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppVendor.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/demo/DemoFormComponents.js"></script>
		
		<?php if ((is_page('groups') || is_page('members')) && ($_GET['z'] == 'add'|| $_GET['z'] == 'edit')): ?>
			<script src="<?= bloginfo('template_url') ?>/assets/js/core/actions.js"></script>
		<?php endif;?>

		<?php if (is_page('registration')): ?>
			<script src="<?= bloginfo('template_url') ?>/assets/js/core/actionsReg.js"></script>
		<?php endif;?>

		<script src="<?= get_site_url() ?>/wp-content/plugins/wp-file-upload/js/wordpress_file_upload_functions.js?ver=4.9.8"></script>
		<script>
		window.onresize = function() {
			if((window.innerWidth || document.documentElement.clientWidth) <= 1000){
				document.querySelector('.title_text').textContent = "Портал CPI";
			}else{
				document.querySelector('.title_text').textContent = "Система для координации процедур оценивания «Центра Педагогических Измерений»";
			}
		}
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>

			