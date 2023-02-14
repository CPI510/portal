<?php
global $wpdb;

?>
<input type="hidden" id="kz" value="<?= LANGID ?>">
<script src="<?= bloginfo('template_url') ?>/assets/js/core/countDown.js"></script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary"><?= MY_FOLDERS ?></h1>
    </div>
    <div class="col-lg-12">
    </div><!--end .col -->
    <div class="col-md-8">
        <article class="margin-bottom-xxl">
            <?php
            if(isset($_GET['id']) && $_GET['id'] != 'all'){
                $sql_filtr = " AND id_group = %d";
                $idcourse = $_GET['id'];
            }else{
                $sql_filtr = "";
                $idcourse = "";
            }

            $dataCourses = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, r.program_id, r.number_group, r.end_date, p.p_name program_name 
                            FROM p_groups_users g 
                            LEFT OUTER JOIN p_groups r ON r.id = g.id_group
                            LEFT OUTER JOIN p_programs p ON p.id = r.program_id
                            WHERE g.id_user = %d AND r.deleted = 0 $sql_filtr ORDER BY r.date_create DESC",get_current_user_id(), $idcourse ));
            if(empty($dataCourses)){

                if($course_id = get_the_author_meta('reg_course_id', get_current_user_id())){
                    echo'<meta http-equiv="refresh" content="0;url=/registration/?id='.$course_id.'" />';
                }else{
                    echo "<h3>Вам необходимо перейти по ссылке, для регистрации в группе!</h3>";
                }
            }else{

            } //printAll($dataCourses);
            ?>
        </article>
    </div><!--end .col -->


    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <label><?= GROUPS ?>:</label>
                <form>
                    <select class="form-control" name="id" onchange="this.form.submit()">
                        <option value="all"><?= ALL ?></option>
                        <?php $course_all = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, r.program_id, r.number_group, r.end_date FROM p_groups_users g 
                                LEFT OUTER JOIN p_groups r ON r.id = g.id_group 
                                WHERE id_user = %d AND r.deleted = 0", get_current_user_id()));
                        foreach ($course_all as $course_n): ?>
                            <?php if($_GET['id'] == $course_n->id_group): ?>
                                <option value="<?= $course_n->id_group ?>" selected="selected"><?= $course_n->number_group ?></option>
                            <?php else: ?>
                                <option value="<?= $course_n->id_group ?>"><?= $course_n->number_group ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </select>
                    <input type="hidden" name="z" value="<?= $_GET['z'] ?>">
                </form>
            </div>
        </div>
    </div>


    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">

                    <?php $access = getAccess(get_current_user_id())->access;?>

                    <?php if( $access === '1'): ?>
                            <?php $dataFolders = $wpdb->get_results($wpdb->prepare("SELECT f.id folder_id, f.access_id, f.name folder_name, p.user_id, COUNT(p.id) Num_f
                                FROM p_folder f
                                LEFT OUTER JOIN p_file p ON p.folder = f.id AND p.user_id = %d
                                WHERE f.id_program = 1
                                GROUP BY f.id
                                ",get_current_user_id()));?>
                    <div class="card card-outlined style-success">
                        <div class="card-head">
                            <header><i class="fa fa-fw fa-tag"></i> Папки сотрудников портала</header>
                        </div><!--end .card-head -->
                        <div class="card-body style-default-bright">
                            <div class="row">
                            <?php foreach($dataFolders as $folder): ?>
                                <?php if ($folder->access_id): ?>
                                    <?php   foreach(explode(",", $folder->access_id) as $accessid){
                                            $arrAccessId[$accessid] = $accessid;
                                            }
                                    ?>
                                    <?php if ($arrAccessId[$access] == $access): ?>
                                        <div class="col-sm-10">
                                            <p>
                                                <a href="/users/?z=add_file&folder_id=<?= $folder->folder_id ?>" class="btn btn-info"><?= $folder->folder_name ?> (<?= $folder->Num_f ?>)</a>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </div>
                        </div><!--end .card-body -->
                    </div>
                    <?php endif; ?>

                    <?php foreach($dataCourses as $data): ?>

                            <div class="card card-outlined style-success">
                                <div class="card-head">
                                    <header>
                                        <i class="fa fa-info-circle fa-fw text-info" data-toggle="tooltip" data-placement="right" data-original-title="<?= $data->program_name ?>" style="cursor: pointer"></i>
                                        <?= FOLDERS_GROUPS ?>: <?= $data->number_group ?> (<?= DATE_END_DOWNLOAD ?>: <?= $data->end_date ?>).
                                    </header>
                                </div><!--end .card-head -->
                                <div class="card-body style-default-bright">
                        <?php $dataFolders = $wpdb->get_results($s=$wpdb->prepare("SELECT f.id folder_id, f.name folder_name, p.user_id, COUNT(p.id) Num_f
                            FROM p_folder f
                            LEFT OUTER JOIN p_file p ON p.folder = f.id AND p.user_id = %d AND p.group_id = %d
                            WHERE f.id_program = %d
                            GROUP BY f.id ORDER BY f.sort_field
                            ",get_current_user_id(), $data->id_group, $data->program_id ));?>
                            <div class="row">
                            <?php foreach($dataFolders as $folder): ?>
                                <div class="col-sm-10">
                                    <p>
                                        <a href="/users/?z=add_file&folder_id=<?= $folder->folder_id ?>&group_id=<?= $data->id_group ?>" class="btn btn-info" >
                                            <?= $folder->folder_name ?>
                                            (<?= $folder->Num_f ?>)
                                        </a>
                                    </p>
                                </div>

                            <?php endforeach; ?>
                                <?php if( $data->program_id == 7 ): ?>
                                    <div class="col-sm-10">
                                        <p>
                                            <a href="#" id="fileu" data-id="<?=$data->id_user?>" data-link="portal_server/?portfolio_list=<?=$data->id_group?>&lang=<?= $_GET['lang'] ?>" data-toggle="modal" data-target="#Modal" class="btn btn-success">
                                                Портфолио
                                                (<?= $num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM p_file WHERE group_id = %d AND user_id = %d AND portfolio = 1", $data->id_group, $data->id_user)) ?>)
                                            </a>

                                        </p>
                                    </div>
                                <?php endif;?>
                            </div>
                            <?php $recomData = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_proforma_recommendation WHERE user_id = %d AND group_id = %d "
                                , get_current_user_id()
                                , $data->id_group
                            ));
                            if (isset($recomData)) {
                                echo "
                                    <p><b>Рекомендация:</b> {$recomData->recom}</p>";
                            } else {

                            }
                            ?>
                                <h4 class="text-primary-dark"><?= TIME_LEFT ?>: <span id="display<?= ++$q ?>"></span></h4>
                                <script>
                                    const display<?= $q ?> = document.querySelector('#display<?= $q ?>');
                                    countDown('<?= $data->end_date ?>', display<?= $q ?>);
                                    //console.log(new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''));
                                </script>


                        </div><!--end .card-body -->

                    </div>

                    <?php endforeach; ?>
                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
</div>

<div class="modal fade" id="Modal" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="box"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal"><?= ASSESSMENT_SECOND[1] ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>

    const allData = {};
    const fileu = document.querySelectorAll("#fileu"),
        boxdiv = document.querySelector('.box'),
        formreg = document.querySelector('#formreg')
    close = document.querySelector("#close");
    const message = {
        loading: `${document.location.origin}/wp-content/themes/portalcpi/assets/img/spinner.svg`,
        success: "Спасибо, все данные внесены",
        failure: "Что-то пошло не так, попробуйте зайти позднее!"
    };
    const statusMessage = document.createElement('div');

    statusMessage.classList.add('status');

    boxdiv.append(statusMessage);

    for (var i = 0; i < fileu.length ; i++) {
        //let dataid = fileu[i].getAttribute('data-id');
        fileu[i].addEventListener('click', saveData);
        //console.log(fileu[i].getAttribute('data-id'));
    }

    function saveData(event) {

        statusMessage.innerHTML = "";
        var target = event.currentTarget;
        //var parent = target.parentElement.nodeName;
        //console.log(target.getAttribute('data-id'));
        allData.fileuserdata = target.getAttribute('data-id');

        //console.log(target.getAttribute('data-link'));
        let dataLink = target.getAttribute('data-link');

        const request = new XMLHttpRequest();
        request.open('POST', `${document.location.origin}/${dataLink}`);

        request.setRequestHeader('Content-type', 'application/json');
        const json = JSON.stringify(allData);
        request.send(json);

        const spinner = document.createElement('img');
        spinner.src = message.loading;
        boxdiv.append(spinner);

        request.addEventListener('load', () => {
            if (request.status === 200){
            //console.log(request.response);

            // statusMessage.textContent = message.success;
            statusMessage.innerHTML = request.response;
            spinner.remove();

            /*if(dataform == 'add') formreg.reset();
            else {
                //location.href = window.location.href;
            }*/


        } else {
            spinner.remove();
            statusMessage.textContent = message.failure;
        }
    });
    }
</script>