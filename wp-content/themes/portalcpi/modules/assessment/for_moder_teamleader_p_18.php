<?php
global $wpdb;

$_POST = json_decode(file_get_contents("php://input"), true);

$groupInfo = groupInfo($_GET['id']);
$name_var = translateDir($_GET['id']);


if($_POST['action'] && $_POST['dataid']) {

    $arrGrades = array();
    //Created array
    $i = 18;
    $section_index = -1;
    $section_array = [1,1,1,2,2,2,3,3,3];
    $user_id = $_POST['fileuserdata'];
    foreach($_POST as $key => $value) {
        if (strpos($key, 'items') === 0) {
            $arrGrades[$user_id][$i] = array('proforma_spr_id' => $i, 'data_value' => $value, 'section_id' => $section_array[$section_index]);
        }
        $i++;
        $section_index++;
    }
    //reset variables
    $i=0;
    $section_index = 0;
    unset($i);
    unset($section_index);


    foreach ($arrGrades as $grades) {
        foreach ( $grades as $data ) {

            $section_id = $data['section_id'];
            $value = $data['data_value'];
            $spr_id = $data['proforma_spr_id'];

            if ( $_POST['action'] == 1 ) {

                $wpdb->insert( 'p_proforma_user_data', [
                    'user_id' => $user_id,
                    'proforma_id' => 2,
                    'group_id' => $_POST['id'],
                    'proforma_spr_id' => $spr_id,
                    'data_value' => $value,
                    'moderator_id' => get_current_user_id(),
                ], [ '%d', '%d','%d', '%d','%d', '%d' ] );


                if ($value == 3) {
                    $final["insert"][$user_id]['plagiarism'] = 3;

                    if ($section_id == 1) $final["insert"][$user_id]['plagiarism_section_a'] = 3;
                    elseif($section_id == 2) $final["insert"][$user_id]['plagiarism_section_b'] = 3;
                    else $final["insert"][$user_id]['plagiarism_section_c'] = 3;
                } elseif($value == 0) {
                    $final["insert"][$user_id]['none'] = 1;

                    if($section_id == 1) $final["insert"][$user_id]['none_section_a'] = 1;
                    elseif ($section_id == 2)  $final["insert"][$user_id]['none_section_b'] = 1;
                    else $final["insert"][$user_id]['none_section_c'] = 1;
                } elseif ($value == -1) {
                    $final["insert"][$user_id]['absent'] = 1;

                    if($section_id == 1) $final["insert"][$user_id]['absent_section_a'] = 1;
                    elseif ($section_id == 2)  $final["insert"][$user_id]['absent_section_b'] = 1;
                    else $final["insert"][$user_id]['absent_section_c'] = 1;
                }else{
                    if($section_id == 1){
                        $final["insert"][$user_id]['section_a'] += $value;
                    }elseif ($section_id == 2){
                        $final["insert"][$user_id]['section_b'] += $value;
                    }elseif ($section_id == 3){
                        $final["insert"][$user_id]['section_c'] += $value;
                    }
                    $final["insert"][$user_id]['total'] += $value;
                }

            }
            else if ($_POST['action'] == 2) {
                //update
                $wpdb->update( 'p_proforma_user_data',
                    [ 'data_value' => $value, 'datetime_update' => dateTime() ],
                    [ 'user_id' => $user_id, 'proforma_id' => 2, 'group_id' => $_POST['id'], 'proforma_spr_id' => $spr_id, 'moderator_id' => get_current_user_id() ],
                    [ '%d', '%s' ],
                    [ '%d', '%d', '%d', '%d', '%d' ]
                );

                if ($value == 3){
                    $final["update"][$user_id]['plagiarism'] = 3;
                    if($section_id == 1) $final["update"][$user_id]['plagiarism_section_a'] = 3;
                    elseif($section_id == 2) $final["update"][$user_id]['plagiarism_section_b'] = 3;
                    else $final["update"][$user_id]['plagiarism_section_c'] = 3;
                }elseif($value == 0){
                    $final["update"][$user_id]['none'] = 1;
                    if($section_id == 1) $final["update"][$user_id]['none_section_a'] = 1;
                    elseif($section_id == 2) $final["update"][$user_id]['none_section_b'] = 1;
                    else $final["update"][$user_id]['none_section_c'] = 1;
                }elseif($value == -1){
                    $final["update"][$user_id]['absent'] = 1;
                    if($section_id == 1) $final["update"][$user_id]['absent_section_a'] = 1;
                    elseif ($section_id == 2)  $final["update"][$user_id]['absent_section_b'] = 1;
                    else $final["update"][$user_id]['absent_section_c'] = 1;
                }else{
                    if($section_id == 1){
                        $final["update"][$user_id]['section_a'] += $value;
                    }elseif ($section_id == 2) {
                        $final["update"][$user_id]['section_b'] += $value;
                    }elseif ($section_id == 3){
                        $final["update"][$user_id]['section_c'] += $value;
                    }
                    $final["update"][$user_id]['total'] += $value;

                }
            }
            else {
                echo "Нет данных!";
            }
        }
    }

    foreach ($final as $action => $total) {
        foreach ($total as $uid => $actions) {
            //echo "$action : $uid:  decision: $actions[decision], total : $actions[total], plagiarism: $actions[plagiarism], none: $actions[none],
            //section_a: $actions[section_a], section_b: $actions[section_b],  sections_c: $actions[section_c]<br>";

            if ($actions['plagiarism'] == 3 &&  $actions['plagiarism_section_a'] == 3 ) {
                $final = "Плагиат";
                $decision = "Незачет";
                $section_a = "Плагиат";
                $section_b = $actions['section_b'];
                $section_c = $actions['section_c'];
            } elseif ($actions['plagiarism'] == 3 &&  $actions['plagiarism_section_b'] == 3) {
                $final = "Плагиат";
                $decision = "Незачет";
                $section_a = $actions['section_a'];
                $section_b = "Плагиат";
                $section_c = $actions['section_c'];
            } elseif ($actions['plagiarism'] == 3 &&  $actions['plagiarism_section_c'] == 3) {
                $final = "Плагиат";
                $decision = "Незачет";
                $section_a = $actions['section_a'];
                $section_b = $actions['section_b'];
                $section_c = "Плагиат";
            } elseif ( $actions['absent'] == 1 && $actions['absent_section_a'] == 1) {
                $final = 0;
                $decision = 'Неявка';
                $section_a = 'Неявка';
                $section_b = 'Неявка';
                $section_c = 'Неявка';
            } elseif ( $actions['absent'] == 1 && $actions['absent_section_b'] == 1) {
                $final = 0;
                $decision = 'Неявка';
                $section_a = 'Неявка';
                $section_b = 'Неявка';
                $section_c = 'Неявка';
            } elseif ( $actions['absent'] == 1 && $actions['absent_section_c'] == 1) {
                $final = 0;
                $decision = 'Неявка';
                $section_a = 'Неявка';
                $section_b = 'Неявка';
                $section_c = 'Неявка';
            } elseif ( $actions['none'] == 1 && $actions['none_section_a'] == 1) {
                $final = 0;
                $decision = 'Незачет';
                $section_a = 0;
                $section_b = $actions['section_b'];
                $section_c = $actions['section_c'];
            } elseif ( $actions['none'] == 1 && $actions['none_section_b'] == 1) {
                $final = 0;
                $decision = 'Незачет';
                $section_a = $actions['section_a'];
                $section_b = 0;
                $section_c = $actions['section_c'];
            } elseif ( $actions['none'] == 1 && $actions['none_section_c'] == 1) {
                $final = 0;
                $decision = 'Незачет';
                $section_a = $actions['section_a'];
                $section_b = $actions['section_b'];
                $section_c = 0;
            }elseif($actions['total'] < 10) {
                $final = $actions['total'];
                $decision = 'Незачет';
                $section_a = $actions['section_a'];
                $section_b = $actions['section_b'];
                $section_c = $actions['section_c'];
            } else {
                $final = $actions['total'];
                $decision = 'Зачет';
                $section_a = $actions['section_a'];
                $section_b = $actions['section_b'];
                $section_c = $actions['section_c'];
            }


            if($action == "update") {
                $result = $wpdb->update( 'p_proforma_user_result',
                    [ 'section_c' => $section_c, 'section_b' => $section_b, 'section_a' => $section_a, 'total' => $final, 'decision' => $decision, 'date_update' => current_time('mysql', 0), 'review' => $_POST['review'] ],
                    [ 'user_id' => $uid, 'proforma_id' => 2, 'group_id' => $_POST['id'], 'moderator_id' => get_current_user_id() ],
                    [ '%s', '%s', '%s', '%s', '%s', '%s', '%s'],
                    [ '%d', '%d', '%d', '%d' ]
                );

            } else if($action == "insert") {
                $result = $wpdb->insert('p_proforma_user_result', [
                    'user_id' => $uid,
                    'proforma_id' => 2,
                    'group_id' => $_POST['id'],
                    'total' => $final,
                    'decision' => $decision,
                    'section_a' => $section_a,
                    'section_b' => $section_b,
                    'section_c' => $section_c,
                    'moderator_id' => get_current_user_id(),
                    'review' => $_POST['review'],
                ], ['%d', '%d','%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s']);

//                           printAll('user_id ' . $uid);
//                           printAll('proforma_id ' . 2,);
//                           printAll('group_id ' . $_GET['group'],);
//                           printAll('total ' . $final,);
//                           printAll('decision ' . $decision,);
//                           printAll('section_a ' . $section_a);
//                           printAll('section_b ' . $section_b);
//                           printAll('section_c ' . $section_c);

            }
        }
    }
    if ($result) {
        alertStatus('success', "Форма обновлена!");
        echo '<meta http-equiv="refresh" content="1;url=/groups/?z=group&id=' . $_POST['id'] . '" />';
        exit();
    }
    else{
        alertStatus('danger',"Код не обновлен!");
        echo $wpdb->last_error;
        echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['id'].'" />';
    }
}

?>

<?php

if(get_current_user_id() == $groupInfo->moderator_id){
    $res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_proforma_user_result WHERE group_id = %d AND user_id = %d AND moderator_id = %d", $_GET['id'], $_POST['fileuserdata'], get_current_user_id()));
    $field_name = 'moderator_id';
    $field_text = "AND p.moderator_id = %d";
    //$expertGrades = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_proforma_user_result WHERE group_id = %d AND user_id = %d AND expert_id = %d", $_GET['id'], $_POST['fileuserdata'], $groupInfo->expert_id));
    //trenerGrades = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_proforma_user_result WHERE group_id = %d AND user_id = %d AND expert_id = %d", $_GET['id'], $_POST['fileuserdata'], $groupInfo->expert_id));

    $trenerData = $wpdb->get_results($wpdb->prepare("SELECT d.user_id, d.proforma_id, d.proforma_spr_id, d.group_id, d.datetime, d.data_value, d.trener_id, d.expert_id, d.moderator_id
                            FROM p_proforma_user_data d                                
                            WHERE d.user_id = %d AND d.trener_id = %d", $_POST['fileuserdata'],  $groupInfo->trener_id));

    $expertData = $wpdb->get_results($wpdb->prepare("SELECT d.user_id, d.proforma_id, d.proforma_spr_id, d.group_id, d.datetime, d.data_value, d.trener_id, d.expert_id, d.moderator_id
                            FROM p_proforma_user_data d                                
                            WHERE d.user_id = %d AND d.expert_id = %d", $_POST['fileuserdata'],  $groupInfo->expert_id));

    $trenerResult = $wpdb->get_row($wpdb->prepare("SELECT d.user_id, d.group_id, d.total, d.decision, d.trener_id, d.expert_id, d.moderator_id, d.review
                            FROM p_proforma_user_result d                              
                            WHERE d.user_id = %d AND d.trener_id = %d AND d.group_id = %d", $_POST['fileuserdata'], $groupInfo->trener_id, $_GET['id']));

    $expertResult = $wpdb->get_row($wpdb->prepare("SELECT d.user_id, d.group_id, d.total, d.decision, d.trener_id, d.expert_id, d.moderator_id, d.review
                            FROM p_proforma_user_result d                              
                            WHERE d.user_id = %d AND d.expert_id = %d AND d.group_id = %d", $_POST['fileuserdata'], $groupInfo->expert_id,$_GET['id']));

}

?>


<h3>
    <?php echo "<div align='center'>".ASSESSMENT_SECOND[2]." <br>".LISTENER_TEXT.": " . nameUser($_POST['fileuserdata'], 5) . "</div>"; ?>
</h3>

<div class="card">
    <?php if($res): ?>

        <?php if(!empty($res->review)) : ?>
            <a href="/export_to_word/?form=<?= $res->proforma_id?>_review&group=<?= $res->group_id ?>&user_id=<?= $res->user_id?>" class="btn btn-primary"><?= ASSESSMENT_SECOND[0] ?> </a>
        <?php else : ?>
            <a class="btn btn-danger" disabled><?= ASSESSMENT_SECOND[0] ?></a>
        <?php endif; ?>

    <?php endif; ?>
    <form novalidate id="form" method="post" action="">
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

            <tr>
                <th colspan="22">Оценка Тренера</th>
            </tr>

            <tr>
                <td colspan="2"> <?= nameUser($_POST['fileuserdata'], 5) ?></td>

                <?php foreach ($trenerData as $user): ?>
                    <td>
                        <?php
                        if($user->data_value == 3){
                            echo 'Плагиат';
                        } elseif($user->data_value == -1){
                            echo 'Неявка';
                        } else {
                            echo $user->data_value;
                        }
                        ?>
                    </td>
                <?php endforeach?>
                <td><?= $trenerResult->total ?></td>
                <td><?= $trenerResult->decision ?></td>
            </tr>

            <tr>
                <th colspan="22">Оценка Эксперта</th>
            </tr>

            <tr>
                <td colspan="2"> <?= nameUser($_POST['fileuserdata'], 5) ?></td>
                <?php foreach ($expertData as $user): ?>
                    <td>
                        <?php
                        if($user->data_value == 3){
                            echo 'Плагиат';
                        } elseif($user->data_value == -1){
                            echo 'Неявка';
                        } else {
                            echo $user->data_value;
                        }
                        ?>
                    </td>
                <?php endforeach?>
                <td><?= $expertResult->total ?></td>
                <td><?= $expertResult->decision ?></td>
            </tr>

            <tr>
                <th colspan="22">Оценка Модератора</th>
            </tr>

            <tr>
                <td colspan="2"> <?= nameUser($_POST['fileuserdata'], 5) ?></td>
                <?php
                    $proformaDataUser = $wpdb->get_results($s=$wpdb->prepare("SELECT p.id, p.user_id, p.proforma_id, p.proforma_spr_id, p.group_id, p.datetime, p.data_value, p.datetime_update, p.trener_id, p.expert_id, p.moderator_id 
                                FROM p_proforma_user_data p
                                WHERE p.user_id= %d AND p.proforma_id = 2 AND p.group_id =%d $field_text", $_POST['fileuserdata'], $_GET['id'], get_current_user_id()));

                    // 1 - Insert, 2 - Update
                    $action = ($proformaDataUser) ? "2":"1";
                ?>
                <?php $q=0; ?>

                <?php foreach ($proformaSpr as $data): ?>
                    <td>
                        <?php if($groupInfo->moderator_id == get_current_user_id()): ?>
                            <select name="items[<?= $user->user_id ?>][<?= $data->id ?>]" data-assessment class="form-control" required>
                                <option></option>
                                <?php if(($proformaDataUser[$q]->data_value == 3)){
                                    //Прошлое условие которое показывало плагиатом целую секцию if(($proformaDataUser[$q]->data_value == 3 || ($data->section_id == 1 && $user->section_a == "Плагиат") || ($data->section_id == 2 && $user->section_b == "Плагиат")  || ($data->section_id == 3 && $user->section_c == "Плагиат")))
                                    echo '<option value="0">0</option>
                                                                  <option value="1">1</option>
                                                                  <option value="2">2</option>
                                                                  <option value="3" selected>Плагиат</option>  
                                                                  <option value="-1">Неявка</option>  
                                                                  ';
                                }elseif(($proformaDataUser[$q]->data_value == -1 || ($data->section_id == 1 && $user->section_a == "Неявка") || ($data->section_id == 2 && $user->section_b == "Неявка")  || ($data->section_id == 3 && $user->section_c == "Неявка"))){
                                    echo '<option value="0">0</option>
                                                                  <option value="1">1</option>
                                                                  <option value="2">2</option>
                                                                  <option value="3">Плагиат</option>  
                                                                  <option value="-1" selected>Неявка</option>  
                                                                  ';
                                } else{

                                    ?>
                                    <option value="0" <?= (isset($proformaDataUser[$q]->data_value) && $proformaDataUser[$q]->data_value == 0) ? "selected" : "" ?>>0</option>
                                    <option value="1" <?= ($proformaDataUser[$q]->data_value == 1) ? "selected" : "" ?>>1</option>
                                    <option value="2" <?= ($proformaDataUser[$q]->data_value == 2) ? "selected" : "" ?>>2</option>
                                    <option value="3" >Плагиат</option>
                                    <option value="-1" >Неявка</option>
                                <?php } ?>
                            </select>
                        <?php endif; ?>

                    </td>
                    <?php $q++; ?>
                <?php endforeach; ?>
                <td><?= $res->total ?></td>
                <td><?= $res->decision ?></td>
            </tr>
        </table>
    </form>
</div>


<?php if(get_current_user_id() == $groupInfo->moderator_id) : ?>
    <div class="card">
        <div class="card-body">
            <h4><?= EXPERT_RATIONALE ?></h4>
            <div class="form-group">
                <label for="textarea3"></label>
                <div name="expert_review" id="textarea4" class="form-control">
                    <?= $expertResult->review ?>
                </div>
            </div>

        </div><!--end .card-body -->
    </div><!--end .card -->
<?php endif; ?>



<div class="card">
    <div class="card-body">
        <h4><?= RATIONALE ?></h4>
        <div class="form-group">
            <label for="textarea3"></label>
            <textarea data-assessment name="review" id="textarea3" class="form-control" rows="4" required="" aria-required="true" aria-invalid="false" placeholder="<?= ($res->review) ? "" : NOT_FILLED ?>"><?= $res->review ?></textarea>
        </div>

    </div><!--end .card-body -->

</div><!--end .card -->


<?php
//    printAll($_GET['id']); // group_id
//    printAll($_POST['fileuserdata']); //user_id
//    printAll($action); //action, 1 - INSERT, 2 - UPDATE
?>


<div class="boxcoding"></div>
<div class="card-actionbar">
    <div class="card-actionbar-row">
        <button id="timer"  type="submit" class="btn btn-success" ><?= SAVE_TEXT ?></button>
        <input type="hidden" name="link" class="form-control" data-assessment value="assessment/?z=for_moder_teamleader_p_18">
        <input type="hidden" name="id" class="form-control" data-assessment value="<?=$_GET['id']?>">
        <input type="hidden" name="dataid" class="form-control" data-assessment value="<?=$_POST['fileuserdata']?>">
        <input type="hidden" name="action" class="form-control" data-assessment value="<?=$action?>">
    </div>
</div><!--end .card-actionbar -->







