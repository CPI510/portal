<div class="row">
    <div class="card"></div>
    <?php if ($_POST['recom'] && $_GET['group'] && $_GET['form']){
        if($_POST['action'] == 2 ){
            $wpdb->update( 'p_proforma_recommendation',
                [ 'recom' => $_POST['recom'] ],
                [ 'user_id' => $_GET['uid'], 'expert_id' => get_current_user_id(), 'proforma_id' => substr($_GET['form'],0,-1), 'group_id' => $_GET['group'] ],
                [ '%s' ],
                [ '%d', '%d', '%d', '%d' ]
            );

        } else {
            $wpdb->insert('p_proforma_recommendation', [
                'user_id' => $_GET['uid'],
                'expert_id' => get_current_user_id(),
                'proforma_id' => substr($_GET['form'],0,-1),
                'group_id' => $_GET['group'],
                'recom' => $_POST['recom']
            ], ['%d', '%d', '%d', '%d', '%s']);
        }

        echo "<meta http-equiv='refresh' content='0;url=/proforma/?form=". substr($_GET['form'],0,-1) ."&group=$_GET[group]' />"; exit();
    } else { ?>
    <div class="col-lg-12">

        <div class="card">
            <div class="card-head style-primary">
                <header>Обоснование</header>
            </div>
            <div class="card-body">
                <p><b>ФИО: <?= nameUser($_GET['uid'], 5) ?></b></p>
                <?php
                $proformaDataUser = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_user_data WHERE user_id= %d AND proforma_id = %d AND group_id =%d", $_GET['uid'], substr($_GET['form'],0,-1), $_GET['group'] ));
                $proform_spr = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_spr WHERE proforma_id = %d", substr($_GET['form'],0,-1)));
                $finalData = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_proforma_user_result WHERE user_id= %d AND proforma_id = %d AND group_id =%d", $_GET['uid'], substr($_GET['form'],0,-1), $_GET['group'] ));
                ?>
                <p><b>Раздел А. Задания по суммативному оцениванию за раздел/сквозную тему</b><br>
                    <?php foreach ($proformaDataUser as $data) {
                        if($_GET['uid'] == 18){
                            // Для презентации
                            echo "Цели соответствуют разделу учебной программы. Критерии оценивания не соответствуют целям обучения и уровням мыслительных навыков. Задания №2, №3, №4 не соответствуют критериям оценивания. Задания №3 и №4 не соответствуют уровням мыслительных навыков. Задания соответствуют возрастным особенностям. Формулировка заданий понятна.  Формулировка заданий не содержит подсказок. Время, отведенное на выполнение заданий не указано. Дескрипторы описывают наблюдаемые и измеримые действия/шаги по выполнению заданий, но не поясняется, при каком условии ставится каждый балл, когда указываются несколько элементов. Количество баллов соответствует уровню сложности заданий.";
                            break;
                        }elseif($finalData->section_a == "Плагиат" && $proform_spr[$data->proforma_spr_id]->section_id == 1){
                            echo $data_text = "Согласно подпункту 4 пункта 10 раздела 2 Правил организации и проведения процедур суммативного оценивания портфолио слушателей на курсах повышения квалификации педагогов по образовательной программе «Разработка и экспертиза заданий для оценивания» по предметам в рамках обновления содержания среднего образования, утвержденных решением Правления АОО от 12.07.2018 года (протокол №41), с внесенными изменениями и дополнениями, утвержденными решением Правления АОО от 22.05.2019 года (протокол №18) в разделе А обнаружен плагиат.";
                            break;
                        }else{
                            if($data->data_value == 2 && $proform_spr[$data->proforma_spr_id]->section_id == 1)
                                echo $proform_spr[$data->proforma_spr_id]->name . ". ";
                            elseif($data->data_value == 1 && $proform_spr[$data->proforma_spr_id]->section_id == 1)
                                echo $proform_spr[$data->proforma_spr_id]->partially_name .". ";
                            elseif($data->data_value == 0 && $proform_spr[$data->proforma_spr_id]->section_id == 1)
                                echo $proform_spr[$data->proforma_spr_id]->negation_name;
                            }
                        }
                     ?>
                </p>
                <p>
                    <b>Раздел В. Задания по суммативному оцениванию за четверть</b><br>
                    <?php foreach ($proformaDataUser as $data) {
                        if($_GET['uid'] == 18){
                            // Для презентации
                            echo "Не все цели обучения соответствуют спецификации. В характеристике несколько целей обучения и количество баллов за разделы не соответствуют спецификации. Для заданий №8 и №9 неверно указан уровень мыслительных навыков. Содержание заданий №1, №2, №4, №6 не соответствует целям обучения. Время на выполнение заданий распределено рационально. В схеме выставления баллов неверно указаны ответы к заданию №5 и №6.   Количество баллов соответствует уровню сложности заданий. ";
                            break;
                        }elseif($finalData->section_b == "Плагиат" && $proform_spr[$data->proforma_spr_id]->section_id == 2){
                        echo $data_text2 = "Согласно подпункту 4 пункта 10 раздела 2 Правил организации и проведения процедур суммативного оценивания портфолио слушателей на курсах повышения квалификации педагогов по образовательной программе «Разработка и экспертиза заданий для оценивания» по предметам в рамках обновления содержания среднего образования, утвержденных решением Правления АОО от 12.07.2018 года (протокол №41), с внесенными изменениями и дополнениями, утвержденными решением Правления АОО от 22.05.2019 года (протокол №18) в разделе B обнаружен плагиат.";
                        break;
                    }else{
                        if($data->data_value == 2 && $proform_spr[$data->proforma_spr_id]->section_id == 2)
                            echo $proform_spr[$data->proforma_spr_id]->name . ". ";
                        elseif($data->data_value == 1 && $proform_spr[$data->proforma_spr_id]->section_id == 2)
                            echo $proform_spr[$data->proforma_spr_id]->partially_name .". ";
                        elseif($data->data_value == 0 && $proform_spr[$data->proforma_spr_id]->section_id == 2)
                            echo $proform_spr[$data->proforma_spr_id]->negation_name;
                        }
                    } ?>
                </p>
                <p>

                    <b>
                        Раздел А: <?= $finalData->section_a ?><br>
                        Раздел В: <?= $finalData->section_b ?><br>
                        Итоговый балл: <?= ($finalData->total == 0 || $finalData->total < 20) ? $finalData->decision : $finalData->total ?>
                    </b></br>
                </p>

            </div>
        </div>
        <form class="form-horizontal" method="POST" action="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>&uid=<?= $_GET['uid'] ?>" >
            <div class="card">
                <div class="card-head style-primary">
                    <header>Рекоммендация для слушателя <?= nameUser($_GET['uid'], 5) ?></header>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="Username5" class="col-sm-2 control-label">Рекоммендация</label>
                        <div class="col-sm-10">
                            <?php $recomData = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_proforma_recommendation WHERE user_id = %d AND group_id = %d AND proforma_id = %d"
                                , $_GET['uid']
                                , $_GET['group']
                                , substr($_GET['form'],0,-1)
                            ));
                            if (isset($recomData)) {
                                $dataText = $recomData->recom;
                                $action = "2";
                            } else {
                                $dataText = "";
                                $action = "1";
                            }
                            ?>
                            <textarea class="form-control" name="recom" maxlength="1000" rows="5"><?= $dataText ?></textarea><div class="form-control-line"></div>
                            <input type="hidden" name="action" value="<?= $action ?>">
                        </div>
                    </div>

                </div><!--end .card-body -->
                <div class="card-actionbar">
                    <div class="card-actionbar-row">
                        <button type="submit" class="btn btn-flat btn-primary ink-reaction">Сохранить</button>
                    </div>
                </div>
            </div><!--end .card -->
        </form>
    </div>
<?php } ?>
</div>