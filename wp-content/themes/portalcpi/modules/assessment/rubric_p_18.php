<?php
global $wpdb;

$_POST = json_decode(file_get_contents("php://input"), true);

if($_POST['fileuserdata']) {
    if ( $_POST['dataid'] ){
        if($wpdb->update( 'p_proforma_user_result',
            [   'review' => $_POST['review'],
                'date_update' => dateTime()
            ],
            [ 'group_id' => $_POST['id'], 'user_id' => $_POST['fileuserdata'], 'id' => $_POST['dataid'] ],
            [ '%s', '%s' ],
            [ '%d', '%d' ]
            )) {
            alertStatus('success',"Форма обновлена!");
            echo'<meta http-equiv="refresh" content="1;url=/groups/?z=group&id='.$_POST['id'].'" />';
            exit();
        }  else alertStatus('danger',"Код не обновлен!");
    }
//    if($wpdb->update( 'p_proforma_user_result',
//        [   'review' => $_POST['review'],
//            'date_update' => dateTime()
//        ],
//        [ 'group_id' => $_POST['id'], 'user_id' => $_POST['fileuserdata'] ],
//        [ '%d', '%s' ],
//        [ '%d', '%d' ]
//    )) {
//        alertStatus('success',"Форма обновлена!");
//        echo'<meta http-equiv="refresh" content="1;url=/groups/?z=group&id='.$_POST['id'].'" />';
//    }  else alertStatus('danger',"Код не обновлен!");


//    printAll($_POST); exit();
//    if( $_POST['dataid'] == '' ){
//        if($query = $wpdb->insert('p_assessment_rubric', [
//            'group_id' => $_POST['id'],
//            'listener_id' => $_POST['fileuserdata'],
//            'create_user_id' => get_current_user_id(),
//            'section_a_grade' => $_POST['section_a_grade'],
//            'section_b_grade' => $_POST['section_b_grade'],
//            'section_c_grade' => $_POST['section_c_grade'],
//            'section_a_description' => $_POST['section_a_description'],
//            'section_b_description' => $_POST['section_b_description'],
//            'section_c_description' => $_POST['section_c_description'],
//            'grading_solution' => $_POST['grading_solution'],
//            'review' => $_POST['review']
//        ],['%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s'])) {
//            alertStatus('success',"Форма добавлена!");
//            echo'<meta http-equiv="refresh" content="1;url=/groups/?z=group&id='.$_POST['id'].'" />';
//        } else {
//            if($wpdb->last_error !== '') :
//                $wpdb->print_error();
//            endif;
//            alertStatus('danger',"Не добавлен!");
//
//        }
//    }elseif( $_POST['dataid'] != '' ){
//        if($wpdb->update( 'p_proforma_user_result',
//            [   'review' => $_POST['review'],
//                'date_update' => dateTime()
//            ],
//            [ 'group_id' => $_POST['id'], 'user_id' => $_POST['fileuserdata'] ],
//            [ '%d', '%s' ],
//            [ '%d', '%d' ]
//        )) {
//            alertStatus('success',"Форма обновлена!");
//            echo'<meta http-equiv="refresh" content="1;url=/groups/?z=group&id='.$_POST['id'].'" />';
//        }  //else alertStatus('danger',"Код не обновлен!");
//
//
//    }else{
//        alertStatus('danger',"Нет данных!");
//    }
    echo $wpdb->last_error;
//    echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['id'].'" />';
}

    $groupInfo = groupInfo($_GET['id']);

    $name_var = translateDir($_GET['id']);

    ?>

    <?php

    if(get_current_user_id() == $groupInfo->expert_id ){
        $res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_proforma_user_result WHERE group_id = %d AND user_id = %d AND expert_id = %d", $_GET['id'], $_POST['fileuserdata'], get_current_user_id()));
        $field_name = 'expert_id';
    }elseif(get_current_user_id() == $groupInfo->moderator_id){
        $res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_proforma_user_result WHERE group_id = %d AND user_id = %d", $_GET['id'], $_POST['fileuserdata']));
        $res->id = "";
        $field_name = 'moderator_id';
    }

    ?>


    <h3><?php
        echo "<div align='center'>".ASSESSMENT_SECOND[2]." <br>".LISTENER_TEXT.": " . nameUser($_POST['fileuserdata'], 5) . "</div>";
        ?>
    </h3>

    <div class="card">
        <?php if(isset($res) && !empty($res->id )):?>

            <?php if(!empty($res->review)) : ?>
                <a href="/export_to_word/?form=<?= $res->proforma_id?>_review&group=<?= $res->group_id ?>&user_id=<?= $res->user_id?>" class="btn btn-primary"><?= ASSESSMENT_SECOND[0] ?> </a>
            <?php else : ?>
                <a class="btn btn-danger" disabled><?= ASSESSMENT_SECOND[0] ?></a>
            <?php endif; ?>

        <?php endif; ?>



        <?php //printAll($res); echo $s;?>


        <form novalidate id="form" method="post" action="/proforma/?form=<?= $_GET['form'] ?>&group=<?= $_GET['group'] ?>">
            <table class="table table-bordered">
                <tr>
                    <th rowspan="2">№</th>
                    <th rowspan="2">ФИО слушателей</th>

                    <th colspan="3"><div align="center">Цели</div></th>
                    <th colspan="3"><div align="center">Методы обучения</div></th>
                    <th colspan="3"><div align="center">Задания</div></th>


                    <th rowspan="2">
                        <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);">Итог</span>
                    </th>

                    <th rowspan="2">
                        <span STYLE="writing-mode: vertical-lr; -ms-writing-mode: tb-rl; transform: rotate(180deg);">Решение</span>
                    </th>
                </tr>
                <tr>
                    <?php $proformaSpr = $wpdb->get_results($wpdb->prepare("SELECT * FROM p_proforma_spr WHERE proforma_id = 2")); ?>

                    <?php foreach ($proformaSpr as $data): ?>
                        <th>
                                    <span> <?= 'K'. ++$i;  ?>
                                        <i class="fa fa-info-circle fa-fw text-info" data-toggle="tooltip" data-placement="right" data-original-title="<?= $data->$name_var ?>" style="cursor: pointer;"></i>
                                    </span>
                        </th>
                    <?php endforeach; ?>
                </tr>

                <?php for($y = 1; $y <= 3; $y++): // Это для отображение данныех тренера эксперта и модератора ?>
                    <?php if($y == 1){ //Все для тренера
                        $link_choice = "&trener_id={$groupInfo->trener_id}";
                        $fiels_text = "AND d.trener_id = %d";
                        $fiels_id = $groupInfo->trener_id;
                        $part_text = "тренера";
                        if($groupInfo->trener_id == get_current_user_id()) {
                            $y = 4;
                        }elseif($groupInfo->expert_id == get_current_user_id()){
                            continue;   // Станет 2
                        } elseif ($groupInfo->moderator_id == get_current_user_id()){
                            $y = 2;
                            continue;   //Станет 3
                        }
                    } else if ($y == 2) {
                        $link_choice = "&expert_id={$groupInfo->expert_id}";
                        $fiels_text = "AND d.expert_id = %d";
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
                        $fiels_text = "AND d.moderator_id = %d";
                        $fiels_id = $groupInfo->moderator_id;
                        $part_text = "модератора";
                    } ?>
                    <tr>
                        <th colspan="22">Оценка <?= $part_text ?></th>
                    </tr>
                    <?php
                        $userData = $wpdb->get_results($wpdb->prepare("SELECT d.user_id, d.proforma_id, d.proforma_spr_id, d.group_id, d.datetime, d.data_value, d.trener_id, d.expert_id, d.moderator_id
                            FROM p_proforma_user_data d                                
                            WHERE d.user_id = %d $fiels_text", $_POST['fileuserdata'], $fiels_id )); // Это нужно для отображения оценок этой группы

                        $userResult = $wpdb->get_row($wpdb->prepare("SELECT d.total, d.decision,d.section_a, d.section_b, d.section_c 
                            FROM p_proforma_user_result d                              
                            WHERE d.user_id = %d $fiels_text", $_POST['fileuserdata'], $fiels_id )); // Это нужно для отображения результатов этой группы
                    ?>

                    <tr>
                        <td colspan="2"> <?= nameUser($_POST['fileuserdata'], 5) ?></td>
                            <?php $q=0; ?>
                            <?php foreach ($userData as $user): ?>
                                <?php  //echo "{$proformaDataUser[$q]->proforma_spr_id} == {$data->id}<br>";
                                    if ($proformaDataUser[$q]->proforma_spr_id == $data->id) {
                                        $action = ($action_moderator == 1) ? "1" : "2";
                                    } else {
                                        $action =  "1";
                                    }
//                                ?>
                                <td>
                                    <?php
                                        if($user->data_value == 3){
                                            echo 'Плагиат';
                                        } elseif($user->data_value == -1){
                                             echo 'Плагиат';
                                        } else {
                                            echo $user->data_value;
                                        }
                                        ?>
                                </td>
                            <?php endforeach?>
                        <td><?= $userResult->total ?></td>
                        <td><?= $userResult->decision ?></td>
                    </tr>
                <?php endfor; ?>
            </table>

<!--            <div  id="timer" class="btn btn-success">Сохранить</div>-->
        </form>
    </div>


    <div class="card">
        <div class="card-body">
            <h4><?= RATIONALE ?></h4>
            <div class="form-group">
                <label for="textarea3"></label>
                <textarea data-assessment name="review" id="textarea3" class="form-control" rows="4" required="" aria-required="true" aria-invalid="false" placeholder="<?= ($res->review) ? "" : NOT_FILLED ?>"><?= $res->review ?></textarea>
            </div>

        </div><!--end .card-body -->

    </div><!--end .card -->



    <div class="boxcoding"></div>
    <div class="card-actionbar">
        <div class="card-actionbar-row">
            <button type="submit" class="btn btn-success" ><?= SAVE_TEXT ?></button>
            <input type="hidden" name="link" class="form-control" data-assessment value="assessment/?z=rubric_p_18">
            <input type="hidden" name="id" class="form-control" data-assessment value="<?=$_GET['id']?>">
            <input type="hidden" name="dataid" class="form-control" data-assessment value="<?=$res->id?>">
        </div>
    </div><!--end .card-actionbar -->

    <?php
//    unset($_POST);
//    printAll($_POST);

 ?>




