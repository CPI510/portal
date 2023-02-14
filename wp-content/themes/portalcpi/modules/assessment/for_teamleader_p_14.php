<?php
global $wpdb;
$_POST = json_decode(file_get_contents("php://input"), true);

$gropinfo = groupInfo($_GET['id']);

if($gropinfo->teamleader_id !=  get_current_user_id()) alertStatus('danger','Access denied',true); // только тимлидер может выполнять действия здесь

if(isset($_GET['choiseid'], $_GET['id'], $_GET['listener_id'])){

    $sql = "INSERT INTO p_assessment_rubric_for_teamleader (group_id,create_user_id,rubric_id,info,listener_id) VALUES (%d,%d,%d,%s,%d) ON DUPLICATE KEY UPDATE rubric_id = %d, info = %s";
    $sql = $wpdb->prepare($sql,$_GET['id'],get_current_user_id(),$_GET['choiseid'],$_GET['info'],$_GET['listener_id'],$_GET['choiseid'],$_GET['info']);
    $wpdb->query($sql);

    if($assessment_rubric_teamleader = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_GET['listener_id'], get_current_user_id()))){
        $wpdb->query($wpdb->prepare("DELETE FROM `p_assessment_rubric` WHERE id = %d", $assessment_rubric_teamleader->id));
    }
    $wpdb->query($wpdb->prepare("insert into p_assessment_rubric( `date_create`, `date_update`, `group_id`, `create_user_id`, `listener_id`, `section_a_grade`, `section_a_description`, `section_b_grade`, `section_b_description`, `section_c_grade`, `section_c_description`, `review`, `grading_solution` )
select now(), `date_update`, `group_id` , %d, `listener_id`, `section_a_grade`, `section_a_description`, `section_b_grade`, `section_b_description`, `section_c_grade`, `section_c_description`, `review`, `grading_solution`
from p_assessment_rubric
where id = %d", get_current_user_id(), $_GET['choiseid']));

    echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id=' . $_GET['id'] .'" />';

}else {

//printAll($gropinfo);

    if ($name_var = translateDir($_GET['id']) == 'name') {
        $p_name = "p_name";
        $name = 'name';
        $lang_name = 'lang_name_ru';
        $name_org = 'name_org';
    } else {
        $p_name = "name_kaz";
        $name = "name_kaz";
        $lang_name = 'lang_name_kz';
        $name_org = "name_org_kaz";
    }

    $teamleader_info = $wpdb->get_row($s=$wpdb->prepare("SELECT a.rubric_id, a.create_user_id teamleaderid, a.info, b.listener_id, b.create_user_id
        FROM p_assessment_rubric_for_teamleader a
        LEFT OUTER JOIN p_assessment_rubric b ON b.id = a.rubric_id
        WHERE a.create_user_id = %d AND a.group_id = %d AND b.listener_id = %d", get_current_user_id(), $_GET['id'], $_POST['fileuserdata']) );
//    printAll($teamleader_info);

    $gradeArr = array();
    $allGrade = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade");
    foreach ($allGrade as $grade) {
        $gradeArr += [$grade->id => $grade->$name];
    }

    $results_gr = $wpdb->get_results($wpdb->prepare("SELECT *  FROM `p_assessment_rubric` WHERE `group_id` = %d AND `listener_id` = %d AND create_user_id != %d ", $_GET['id'], $_POST['fileuserdata'], get_current_user_id()));
    ?>
    <div class="boxcoding"></div>
    <h3><?= ASSESSMENT_SHEET[1] ?>: <?= nameUser($_POST['fileuserdata'], 5) ?></h3>
    <?php foreach ($results_gr as $grading): ?>

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered no-margin">
                    <tr>
                        <th colspan="3">
                            <h3>
                                <?php if($gropinfo->trener_id == $grading->create_user_id){
                                    echo TRAINER_ASSESSMENT;
                                }elseif($gropinfo->moderator_id == $grading->create_user_id){
                                    echo MODERATOR_ASSESSMENT;
                                    if($teamleader_info->create_user_id == $grading->create_user_id){
                                        echo ' <a href="" class="btn btn-info disabled">Выбрано!</a>';
                                    }else{
                                        echo ' <a href="/assessment/?z=for_teamleader_p_14&id='.$_GET['id'].'&choiseid='.$grading->id.'&info=moderator&listener_id='.$_POST['fileuserdata'].'" class="btn btn-info">Выбрать оценку</a>';
                                    }
                                }elseif($gropinfo->independent_trainer_id == $grading->create_user_id){
                                    echo MODERATOR_ASSESSMENT." 2";
                                    if($teamleader_info->create_user_id == $grading->create_user_id){
                                        echo ' <a href="" class="btn btn-info disabled">Выбрано!</a>';
                                    }else{
                                        echo ' <a href="/assessment/?z=for_teamleader_p_14&id='.$_GET['id'].'&choiseid='.$grading->id.'&info=moderator2&listener_id='.$_POST['fileuserdata'].'" class="btn btn-info">Выбрать оценку</a>';
                                    }
                                }elseif($gropinfo->expert_id == $grading->create_user_id){
                                    echo EXPERT_ASSESSMENT;
                                    if($teamleader_info->create_user_id == $grading->create_user_id){
                                        echo ' <a href="" class="btn btn-info disabled">Выбрано!</a>';
                                    }else{
                                        echo ' <a href="/assessment/?z=for_teamleader_p_14&id='.$_GET['id'].'&choiseid='.$grading->id.'&info=expert&listener_id='.$_POST['fileuserdata'].'" class="btn btn-info">Выбрать оценку</a>';
                                    }
                                } ?>
                            </h3>
                        </th>
                    </tr>
                    <tr>
                        <th style="text-align: right"><?= ASSESSMENT_SHEET[9] ?></th>
                        <th style="text-align: center"><?= ASSESSMENT_SECOND[6] ?></th>
                        <th><?= ASSESSMENT_SECOND[7] ?></th>
                    </tr>
                    <tr>
                        <td align="right"><?= ASSESSMENT_RUBRIC_14[0] ?></td>
                        <td align="center">
                            <?= $gradeArr[$grading->section_a_grade] ?>
                        </td>
                        <td>
                            <?= $grading->section_a_description ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><?= ASSESSMENT_RUBRIC_14[2] ?></td>
                        <td align="center">
                            <?= $gradeArr[$grading->section_b_grade] ?>
                        </td>
                        <td>
                            <?= $grading->section_b_description ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><?= ASSESSMENT_RUBRIC_14[4] ?></td>
                        <td align="center">
                            <?= $gradeArr[$grading->section_c_grade] ?>
                        </td>
                        <td>
                            <?= $grading->section_c_description ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><b><?= FINAL_DECISION ?></b></td>
                        <td align="center">
                            <b><?= $gradeArr[$grading->grading_solution] ?></b>
                        </td>
                        <td>
                            <b><?= $grading->review ?></b>
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    <?php endforeach;?>


    <?php
}
?>
