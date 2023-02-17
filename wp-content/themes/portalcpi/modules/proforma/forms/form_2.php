<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="<?= bloginfo('template_url') ?>/assets/js/core/countDown.js"></script>
<div class="row">
    <div class="card">
        <?php
            $groupInfo  = groupInfo($_GET['group']);
            $getAccess = getAccess(get_current_user_id());
            $name_var = translateDir($_GET['group']);
        ?>
        <div class="card-body">
            <b>Номер группы:</b> <?=$groupInfo->number_group?><br>
            <b>Программа:</b> <?=$groupInfo->p_name?><br>
            <b>Тренер:</b> <?= $groupInfo->surname ?> <?= $groupInfo->name ?> <?= $groupInfo->patronymic ?></a><br>
            <b>Язык обучения:</b> <?= $groupInfo->lang_name_ru ?> <br>

            <h3 class="text-primary-dark">Осталось времени до окончания работы: <span id="display"></span></h3>
<!--            <script>-->
<!--                const display--><?php //= $q ?><!-- = document.querySelector('#display--><?php //= $q ?><!--');-->
<!--                countDown('--><?php //= $time_end ?><!--', display--><?php //= $q ?><!--);-->
<!--            </script>-->



        </div>
    </div>
</div>
<div class="row">
    <div class="card">
        <div class="card-body">
            <form id="form" method="post" action="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>">
                <table class="table table-bordered">
                    <tr>
                        <th rowspan="2">№</th>
                        <th rowspan="2">ФИО слушателей</th>

                        <th colspan="3"><div align="center"><?= PROFORMA[6] ?></div></th>
                        <th colspan="3"><div align="center"><?= PROFORMA[7] ?></div></th>
                        <th colspan="3"><div align="center"><?= PROFORMA[7] ?></div></th>


                        <th rowspan="2">
                            <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);">Итого</span>
                        </th>

                        <th rowspan="2">
                            <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);">Решение</span>
                        </th>
                    </tr>
                    <tr>
                        <?php $proformaSpr = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_spr WHERE proforma_id = %d", $_GET['form'])); ?>
                        <?php foreach ($proformaSpr as $data): ?>
                            <th>
                                                        <span>
                                                            <?= 'K'. ++$i;  ?>
                                                            <i class="fa fa-info-circle fa-fw text-info" data-toggle="tooltip" data-placement="right" data-original-title="<?= $data->$name_var ?>" style="cursor: pointer"></i>
                                                        </span>
                            </th>
                            <?php if($data->section_id == 1) ++$d; else ++$r; endforeach; ?>
                    </tr>
                    <?php if($getAccess->access == 1): ?>
                        <tr>
                            <td colspan="22">
                                <div class="btn-group dropup align-items-end" >
                                    <button type="button" class="btn ink-reaction btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <?php if ($_GET['filtr'] == 1){
                                            $filtr_text = "(Зачет)";
                                            $sql_filtr = "AND p.decision = 'Зачет'";
                                        } elseif ($_GET['filtr'] == 2){
                                            $filtr_text = "(Незачет)";
                                            $sql_filtr = "AND p.decision = 'Незачет'";
                                        } elseif ($_GET['filtr'] == 3){
                                            $filtr_text = "(Плагиат)";
                                            $sql_filtr = "AND (p.section_a = 'Плагиат' OR p.section_b = 'Плагиат')";
                                        } else {
                                            $sql_filtr = "";
                                        } ?>
                                        Фильтр <?= $filtr_text ?><i class="fa fa-caret-up text-default-light"></i>
                                    </button>
                                    <ul class="dropdown-menu animation-expand" role="menu">
                                        <li><?php if($_GET['filtr'] == 1): ?><a href="" class="btn btn-info btn-xs active">Зачет</a><?php else: ?><a href="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&filtr=1" class="btn btn-default btn-xs">Зачет</a><?php endif; ?></li>
                                        <li><?php if($_GET['filtr'] == 2): ?><a href="" class="btn btn-info btn-xs active">Незачет</a><?php else: ?><a href="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&filtr=2" class="btn btn-default btn-xs">Незачет</a><?php endif; ?></li>
                                        <li><?php if($_GET['filtr'] == 3): ?><a href="" class="btn btn-info btn-xs active">Плагиат</a><?php else: ?><a href="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&filtr=3" class="btn btn-default btn-xs">Плагиат</a><?php endif; ?></li>
                                        <li><a href="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&filtr=reject" class="btn btn-default btn-xs">Сбросить</a></li>
                                        <li class="divider"></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php for($y = 1; $y <= 3; $y++): // Это для отображение данныех тренера эксперта и модератора ?>
                         <?php if($y == 1){ //Все для эксперта
                            $link_choice = "&trener_id={$groupInfo->trener_id}";
                            $fiels_text = "AND p.trener_id = %d";
                            $fiels_id = $groupInfo->trener_id;
                            $part_text = "тренера";
                            if($groupInfo->trener_id == get_current_user_id()) {
                                $y = 4;
                            }elseif($groupInfo->expert_id == get_current_user_id()){
                                continue;
                            }
                        } else if ($y == 2) {
                            $link_choice = "&expert_id={$groupInfo->expert_id}";
                            $fiels_text = "AND p.expert_id = %d";
                            $fiels_id = $groupInfo->expert_id;
                            $part_text = "эксперта";
                            if($groupInfo->expert_id == get_current_user_id()) {
                                $y = 4;
                            }elseif($groupInfo->moderator_id == get_current_user_id()){
                                continue;
                            }
                        }
                        else{ //Все для модератора
                            $link_choice = "&moderator_id={$groupInfo->moderator_id}";
                            $fiels_text = "AND p.moderator_id = %d";
                            $fiels_id = $groupInfo->moderator_id;
                            $part_text = "модератора";
                        } ?>
                        <tr>
                            <th colspan="22">Оценка <?= $part_text ?></th>
                        </tr>
                        <?php
                        if($groupInfo->teamleader_id == get_current_user_id()){ // Показываем данные для тимлидера
                            $usersField = $wpdb->get_results($s=$wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b, p.expert_id, p.moderator_id, p.id proforma_result_id
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user 
                                WHERE g.id_group = %d AND p.group_id = %d $fiels_text AND p.total < 20", $_GET['group'], $_GET['group'], $fiels_id  ));

                            if($_GET['teamleader_choice'] == 1
                                && $_GET['form']
                                && $_GET['group']
                                && $_GET['user_id']
                                && $_GET['proforma_result_id']
                            ){
                                if($_GET['expert_id']) $messageText .= "<br><br> Выбрана оценка эксперта, пользователя ".nameUser($_GET['user_id'],5);
                                if($_GET['moderator_id']) $messageText .= "<br><br> Выбрана оценка модератора, пользователя ".nameUser($_GET['user_id'],5);

                                //printAll($_GET);
                                if($wpdb->insert('p_proforma_teamleader_choice', [
                                    'proforma_id' => $_GET['form'],
                                    'group_id' => $_GET['group'],
                                    'user_id' => $_GET['user_id'],
                                    'teamleader_id' => get_current_user_id(),
                                    'expert_id' => $_GET['expert_id'],
                                    'moderator_id' => $_GET['moderator_id'],
                                    'proforma_result_id' => $_GET['proforma_result_id']
                                ], ['%d','%d','%d','%d','%d','%d','%d'])){
                                    wp_mail(nameUser($groupInfo->admin_id,6), 'Портал ЦПИ: Тимлидер выполнил оценку', $messageText, $headers, $attachments);
                                    echo "<meta http-equiv='refresh' content='0;url=/proforma/?form=$_GET[form]&group=$_GET[group]' />"; exit();
                                }elseif($wpdb->update( 'p_proforma_teamleader_choice',
                                    [ 'moderator_id' => $_GET['moderator_id'],'expert_id' => $_GET['expert_id'], 'date_update' => dateTime(), 'proforma_result_id' => $_GET['proforma_result_id'] ],
                                    [ 'proforma_id' => $_GET['form'], 'group_id' => $_GET['group'], 'user_id' => $_GET['user_id'], 'teamleader_id' => get_current_user_id() ],
                                    [ '%d', '%d', '%s', '%d' ],
                                    [ '%d', '%d', '%d', '%d' ]
                                )){
                                    wp_mail(nameUser($groupInfo->admin_id,6), 'Портал ЦПИ: Тимлидер выполнил оценку', $messageText, $headers, $attachments);
                                    echo "<meta http-equiv='refresh' content='0;url=/proforma/?form=$_GET[form]&group=$_GET[group]' />"; exit();
                                }
                            }
                        }else{
                            $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b, p.trener_id, p.expert_id, p.moderator_id, p.id proforma_result_id
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user 
                                WHERE g.id_group = %d AND p.group_id = %d $fiels_text $sql_filtr", $_GET['group'], $_GET['group'], $fiels_id)); // Это нужно для отображения пользователей этой группы

                            if(!$usersField && $groupInfo->expert_id == get_current_user_id()){ // Если эксперт еще не поставил оценки
                                $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email
                                    FROM p_groups_users g
                                    LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                    WHERE g.id_group = %d", $_GET['group']));
                            }elseif(!$usersField && $groupInfo->moderator_id == get_current_user_id()){ // Если модератор еще не поставил оценки, берется данные эксперта
                                $usersField = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, g.date_reg, u.user_id, u.surname, u.name, u.patronymic, u.email, p.total, p.decision, p.section_a, p.section_b, p.expert_id, p.moderator_id, p.id proforma_result_id
                                FROM p_groups_users g
                                LEFT OUTER JOIN p_user_fields u ON u.user_id = g.id_user 
                                LEFT OUTER JOIN p_proforma_user_result p ON p.user_id = g.id_user 
                                WHERE g.id_group = %d AND p.group_id = %d AND expert_id = %d AND p.total < 20", $_GET['group'], $_GET['group'], $groupInfo->expert_id));
                            }
                        }

                        //echo $s;//printAll($usersField);
                        ?>

                        <?php foreach ($usersField as $user): ?>
                            <?php $user->decision = ($user->decision == 'Незачет') ? ASSESSMENT_SHEET[8] : ASSESSMENT_SHEET[7]; ?>
                            <tr>
                                <td><?= ++$i; ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn ink-reaction btn-icon-toggle btn-primary" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-chevron-down"></i></button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li class="divider"></li>
                                            <li><a href="/proforma/?form=<?= $_GET['form'] ?>r&group=<?= $_GET['group'] ?>&uid=<?= $user->user_id ?>" ><i class="md md-exit-to-app text-info"></i> Реккоммендация</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/export_to_word/?form=<?= $_GET['form'] ?>a&group=<?= $_GET['group'] ?>&uid=<?= $user->user_id ?>" ><i class="md md-exit-to-app text-info"></i> Обоснование</a></li>
                                            <li class="divider"></li>
                                            <li><a href="#" id="fileu" data-id="<?= $user->user_id ?>" data-toggle="modal" data-target="#Modal">
                                                    <i class="md md-exit-to-app text-info"></i>
                                                    Файлы пользователя <?php  echo $num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM p_file WHERE group_id = %d AND user_id = %d", $_GET['group'], $user->user_id)) ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <?= $user->surname ?> <?= $user->$name_var ?> <?= $user->patronymic // ФИО Участика ?>
                                    <?php
                                    $choiceProforma = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_proforma_teamleader_choice WHERE proforma_result_id = %d",$user->proforma_result_id));
                                    if ( $choiceProforma->proforma_result_id == $user->proforma_result_id && ( $getAccess->access == 1 || $groupInfo->teamleader_id == get_current_user_id() ) ){ // Проверяем если есть ли запись в базе с таким выбором и отображаем для тимлидера и админа
                                        echo "<span class='badge'>Эта оценка была выбрана тимлидером!</span>";
                                    }elseif($groupInfo->teamleader_id == get_current_user_id()){
                                        echo "<a href='/proforma/?form={$_GET['form']}&group={$_GET['group']}&user_id={$user->user_id}{$link_choice}&teamleader_choice=1&proforma_result_id={$user->proforma_result_id}' 
                                            class='btn btn-success btn-xs' id='timer'>Выбрать оценку</a>";
                                    } ?>
                                </td>
                                <?php $proformaDataUser = $wpdb->get_results($s=$wpdb->prepare("SELECT p.id, p.user_id, p.proforma_id, p.proforma_spr_id, p.group_id, p.datetime, p.data_value, p.datetime_update, p.expert_id, p.moderator_id FROM p_proforma_user_data p WHERE p.user_id= %d AND p.proforma_id = %d AND p.group_id =%d $fiels_text"
                                    , $user->user_id, $_GET['form'], $_GET['group'], $fiels_id ));
                                if(!$proformaDataUser){
                                    $proformaDataUser = $wpdb->get_results($s=$wpdb->prepare("SELECT p.id, p.user_id, p.proforma_id, p.proforma_spr_id, p.group_id, p.datetime, p.data_value, p.datetime_update, p.expert_id, p.moderator_id FROM p_proforma_user_data p WHERE p.user_id= %d AND p.proforma_id = %d AND p.group_id =%d AND expert_id"
                                        , $user->user_id, $_GET['form'], $_GET['group'], $groupInfo->expert_id ));
                                    $action_moderator = 1;
                                }?>
                                <?php  $q=0; //printAll($proformaDataUser);  ?>

                                <?php foreach ($proformaSpr as $data): ?>
                                    <?php  //echo "{$proformaDataUser[$q]->proforma_spr_id} == {$data->id}<br>";
                                    if ($proformaDataUser[$q]->proforma_spr_id == $data->id) {
                                        $action = ($action_moderator == 1) ? "1" : "2";
                                    } else {
                                        $action =  "1";
                                    }

                                    //$key = array_search($user->user_id, array_column($finalData, 'user_id'));
                                    ?>
                                    <td>
                                        <?php if($groupInfo->expert_id == get_current_user_id() || $groupInfo->moderator_id == get_current_user_id()): ?>
                                            <select name="item[<?= $user->user_id ?>][<?= $data->id ?>][<?= $data->section_id ?>][<?= $action ?>]" class="form-control" required>
                                                <option></option>
                                                <?php if(($proformaDataUser[$q]->data_value == 3 || ($data->section_id == 1 && $user->section_a == "Плагиат") || ($data->section_id == 2 && $user->section_b == "Плагиат"))){
                                                    echo '<option value="0">0</option>
                                                      <option value="1">1</option>
                                                      <option value="2">2</option>
                                                      <option value="3" selected>Плагиат</option>  
                                                      ';
                                                }else{

                                                    ?>
                                                    <option value="0" <?= (isset($proformaDataUser[$q]->data_value) && $proformaDataUser[$q]->data_value == 0) ? "selected" : "" ?>>0</option>
                                                    <option value="1" <?= ($proformaDataUser[$q]->data_value == 1) ? "selected" : "" ?>>1</option>
                                                    <option value="2" <?= ($proformaDataUser[$q]->data_value == 2) ? "selected" : "" ?>>2</option>
                                                    <option value="3" >Плагиат</option>
                                                <?php } ?>
                                            </select>
                                        <?php else:?>
                                            <?= ($proformaDataUser[$q]->data_value == 3) ? "Плагиат" : $proformaDataUser[$q]->data_value ?>
                                        <?php endif; ?>

                                    </td>
                                    <?php $q++; ?>
                                <?php endforeach; ?>
                                <td><?= $user->total ?></td>
                                <td><?= $user->decision ?></td>
                            </tr>
                        <?php  endforeach; ?>
                    <?php endfor; ?>
                </table>
            </form>
        </div>
    </div>
</div>