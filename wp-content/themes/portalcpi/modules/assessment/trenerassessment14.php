<?php
global $wpdb;
$_POST = json_decode(file_get_contents("php://input"), true);

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

$gradeArr = array();
$allGrade = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");
foreach ($allGrade as $grade){
    $gradeArr += [ $grade->id => $grade->$name ];
}
?>

<div class="boxcoding"></div>
<h2><?= ASSESSMENT_SHEET[1] ?>: <?= nameUser($_POST['fileuserdata'], 5) ?></h2>
<?php if($gropinfo->moderator_id == get_current_user_id()): ?>
<?php $res_expert = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_POST['fileuserdata'], $gropinfo->expert_id )); ?>

    <div class="card">
        <div class="card-body">

            <h3><?= EXPERT_ASSESSMENT ?>: <?= nameUser($gropinfo->expert_id,5) ?></h3>
            <table class="table table-bordered no-margin">
                <tr>
                    <th style="text-align: right"><?= ASSESSMENT_SHEET[9] ?></th>
                    <th style="text-align: center"><?= ASSESSMENT_SECOND[6] ?></th>
                </tr>
                <tr>
                    <td align="right"><?= ASSESSMENT_RUBRIC_14[0] ?></td>
                    <td>
                        <?= $gradeArr[$res_expert->section_a_grade] ?>
                    </td>
                </tr>
                <tr>
                    <td align="right"><?= ASSESSMENT_RUBRIC_14[2] ?></td>
                    <td>
                        <?= $gradeArr[$res_expert->section_b_grade] ?>
                    </td>
                </tr>
                <tr>
                    <td align="right"><?= ASSESSMENT_RUBRIC_14[4] ?></td>
                    <td>
                        <?= $gradeArr[$res_expert->section_c_grade] ?>
                    </td>
                </tr>
                <tr>
                    <td align="right"><b><?= FINAL_DECISION ?></b></td>
                    <td>
                        <b><?= $gradeArr[$res_expert->grading_solution] ?></b>
                    </td>
                </tr>
                <tr>
                    <td align="right"><b><?= ASSESSMENT_SECOND[7] ?></b></td>
                    <td><?= $res_expert->review ?></td>
                </tr>
            </table>

        </div><!--end .card-body -->
    </div><!--end .card -->
<?php endif; ?>

<?php $res_trener = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_POST['fileuserdata'], $gropinfo->trener_id )); ?>

<div class="card">
    <div class="card-body">

        <h3><?= TRAINER_ASSESSMENT ?>: <?= nameUser($gropinfo->trener_id,5) ?></h3>
        <table class="table table-bordered no-margin">
            <tr>
                <th style="text-align: right"><?= ASSESSMENT_SHEET[9] ?></th>
                <th style="text-align: center"><?= ASSESSMENT_SECOND[6] ?></th>
            </tr>
            <tr>
                <td align="right"><?= ASSESSMENT_RUBRIC_14[0] ?></td>
                <td>
                    <?= $gradeArr[$res_trener->section_a_grade] ?>
                </td>
            </tr>
            <tr>
                <td align="right"><?= ASSESSMENT_RUBRIC_14[2] ?></td>
                <td>
                    <?= $gradeArr[$res_trener->section_b_grade] ?>
                </td>
            </tr>
            <tr>
                <td align="right"><?= ASSESSMENT_RUBRIC_14[4] ?></td>
                <td>
                    <?= $gradeArr[$res_trener->section_c_grade] ?>
                </td>
            </tr>
            <tr>
                <td align="right"><b><?= FINAL_DECISION ?></b></td>
                <td>
                    <b><?= $gradeArr[$res_trener->grading_solution] ?></b>
                </td>
            </tr>
            <tr>
                <td align="right"><b><?= ASSESSMENT_SECOND[7] ?></b></td>
                <td><?= $res_trener->review ?></td>
            </tr>
        </table>

    </div><!--end .card-body -->
</div><!--end .card -->