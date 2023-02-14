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
            'section_d_grade' => $_POST['grade_d'],
            'section_e_grade' => $_POST['grade_e'],
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
                'section_d_grade' => $_POST['grade_d'],
                'section_e_grade' => $_POST['grade_e'],
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
            <h5><?= ASSESSMENT_SHEET[1] ?>: <?= nameUser($_POST['fileuserdata'], 5) ?></h5>
            <table class="table table-bordered no-margin">
                <tr>
                    <th ></th>
                    <th width="400px"></th>
                </tr>
                <tr>
                    <td><?= ASSESSMENT_TRENER_17[0] ?> </td>
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
                    <td><?= ASSESSMENT_TRENER_17[1] ?></td>
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
                    <td><?= ASSESSMENT_TRENER_17[2] ?> </td>
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
                    <td><?= ASSESSMENT_TRENER_17[3] ?> </td>
                    <td><select name="grade_d" data-assessment class="form-control" id="select2" required>
                            <option></option>
                            <?php
                            foreach ($allGrade as $grade){
                                if ($grade->id == $res->section_d_grade){
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
                    <td><?= ASSESSMENT_TRENER_17[4] ?> </td>
                    <td><select name="grade_e" data-assessment class="form-control" id="select2" required>
                            <option></option>
                            <?php
                            foreach ($allGrade as $grade){
                                if ($grade->id == $res->section_e_grade){
                                    echo "<option value='{$grade->id}' selected>{$grade->$name}</option>";
                                }else{
                                    echo "<option value='{$grade->id}'>{$grade->$name}</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>

            </table>

        </div><!--end .card-body -->
    </div><!--end .card -->

    <input type="hidden" name="grade_solution" class="form-control" data-assessment value="0">
    <input type="hidden" name="id" class="form-control" data-assessment value="<?=$_GET['id']?>">
    <input type="hidden" name="dataid" class="form-control" data-assessment value="<?=$res->id?>">
    <input type="hidden" name="link" class="form-control" data-assessment value="assessment/?z=assessment_p_17">
    <input type="submit" class="btn btn-success" value="<?= SAVE_TEXT ?>" id="checkButton">

    <?php
}
?>

