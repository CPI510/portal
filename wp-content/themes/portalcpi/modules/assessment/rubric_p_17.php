<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<?php
global $wpdb;

$_POST = json_decode(file_get_contents("php://input"), true);

if($_POST['fileuserdata']
    && $_POST['section_a_grade']
    && $_POST['section_b_grade']
    && $_POST['section_c_grade']
){
//    printAll($_POST); exit();
    if( $_POST['dataid'] == '' ){
        if($query = $wpdb->insert('p_assessment_rubric', [
            'group_id' => $_POST['id'],
            'listener_id' => $_POST['fileuserdata'],
            'create_user_id' => get_current_user_id(),
            'section_a_grade' => $_POST['section_a_grade'],
            'section_b_grade' => $_POST['section_b_grade'],
            'section_c_grade' => $_POST['section_c_grade'],
            'section_a_description' => $_POST['section_a_description'],
            'section_b_description' => $_POST['section_b_description'],
            'section_c_description' => $_POST['section_c_description'],
            'grading_solution' => $_POST['grading_solution'],
            'review' => $_POST['review']
        ],['%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s'])) {
            alertStatus('success',"Форма добавлена!");
            echo'<meta http-equiv="refresh" content="1;url=/groups/?z=group&id='.$_POST['id'].'" />';
        } else {
            if($wpdb->last_error !== '') :
                $wpdb->print_error();
            endif;
            alertStatus('danger',"Не добавлен!");

        }
    }elseif( $_POST['dataid'] != '' ){
        if($wpdb->update( 'p_assessment_rubric',
            [ 'section_a_grade' => $_POST['section_a_grade'],
                'section_b_grade' => $_POST['section_b_grade'],
                'section_c_grade' => $_POST['section_c_grade'],
                'section_a_description' => $_POST['section_a_description'],
                'section_b_description' => $_POST['section_b_description'],
                'section_c_description' => $_POST['section_c_description'],
                'grading_solution' => $_POST['grading_solution'],
                'review' => $_POST['review'],
                'date_update' => dateTime()
            ],
            [ 'group_id' => $_POST['id'], 'listener_id' => $_POST['fileuserdata'],'create_user_id' => get_current_user_id() ],
            [ '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s' ],
            [ '%d', '%d', '%d' ]
        )) {
            alertStatus('success',"Форма обновлена!");
            echo'<meta http-equiv="refresh" content="1;url=/groups/?z=group&id='.$_POST['id'].'" />';
        }  //else alertStatus('danger',"Код не обновлен!");


    }else{
        alertStatus('danger',"Нет данных!");
    }
    echo $wpdb->last_error;
//    echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['id'].'" />';
}else{

    $gropinfo = groupInfo($_GET['id']);

    $name_var = translateDir($_GET['id']);

    $gradeArr = array();
    $allGrade = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");
    foreach ($allGrade as $grade) {
        $gradeArr += [$grade->id => $grade->$name_var];
    }
    ?>

    <?php

    if($res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_POST['fileuserdata'], get_current_user_id() ))){

    }elseif(get_current_user_id() == $gropinfo->moderator_id){
        $res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_POST['fileuserdata'], $gropinfo->expert_id ));
        $res->id = "";
    }




    ?>

    <h3><?php
        echo "<div align='center'>Обоснование<br>".LISTENER_TEXT.": " . nameUser($_POST['fileuserdata'], 5) . "</div>";
        ?>
    </h3>
    <?php $grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade $moder_sql"); ?>


    <div class="card">
        <?php if(isset($res) && !empty($res->id )): ?>
            <a href="/export_to_word/?form=assessment_p_17&create_user_id=<?=$res->create_user_id?>&listener_id=<?=$res->listener_id?>&group=<?=$_GET['id']?>" class="btn btn-primary">Скачать обоснование</a>
        <?php else: ?>
            <?php if ($gropinfo->teamleader_id == get_current_user_id()) {
                $res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_POST['fileuserdata'], $gropinfo->expert_id ));
                $res->id = '';
            } ?>
            <a href="" class="btn btn-primary disabled"><?= ASSESSMENT_SECOND[0] ?></a>
            <!--<a href="" class="btn btn-primary disabled"><?= RUBRIC_DOWNLOAD ?></a>-->
        <?php endif; ?>
        <?php //printAll($res); echo $s;?>
        <div class="card-body">

            <table class="table table-bordered no-margin">
                <tr>
                    <td><?= ASSESSMENT_TRENER_17[0] ?></td>
                    <td width="400px">
                        <?php if($gropinfo->teamleader_id1 == get_current_user_id()): ?>
                            <b><?= $gradeArr[$res->section_a_grade] ?></b>
                            <input type="hidden" data-assessment name="section_a_grade" value="<?= $res->section_a_grade ?>">
                        <?php else: ?>
                            <select name="section_a_grade" class="form-control" required="" data-assessment aria-required="true" aria-invalid="false">
                                <option value="">&nbsp;</option>

                                <?php foreach ($grades as $grade): ?>
                                    <?php if( $grade->id == $res->section_a_grade ): ?>
                                        <option value="<?= $grade->id ?>" selected><?= $grade->$name_var ?></option>
                                    <?php else: ?>
                                        <option value="<?= $grade->id ?>"><?= $grade->$name_var ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-control-line"></div>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td><?= ASSESSMENT_TRENER_17[1] ?></td>
                    <td>
                        <?php if($gropinfo->teamleader_id1 == get_current_user_id()): ?>
                            <b><?= $gradeArr[$res->section_b_grade] ?></b>
                            <input type="hidden" data-assessment name="section_b_grade" value="<?= $res->section_b_grade ?>">
                        <?php else: ?>
                            <select data-assessment name="section_b_grade" class="form-control" required="" aria-required="true" aria-invalid="false">
                                <option value="">&nbsp;</option>
                                <?php foreach ($grades as $grade): ?>
                                    <?php if( $grade->id == $res->section_b_grade ): ?>
                                        <option value="<?= $grade->id ?>" selected><?= $grade->$name_var ?></option>
                                    <?php else: ?>
                                        <option value="<?= $grade->id ?>"><?= $grade->$name_var ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-control-line"></div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?= ASSESSMENT_TRENER_17[2] ?></td>
                    <td>
                        <?php if($gropinfo->teamleader_id1 == get_current_user_id()): ?>
                            <b><?= $gradeArr[$res->section_c_grade] ?></b>
                            <input type="hidden" data-assessment name="section_c_grade" value="<?= $res->section_c_grade ?>">
                        <?php else: ?>
                            <select data-assessment name="section_c_grade" class="form-control" required="" aria-required="true" aria-invalid="false">
                                <option value="">&nbsp;</option>
                                <?php foreach ($grades as $grade): ?>
                                    <?php if( $grade->id == $res->section_c_grade ): ?>
                                        <option value="<?= $grade->id ?>" selected><?= $grade->$name_var ?></option>
                                    <?php else: ?>
                                        <option value="<?= $grade->id ?>"><?= $grade->$name_var ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-control-line"></div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?= ASSESSMENT_TRENER_17[3] ?></td>
                    <td>
                        <?php if($gropinfo->teamleader_id1 == get_current_user_id()): ?>
                            <b><?= $gradeArr[$res->section_d_grade] ?></b>
                            <input type="hidden" data-assessment name="section_d_grade" value="<?= $res->section_d_grade ?>">
                        <?php else: ?>
                            <select data-assessment name="section_d_grade" class="form-control" required="" aria-required="true" aria-invalid="false">
                                <option value="">&nbsp;</option>
                                <?php foreach ($grades as $grade): ?>
                                    <?php if( $grade->id == $res->section_d_grade ): ?>
                                        <option value="<?= $grade->id ?>" selected><?= $grade->$name_var ?></option>
                                    <?php else: ?>
                                        <option value="<?= $grade->id ?>"><?= $grade->$name_var ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-control-line"></div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?= ASSESSMENT_TRENER_17[4] ?></td>
                    <td>
                        <?php if($gropinfo->teamleader_id1 == get_current_user_id()): ?>
                            <b><?= $gradeArr[$res->section_e_grade] ?></b>
                            <input type="hidden" data-assessment name="section_e_grade" value="<?= $res->section_e_grade ?>">
                        <?php else: ?>
                            <select data-assessment name="section_e_grade" class="form-control" required="" aria-required="true" aria-invalid="false">
                                <option value="">&nbsp;</option>
                                <?php foreach ($grades as $grade): ?>
                                    <?php if( $grade->id == $res->section_e_grade ): ?>
                                        <option value="<?= $grade->id ?>" selected><?= $grade->$name_var ?></option>
                                    <?php else: ?>
                                        <option value="<?= $grade->id ?>"><?= $grade->$name_var ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-control-line"></div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

        </div><!--end .card-body -->

    </div><!--end .card -->
    <div class="card panel">
        <div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion1" data-target="#accordion1-1" aria-expanded="false">
            <header>Эффективный менеджмент в управлении дошкольной организацией (Заготовки)</header>
            <div class="tools">
                <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
            </div>
        </div>
        <div id="accordion1-1" class="collapse" aria-expanded="false" style="height: 0px;">
            <div class="card-body">

                <table cellspacing="0" style="border-collapse:collapse">
                    <tbody>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; height:26px; vertical-align:middle; white-space:nowrap"><strong>№</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:middle; white-space:normal; width:327px"><strong>Описание&nbsp;</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:middle; white-space:normal; width:292px"><strong>неудовлетворительно</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:middle; white-space:normal; width:341px"><strong>пороговый уровень</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:middle; white-space:normal; width:332px"><strong>удовлетворительно</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:middle; white-space:normal; width:352px"><strong>хорошо</strong></td>
                    </tr>
                    <tr>
                        <td rowspan="4" style="border-bottom:none; border-left:1px solid black; border-right:1px solid black; border-top:none; height:133px; text-align:center; vertical-align:top; white-space:nowrap">Критерий 1</td>
                        <td rowspan="4" style="border-bottom:none; border-left:1px solid black; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:327px"><strong>определение потребностей для осуществления изменений в управление дошкольной организацией</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:292px"><strong>не определены потребности для внесения изменений, отсутствует анализ данных</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:341px"><strong>частично определены потребности для внесения изменений, представлен неполный анализ данных</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:332px"><strong>определены потребности для внесения изменений, частично основанных на анализе данных</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:352px"><strong>определены потребности для внесения изменений, основанных на анализе данных</strong></td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:33px; text-align:left; vertical-align:top; white-space:normal; width:292px; ">
                            <div id="n1" style="cursor: pointer" > Не все вопросы анкетирования направлены на диагностику потребностей. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">
                            <div id="n2" style="cursor: pointer"> Представлены доказательства опроса педагогов, родителей, детей. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n3" style="cursor: pointer"> Определены потребности для внесения изменений, частично основанных на анализе данных.&nbsp;</div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:48px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n4" style="cursor: pointer"> Перечислены общие потребности, отсутствует связь их с&nbsp; результатами анкетирования. </div>
                        </td>
                        <td style="background-color:yellow; border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">
                            <div id="n5" style="cursor: pointer"> Определены потребности для внесения изменений, но для родителей они не являются взаимосвязанными с содержанием опроса, не вытекают из результатов анализа.&nbsp; </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n6" style="cursor: pointer"> Приводятся результаты диагностики за текущий год&nbsp; </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom:none; border-left:none; border-right:1px solid black; border-top:none; height:20px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n30" style="cursor: pointer"> Потребности не определены. </div>
                        </td>
                        <td style="border-bottom:none; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">&nbsp;</td>
                        <td style="border-bottom:none; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n31" style="cursor: pointer"> Прогнозируются ожидаемые результаты. </div>
                        </td>
                        <td style="border-bottom:none; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td rowspan="3" style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; height:145px; text-align:center; vertical-align:top; white-space:nowrap">Критерий 2</td>
                        <td rowspan="3" style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; text-align:left; vertical-align:top; white-space:normal; width:327px"><strong>определение миссии и приоритетов для внесения изменений в управление дошкольной организацией</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; text-align:center; vertical-align:top; white-space:normal; width:292px"><strong>&ndash; не определены миссия и приоритеты для осушествления изменений;</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; text-align:center; vertical-align:top; white-space:normal; width:341px"><strong>миссия и приоритеты для осушествления изменений не соотносятся с потребностями;</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; text-align:center; vertical-align:top; white-space:normal; width:332px"><strong>миссия, приоритеты для осушествления изменений частично соотносятся с потребностями;</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; text-align:center; vertical-align:top; white-space:normal; width:352px"><strong>миссия и приоритеты для осушествления изменений соотносятся с потребностями;</strong></td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:48px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n7" style="cursor: pointer"> Приводятся вопросы анкетирования, которые не позволяют диагностировать потребности детей, родителей.&nbsp; </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">
                            <div id="n8" style="cursor: pointer"> Миссия и приоритеты для осушествления изменений не соотносятся с потребностями участников образовательного процесса.&nbsp; </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n9" style="cursor: pointer"> Приоритеты сформулированы в виде задач, которые соотносятся с потребностью.&nbsp; </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">
                            <div id="n10" style="cursor: pointer"> Миссия и приоритеты для осушествления изменений соотносятся с потребностями в создании здоровьесберегающей среды. </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:64px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n11" style="cursor: pointer"> SWOT-анализ содержит противоречивые данные. Вывод поверхностный, не объясняется его взаимосвязь с результатами анкетирования. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">&nbsp;</td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n12" style="cursor: pointer"> Миссия сформулирована обобщенно, частично соотносится с потребностью, направлена на создание модели, а не на воспитание и развитие детей, создание соответствующей среды для внедрения изменений. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td rowspan="3" style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; height:161px; text-align:center; vertical-align:top; white-space:nowrap">Критерий 3</td>
                        <td rowspan="3" style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:327px"><strong>способы поддержки коллег в действии (коучинг и менторинг) для внедрения изменений</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:292px"><strong>не представлены способы поддержки коллег или не соответствуют миссии, приоритетам</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:341px"><strong>способы поддержки коллег не соответствуют миссии и приоритетам</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:332px"><strong>способы поддержки коллег частично соответствуют миссии и приоритетам</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:352px"><strong>способы поддержки коллег соответствуют миссии и приоритетам</strong></td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:48px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n13" style="cursor: pointer"> Названы мероприятия, ожидаемые результаты сформулированы нечетко.&nbsp; </div>
                        </td>
                        <td style="background-color:yellow; border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">
                            <div id="n14" style="cursor: pointer"> Способы поддержки коллег через коучинг приведены, но содержание менторинга не связано&nbsp; с миссией и приоритетами. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n15" style="cursor: pointer"> Представлены способы поддержки коллег, которые частично соответствуют миссии и приоритетам развития ДО.&nbsp; </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:none; border-left:none; border-right:1px solid black; border-top:none; height:80px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n16" style="cursor: pointer"> Запланированные коучинги и менторинг таковыми не являются: менторинг не предусматривает поддержку конкретного коллеги во внедрении изменений, а коучинги по своей тематике не направлены на поддержку изменений.&nbsp; </div>
                        </td>
                        <td style="border-bottom:none; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">&nbsp;</td>
                        <td style="border-bottom:none; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n17" style="cursor: pointer"> Объясняется, как будет оказываться поддержка коллегам на основе коучинга и менторинга. </div>
                        </td>
                        <td style="border-bottom:none; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td rowspan="4" style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; height:242px; text-align:center; vertical-align:top; white-space:nowrap">Критерий 4</td>
                        <td rowspan="4" style="border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black; border-top:1px solid black; text-align:left; vertical-align:top; white-space:normal; width:327px"><strong>способы вовлечения педагогов, воспитанников, их родителей и иных законных представителей в вопросы развития дошкольной организации</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; text-align:center; vertical-align:top; white-space:normal; width:292px"><strong>не определены способы вовлечения педагогов, детей и их родителей в развитие дошкольной организации;&nbsp;&nbsp;</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; text-align:center; vertical-align:top; white-space:normal; width:341px"><strong>способы вовлечения педагогов, детей и их родителей в развитие дошкольной организации не соответствуют миссии и приоритетам;</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; text-align:center; vertical-align:top; white-space:normal; width:332px"><strong>способы вовлечения педагогов, детей и их родителей в развитие дошкольной организации частично соответствуют миссии и приоритетам;</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:1px solid black; text-align:center; vertical-align:top; white-space:normal; width:352px"><strong>способы вовлечения педагогов, детей и их родителей в развитие дошкольной организации соответствуют миссии и приоритетам;</strong></td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:80px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n18" style="cursor: pointer"> Названы способы вовлечения педагогов.&nbsp; </div>
                        </td>
                        <td style="background-color:yellow; border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">
                            <div id="n19" style="cursor: pointer"> Способы вовлечения педагогов, детей и их родителей в развитие дошкольной организации названы, но они не соответствуют миссии и приоритетам. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n20" style="cursor: pointer"> Перечислены мероприятия, способствующие вовлечению педагогов, детей и их родителей в развитие дошкольной организации, которые частично соответствуют миссии и приоритетам. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">
                            <div id="n21" style="cursor: pointer"> Перечислены разнообразные мероприятия, способствующие вовлечению педагогов, детей и их родителей в развитие дошкольной организации в соответствии с миссией и приоритетами. </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:48px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n22" style="cursor: pointer"> Способы вовлечения детей не конкретны, не соответствуют выявленным приоритетам.&nbsp; </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">
                            <div id="n23" style="cursor: pointer"> Отсутствуют пояснения, какое участие в этих формах будут принимать все участники педагогического процесса </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n24" style="cursor: pointer"> Вывод является неконкретным, ожидаемые результаты неизмеримыми. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:64px; vertical-align:top; white-space:nowrap">
                            <div id="n25" style="cursor: pointer"> Способы вовлечения родителей не названы. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">
                            <div id="n26" style="cursor: pointer"> Отсутствуют пояснения как это позволит им продвинуться в направлении миссии и приоритетов.&nbsp; </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">
                            <div id="n27" style="cursor: pointer"> Названы способы вовлечения педагогов. Способы вовлечения детей не конкретны, не соответствуют выявленным приоритетам. Способы вовлечения родителей не названы. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td rowspan="2" style="border-bottom:.7px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; height:112px; text-align:center; vertical-align:top; white-space:nowrap">Критерий 5</td>
                        <td rowspan="2" style="border-bottom:.7px solid black; border-left:1px solid black; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:327px"><strong>оценка эффективности изменений и выводы по планированию изменений</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:292px"><strong>не оценивается эффективность изменений, отсутствуют выводы.</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:341px"><strong>частично оценивается эффективность изменений, представлены выводы, не основанные на оценке эффективности планирования изменений.</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:332px"><strong>оценивается эффективность изменений, представлены выводы, частично основанные на оценке эффективности планирования изменений.</strong></td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:center; vertical-align:top; white-space:normal; width:352px"><strong>оценивается эффективность изменений, представлены выводы на основе оценки эффективности планирования изменений.</strong></td>
                    </tr>
                    <tr>
                        <td style="background-color:yellow; border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; height:64px; text-align:left; vertical-align:top; white-space:normal; width:292px">
                            <div id="n28" style="cursor: pointer"> Приводятся результаты опроса, но они не связаны с оценкой эффективности изменений. Сделаны обобщенные выводы, которые частично связаны с миссией и приоритетами. </div>
                        </td>
                        <td style="background-color:yellow; border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:341px">
                            <div id="n29" style="cursor: pointer"> Утверждается, что изменения будут эффективными, но не представлены выводы, основанные на оценке эффективности планирования изменений. </div>
                        </td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:332px">&nbsp;</td>
                        <td style="border-bottom:1px solid black; border-left:none; border-right:1px solid black; border-top:none; text-align:left; vertical-align:top; white-space:normal; width:352px">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4><?= RATIONALE ?></h4>
            <div class="form-group">
                <label for="textarea3"></label>
                <textarea data-assessment name="review" id="textarea3" class="form-control" rows="8" required="" aria-required="true" aria-invalid="false" placeholder="<?= ($res->review) ? "" : NOT_FILLED ?>"><?= $res->review ?></textarea>
            </div>
        </div><!--end .card-body -->

    </div><!--end .card -->

    <div class="boxcoding"></div>
    <div class="card-actionbar">
        <div class="card-actionbar-row">
            <input type="hidden" data-assessment name="grading_solution" value="<?= $res->grading_solution ?>">
            <button type="submit" class="btn btn-success" ><?= SAVE_TEXT ?></button>
            <input type="hidden" name="link" class="form-control" data-assessment value="assessment/?z=rubric_p_6">
            <input type="hidden" name="id" class="form-control" data-assessment value="<?=$_GET['id']?>">
            <input type="hidden" name="dataid" class="form-control" data-assessment value="<?=$res->id?>">
        </div>
    </div><!--end .card-actionbar -->

    <?php

//    unset($_POST);
//    printAll($_POST);

} ?>


