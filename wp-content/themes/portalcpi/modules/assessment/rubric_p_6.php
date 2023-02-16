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
        echo "<div align='center'>".ASSESSMENT_SECOND[2]." <br>".LISTENER_TEXT.": " . nameUser($_POST['fileuserdata'], 5) . "</div>";
        ?>
    </h3>
    <?php $grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade $moder_sql"); ?>


    <div class="card">
        <?php if(isset($res) && !empty($res->id )): ?>

            <?php if( get_current_user_id() == $gropinfo->teamleader_id && ($res->grading_solution == 1 || $res->grading_solution == 2) ): ?>
                <a href="/export_to_word/?form=assessment_p_6&create_user_id=<?=$res->create_user_id?>&listener_id=<?=$res->listener_id?>&group=<?=$_GET['id']?>" class="btn btn-primary"><?=  ASSESSMENT_SECOND[0] ?></a>
            <?php else:?>
                <a href="/export_to_word/?form=assessment_p_6&create_user_id=<?=$res->create_user_id?>&listener_id=<?=$res->listener_id?>&group=<?=$_GET['id']?>&ver=2" class="btn btn-primary"><?= ($gropinfo->teamleader_id == get_current_user_id()) ? DOWNLOAD_JUSTIFICATION : ASSESSMENT_SECOND[0] ?></a>
            <?php endif; ?>
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
                    <td><b><?= ASSESSMENT_SHEET[9] ?> A</b></td>
                    <td>
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
                    <td><b><?= ASSESSMENT_SHEET[9] ?> B</b></td>
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
                    <td><b><?= ASSESSMENT_SHEET[9] ?> C</b></td>
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
            </table>

        </div><!--end .card-body -->

    </div><!--end .card -->


    <div class="card">
        <div class="card-body">
            <h4><?= RATIONALE ?></h4>
            <div class="form-group">
                <label for="textarea3"></label>
                <textarea data-assessment name="review" id="textarea3" class="form-control" rows="8" required="" aria-required="true" aria-invalid="false" placeholder="<?= ($res->review) ? "" : NOT_FILLED ?>"><?= $res->review ?></textarea>
            </div>
            <div class="form-group">
                <label for="select1"><?= FINAL_GRADE ?>:</label>
                <?php if($gropinfo->teamleader_id1 == get_current_user_id()): ?>
                    <b><?= $gradeArr[$res->grading_solution] ?></b>
                    <input type="hidden" data-assessment name="grading_solution" value="<?= $res->grading_solution ?>">
                <?php else: ?>
                    <select data-assessment name="grading_solution" class="form-control" required="" aria-required="true" aria-invalid="false">
                        <option value="">&nbsp;</option>
                        <?php foreach ($grades as $grade): ?>
                            <?php if(
                                (get_current_user_id() == $gropinfo->expert_id ||
                                    get_current_user_id() == $gropinfo->teamleader_id ||
                                    get_current_user_id() == $gropinfo->moderator_id ) && $grade->id == 3
                            ): ?>
                            <?php else: ?>
                                <?php if( $grade->id == $res->grading_solution ): ?>
                                    <option value="<?= $grade->id ?>" selected><?= $grade->$name_var ?></option>
                                <?php else: ?>
                                    <option value="<?= $grade->id ?>"><?= $grade->$name_var ?></option>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </select>
                    <div class="form-control-line"></div>
                <?php endif; ?>

            </div>

        </div><!--end .card-body -->

    </div><!--end .card -->

    <div class="boxcoding"></div>
    <div class="card-actionbar">
        <div class="card-actionbar-row">
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


