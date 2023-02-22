
            </section>
			<!-- BEGIN BLANK SECTION -->
            </div><!--end #content-->
            <!-- END CONTENT -->
            <?php
//            if(isset($_GET['id'])){
//                $groupid = $_GET['id'];
//                $name_var = translateDir($_GET['id']);
//            } elseif (isset($_GET['group_id'])){
//                $groupid = $_GET['group_id'];
//                $name_var = translateDir($_GET['group_id']);
//            }
//
//            if($name_var != 'name'){
//                $p_name = "name_kaz";
//                $name = "name_kaz";
//                $lang_name = 'lang_name_kz';
//                $name_org = "name_org_kaz";
//            }else{
//                $p_name = "p_name";
//                $name = 'name';
//                $lang_name = 'lang_name_ru';
//                $name_org = 'name_org';
//            }

            ?>
            <div id="menubar" class="menubar">
				<div class="menubar-fixed-panel">
					<div>
						<a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
							<i class="fa fa-bars"></i>
						</a>
					</div>
					<div class="expanded">
						<a href="../../html/dashboards/dashboard.html">
							<span class="text-lg text-bold text-primary ">Система</span>
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
								<span class="title"><?= MAIN ?></span>
							</a>
						</li><!--end /menu-li -->
						<!-- END DASHBOARD -->


                        <?php if(getAccess(get_current_user_id())->access == 1):?>

                            <!-- BEGIN training_center -->
                            <li class="gui-folder">
                                <a class="<?= (is_page('action') ) ? "active":"" ?>">
                                    <div class="gui-icon"><i class="md md-picture-in-picture"></i></div>
                                    <span class="title">Контент</span>
                                </a>
                                <!--start submenu -->
                                <ul>
                                    <li><a href="/action/?z=listfrontpage" class="<?= ($_GET['z'] == 'listfrontpage' ) ? "active":"" ?>"><span class="title">Список контента главной страницы</span></a></li>
                                </ul><!--end /submenu -->
                            </li><!--end /menu-li -->
                                 <!-- END training_center -->

                            <!-- BEGIN training_center -->
                            <li class="gui-folder">
                                <a>
                                    <div class="gui-icon"><i class="md md-account-balance"></i></div>
                                    <span class="title">Центр обучения</span>
                                </a>
                                <!--start submenu -->
                                <ul>
                                    <li><a href="/training_center/" class="<?= (is_page('training_center') ) ? "active":"" ?>"><span class="title">Список</span></a></li>
                                </ul><!--end /submenu -->
                            </li><!--end /menu-li -->
                            <!-- END training_center -->

                            <!-- BEGIN PROGRAMM -->
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
						<!-- END PROGRAMM -->
                        <?php endif; ?>

						<!-- BEGIN MEMBERS -->
						<?php if(getAccess(get_current_user_id())->access == 1 || getAccess(get_current_user_id())->access == 2 || getAccess(get_current_user_id())->access == 3):?>
						<li class="gui-folder">
							<a>
								<div class="gui-icon"><i class="md md-group fa-fw"></i></div>
								<span class="title">Участники</span>
							</a>
							<!--start submenu -->
							<ul>
								<li><a href="/members/?z=list" class="<?= (is_page('members') && ($_GET['z'] == 'list'|| $_GET['z'] == 'edit' || $_GET['z'] == 'user_page')) ? "active":"" ?>"><span class="title">Список</span></a></li>
								<?php if(getAccess(get_current_user_id())->access == 1):?>
								<li><a href="/members/?z=add" class="<?= (is_page('members') && $_GET['z'] == 'add') ? "active":"" ?>"><span class="title">Добавить</span></a></li>
								<?php endif; ?>
							</ul><!--end /submenu -->
						</li><!--end /menu-li -->
						<?php endif; ?>
						<!-- END MEMBERS -->

                        <?php if(getAccess(get_current_user_id())->access != 5 ):?>
						<!-- BEGIN GROUPS -->
						<li class="gui-folder">
							<a>
								<div class="gui-icon"><i class="md md-web"></i></div>
								<span class="title"><?= GROUPS ?></span>
							</a>
							<!--start submenu -->
							<ul>
								<li><a href="/groups/?z=list&y=<?= date('Y') ?>" class="<?= (is_page('groups') && ( ($_GET['z'] == 'list' && $_GET['notactive'] != 1 )|| $_GET['z'] == 'edit')) ? "active":"" ?>"><span class="title">Список</span></a></li>
								<?php if(getAccess(get_current_user_id())->access == 1):?>
								    <li><a href="/groups/?z=list&notactive=1&y=<?= date('Y') ?>" class="<?= (is_page('groups') && $_GET['z'] == 'list' && $_GET['notactive'] == 1) ? "active":"" ?>"><span class="title">Неактивные</span></a></li>
								<?php endif; ?>
                                <?php if(get_current_user_id() == 1):?>
                                    <li><a href="/groups/?z=foradmin&y=<?= date('Y') ?>" class="<?= (is_page('groups') && $_GET['z'] == 'foradmin' ) ? "active":"" ?>"><span class="title">Для админа</span></a></li>
                                <?php endif; ?>
							</ul><!--end /submenu -->
						</li><!--end /menu-li -->
						<!-- END GROUPS -->
                        <?php endif; ?>

				

						<!-- BEGIN FOR USER -->
                        <?php if(getAccess(get_current_user_id())->access == 5 || getAccess(get_current_user_id())->access == 4 ):?>
						<li class="gui-folder">
							<a>
                                <div class="gui-icon"><i class="fa fa-folder-open fa-fw"></i></div>
								<span class="title"><?= UPLOAD_FILES ?></span>
							</a>
							<!--start submenu -->
							<ul>
								<li><a href="/users/?z=folders" class="<?= (is_page('users') && ($_GET['z'] == 'folders' || $_GET['z'] == 'add_file' || $_GET['z'] == 'comment')) ? "active":"" ?>"><span class="title"><?= FOLDERS ?></span></a></li>
							</ul><!--end /submenu -->
						</li><!--end /menu-li -->
                        <?php endif; ?>
						<!-- END FOR USER -->
					<?php endif; ?>
					
					<?php if(is_page('registration')): ?>
							<!-- BEGIN REGGROUP -->
							<li>
								<a href="" class="active">
									<div class="gui-icon"><i class="md md-pages"></i></div>
									<span class="title"><?= REGISTRATION[0] ?></span>
								</a>

							</li><!--end /menu-li -->
							<!-- END REGGROUP -->
							<?php endif; ?>

							<?php if(getAccess(get_current_user_id())->access == 1):?>
							<!-- BEGIN DOWNLOAD
							<li>
								<a href="/server_file/?zip=1" >
									<div class="gui-icon"><i class="md md-play-download"></i></div>
									<span class="title">Скачать все файлы</span>
								</a>
							</li><!--end /menu-li -->
							<!-- END DOWNLOAD -->
							<?php endif; ?>
							
					</ul><!--end .main-menu -->
					<!-- END MAIN MENU -->
					
		<h2>
            <?php









            ?>
        </h2>

<div class="menubar-foot-panel">
						<small class="no-linebreak hidden-folded">
							<span class="opacity-75"><?= date('Y') ?> &copy;</span> <strong>ЦПИ</strong>
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
        <script src="<?= bloginfo('template_url') ?>/assets/js/libs/multi-select/jquery.multi-select.js"></script>
		<?php if ((is_page('groups') || is_page('members') || is_page('programs')) && ($_GET['z'] == 'add' || $_GET['z'] == 'edit' || $_GET['z'] == 'folder' || $_GET['z'] == 'group' )): ?>
			<script src="<?= bloginfo('template_url') ?>/assets/js/libs/select2/select2.min.js"></script>
			<script src="<?= bloginfo('template_url') ?>/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
            <script src="<?= bloginfo('template_url') ?>/assets/js/libs/bootstrap-datepicker/locales/bootstrap-datepicker.ru.js"></script>

            <!-- start вссе для календаря jquery -->
            <script src="<?= bloginfo('template_url') ?>/assets/bootstrap_datetimepicker/js/moment-with-locales.min.js"></script>
            <script src="<?= bloginfo('template_url') ?>/assets/bootstrap_datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
            <script>
                $(document).ready(function () {
                    $("#datetimepicker1").on("dp.show", function (e) {
                        $('.OK').html("Готово");
                    });
                });
                $(document).ready(function () {
                    $("#datetimepicker2").on("dp.show", function (e) {
                        $('.OK').html("Готово");
                    });
                });

                $(document).ready(function () {
                    $("#datetimepickerobserver").on("dp.show", function (e) {
                        $('.OK').html("Готово");
                    });
                });

                $(document).ready(function () {
                    $("#datetimepickerexpert").on("dp.show", function (e) {
                        $('.OK').html("Готово");
                    });
                });
                $(document).ready(function () {
                    $("#datetimepickermoderator").on("dp.show", function (e) {
                        $('.OK').html("Готово");
                    });
                });
                $(document).ready(function () {
                    $("#datetimepickerteamleader").on("dp.show", function (e) {
                        $('.OK').html("Готово");
                    });
                });
                $(document).ready(function () {
                    $("#datetimepickertrener").on("dp.show", function (e) {
                        $('.OK').html("Готово");
                    });
                });
                $(document).ready(function () {
                    $("#datetimepickerindependenttrainer").on("dp.show", function (e) {
                        $('.OK').html("Готово");
                    });
                });

            </script>
            <script type="text/javascript">
                $(function () {
                    $(function () {
                        $('#datetimepicker1').datetimepicker({
                            format: 'YYYY-MM-DD H:mm',
                            locale: 'ru',
                            showClose: true,
                            icons: {
                                close: 'OK'
                            }

                        });
                        $('#datetimepicker1 > input').click(function(){
                            $('#datetimepicker1 .input-group-addon').trigger('click');
                        })
                    });

                    $(function () {
                        $('#datetimepicker2').datetimepicker({
                            format: 'YYYY-MM-DD H:mm',
                            locale: 'ru',
                            showClose: true,
                            icons: {
                                close: 'OK'
                            }

                        });
                        $('#datetimepicker2 > input').click(function(){
                            $('#datetimepicker2 .input-group-addon').trigger('click');
                        })
                    });

                    $(function () {
                        $('#datetimepickerobserver').datetimepicker({
                            format: 'YYYY-MM-DD H:mm',
                            locale: 'ru',
                            showClose: true,
                            icons: {
                                close: 'OK'
                            }

                        });
                        $('#datetimepickerobserver > input').click(function(){
                            $('#datetimepickerobserver .input-group-addon').trigger('click');
                        })
                    });

                    $(function () {
                        $('#datetimepickerexpert').datetimepicker({
                            format: 'YYYY-MM-DD H:mm',
                            locale: 'ru',
                            showClose: true,
                            icons: {
                                close: 'OK'
                            }

                        });
                        $('#datetimepickerexpert > input').click(function(){
                            $('#datetimepickerexpert .input-group-addon').trigger('click');
                        })
                    });

                    $(function () {
                        $('#datetimepickermoderator').datetimepicker({
                            format: 'YYYY-MM-DD H:mm',
                            locale: 'ru',
                            showClose: true,
                            icons: {
                                close: 'OK'
                            }

                        });
                        $('#datetimepickermoderator > input').click(function(){
                            $('#datetimepickermoderator .input-group-addon').trigger('click');
                        })
                    });

                    $(function () {
                        $('#datetimepickerteamleader').datetimepicker({
                            format: 'YYYY-MM-DD H:mm',
                            locale: 'ru',
                            showClose: true,
                            icons: {
                                close: 'OK'
                            }

                        });
                        $('#datetimepickerteamleader > input').click(function(){
                            $('#datetimepickerteamleader .input-group-addon').trigger('click');
                        })
                    });

                    $(function () {
                        $('#datetimepickertrener').datetimepicker({
                            format: 'YYYY-MM-DD H:mm',
                            locale: 'ru',
                            showClose: true,
                            icons: {
                                close: 'OK'
                            }

                        });
                        $('#datetimepickertrener > input').click(function(){
                            $('#datetimepickertrener .input-group-addon').trigger('click');
                        })
                    });

                    $(function () {
                        $('#datetimepickerindependenttrainer').datetimepicker({
                            format: 'YYYY-MM-DD H:mm',
                            locale: 'ru',
                            showClose: true,
                            icons: {
                                close: 'OK'
                            }

                        });
                        $('#datetimepickerindependenttrainer > input').click(function(){
                            $('#datetimepickerindependenttrainer .input-group-addon').trigger('click');
                        })
                    });


                });

            </script>
            <!-- end все для календаря jquery -->
		<?php endif;?>
		<script src="<?= bloginfo('template_url') ?>/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
        <script src="<?= bloginfo('template_url') ?>/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
        <script src="<?= bloginfo('template_url') ?>/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/App.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppNavigation.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppCard.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppForm.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppNavSearch.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/source/AppVendor.js"></script>
		<script src="<?= bloginfo('template_url') ?>/assets/js/core/demo/DemoFormComponents.js"></script>
        <script src="<?= bloginfo('template_url') ?>/assets/js/core/demo/Demo.js"></script>

		<?php if ((is_page('groups') || is_page('members')) && ($_GET['z'] == 'add'|| $_GET['z'] == 'edit')): ?>
			<script src="<?= bloginfo('template_url') ?>/assets/js/core/actions.js"></script>
		<?php endif;?>

		<?php if (is_page('registration')): ?>
			<script src="<?= bloginfo('template_url') ?>/assets/js/core/actionsReg.js" type="module"></script>
		<?php endif;?>
		<!-- END JAVASCRIPT -->

            <!-- Yandex.Metrika counter -->
            <script type="text/javascript" >
                (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
                    m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
                (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

                ym(75406873, "init", {
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    trackHash:true
                });
            </script>
            <noscript><div><img src="https://mc.yandex.ru/watch/75406873" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
            <!-- /Yandex.Metrika counter -->

            <div id="device-breakpoints"><div class="device-xs visible-xs" data-breakpoint="xs"></div><div class="device-sm visible-sm" data-breakpoint="sm"></div><div class="device-md visible-md" data-breakpoint="md"></div><div class="device-lg visible-lg" data-breakpoint="lg"></div></div>
	</body>
</html>

			