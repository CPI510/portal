<?php
global $wpdb;
$_POST = json_decode(file_get_contents("php://input"), true);
//printAll($_POST);


$res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_POST['fileuserdata'], get_current_user_id() ));
$gropinfo = groupInfo($_GET['id']);

if($name_var = translateDir($_GET['id']) == 'name'){
    $p_name = "p_name";
    $name = 'name';
    $lang_name = 'lang_name_ru';
    $name_org = 'name_org';
}else{
    $p_name = "name_kaz";
    $name = "name_kaz";
    $lang_name = 'lang_name_kz';
    $name_org = "name_org_kaz";
}

if( ( $gropinfo->moderator == get_current_user_id() || $gropinfo->independent_trainer_id == get_current_user_id()  ) && $gropinfo->program_id == 14){
    $notPr = "WHERE id != 3"; //3-пороговый уровень
}else{
    $notPr = "";
}

$allGrade = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade $notPr");

if (isset($_POST['grade_a'], $_POST['grade_b'], $_POST['grade_c'], $_POST['grade_a'])){

//    if (isset($_POST['grade_solution']) == 1 && ($_POST['grade_a'] != 1 || $_POST['grade_b'] != 1 || $_POST['grade_c'] != 1) ) {
//        alertStatus('warning', FINAL_GRADE. " не сопадает с остальными оценками");
//        echo'<meta http-equiv="refresh" content="5;url=/groups/?z=group&id='.$_POST['id'].'&error=1" />';
//    } else {
        if( $_POST['dataid']  == "" ){
            if( $_POST['grade_a'] == 5
                || $_POST['grade_b'] == 5
                || $_POST['grade_c'] == 5
            ) $_POST['grade_solution'] = 5;
            if($wpdb->insert('p_assessment_rubric', [
                'group_id' => $_POST['id'],
                'section_a_grade' => $_POST['grade_a'],
                'section_b_grade' => $_POST['grade_b'],
                'section_c_grade' => $_POST['grade_c'],
                'grading_solution' => $_POST['grade_solution'],

                'listener_id' => $_POST['fileuserdata'],
                'create_user_id' => get_current_user_id()
            ],[ '%d','%d','%d','%d','%d','%d', '%d'])) alertStatus('success',"Код добавлен!"); //else alertStatus('danger',"Код не добавлен!");
        }elseif( $_POST['dataid'] != "" ){
            if( $_POST['grade_a'] == 5
                || $_POST['grade_b'] == 5
                || $_POST['grade_c'] == 5
            ) $_POST['grade_solution'] = 5;
            if($wpdb->update( 'p_assessment_rubric',
                [
                    'section_a_grade' => $_POST['grade_a'],
                    'section_b_grade' => $_POST['grade_b'],
                    'section_c_grade' => $_POST['grade_c'],
                    'grading_solution' => $_POST['grade_solution'],
                ],
                [ 'group_id' => $_POST['id'], 'listener_id' => $_POST['fileuserdata'], 'create_user_id' => get_current_user_id() ],
                [ '%d','%d','%d','%d'],
                [ '%d', '%d','%d' ]
            )) alertStatus('success',"Код обновлен!"); //else alertStatus('danger',"Код не обновлен!");
        }else{
            alertStatus('danger',"Нет данных!");
        }

        echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['id'].'" />';
//    }


}else{


    ?>
    <div class="boxcoding"></div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered no-margin">
                <tr>
                    <th style="text-align: right"><?= ASSESSMENT_SHEET[1] ?></th>
                    <th><?= nameUser($_POST['fileuserdata'], 5) ?></th>
                </tr>
                <tr>
                    <td align="right"><?= ASSESSMENT_SHEET[9] ?> A</td>
                    <td><select name="grade_a" data-assessment class="form-control" id="select2" required>
                            <option></option>
                            <?php
                            foreach ($allGrade as $grade){
                                if ($grade->id == $res->section_a_grade){
                                    echo "<option value='{$grade->id}' selected>{$grade->$name}</option>";
                                }else{
                                    echo "<option value='{$grade->id}'>{$grade->$name}</option>";
                                }

                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right"><?= ASSESSMENT_SHEET[9] ?> B</td>
                    <td><select name="grade_b" data-assessment class="form-control" id="select2" required>
                            <option></option>
                            <?php
                            foreach ($allGrade as $grade){
                                if ($grade->id == $res->section_b_grade){
                                    echo "<option value='{$grade->id}' selected>{$grade->$name}</option>";
                                }else{
                                    echo "<option value='{$grade->id}'>{$grade->$name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right"><?= ASSESSMENT_SHEET[9] ?> C</td>
                    <td><select name="grade_c" data-assessment class="form-control" id="select2" required>
                            <option></option>
                            <?php
                            foreach ($allGrade as $grade){
                                if ($grade->id == $res->section_c_grade){
                                    echo "<option value='{$grade->id}' selected>{$grade->$name}</option>";
                                }else{
                                    echo "<option value='{$grade->id}'>{$grade->$name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right"><?= FINAL_DECISION ?></td>
                    <td><select name="grade_solution" data-assessment class="form-control" id="select2" required>
                            <option></option>
                            <?php
                            foreach ($allGrade as $grade){
                                if (
                                    (get_current_user_id() == $gropinfo->expert_id ||
                                        get_current_user_id() == $gropinfo->teamleader_id ||
                                        get_current_user_id() == $gropinfo->moderator_id) && $grade->id == 3
                                ){}else{
                                    if ($grade->id == $res->grading_solution){
                                        echo "<option value='{$grade->id}' selected>{$grade->$name}</option>";
                                    }else{
                                        echo "<option value='{$grade->id}'>{$grade->$name}</option>";
                                    }
                                }

                            }
                            ?>
                        </select>
                    </td>
                </tr>

            </table>

        </div><!--end .card-body -->
    </div><!--end .card -->

    <input type="hidden" name="id" class="form-control" data-assessment value="<?=$_GET['id']?>">
    <input type="hidden" name="dataid" class="form-control" data-assessment value="<?=$res->id?>">
    <input type="hidden" name="link" class="form-control" data-assessment value="assessment/?z=assessment_p_6">
    <input type="submit" class="btn btn-success" value="<?= SAVE_TEXT ?>" id="checkButton">

    <?php
}
?>

