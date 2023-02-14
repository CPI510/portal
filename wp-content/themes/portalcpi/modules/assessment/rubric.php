<?php
global $wpdb;
$_POST = json_decode(file_get_contents("php://input"), true);
//printAll($_POST);

if($_POST['fileuserdata']
    && $_POST['section_a_grade']
    && $_POST['section_b_grade']
    && $_POST['section_c_grade']
){

    if( $_POST['dataid']  == "" ){
        if($wpdb->insert('p_assessment_rubric', [
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
        ],['%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s'])) alertStatus('success',"Форма добавлена!"); //else alertStatus('danger',"Код не добавлен!");
    }elseif( $_POST['dataid'] != "" ){
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
        )) alertStatus('success',"Форма обновлена!"); //else alertStatus('danger',"Код не обновлен!");
        echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['id'].'" />';

    }else{
        alertStatus('danger',"Нет данных!");
    }

}else{

    $gropinfo = groupInfo($_GET['id']);

    $name_var = translateDir($_GET['id']);
?>

<?php $res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_POST['fileuserdata'], get_current_user_id() )); ?>

    <h3><?php if( $gropinfo->independent_trainer_id == get_current_user_id() || $gropinfo->moderator_id == get_current_user_id()){ // Проверяем независимый тренер или модератор
        $code = $wpdb->get_var($wpdb->prepare("SELECT code FROM p_assessment_coding_user WHERE group_id = %d AND listener_id = %d", $_GET['id'], $_POST['fileuserdata']));
            echo "Код слушателя: $code";
            if($gropinfo->moderator_id == get_current_user_id()){
                $moder_sql = "WHERE id != 3";
            }
    }elseif($gropinfo->trener_id == get_current_user_id() || $gropinfo->expert_id == get_current_user_id() ){
            echo "<div align='center'>".ASSESSMENT_SECOND[2]." <br>".LISTENER_TEXT.": " . nameUser($_POST['fileuserdata'], 5) . "</div>";
        }elseif($gropinfo->independent_trainer_id != get_current_user_id() || $gropinfo->moderator_id != get_current_user_id() || $gropinfo->moderator_id != get_current_user_id()) {
            $code = $wpdb->get_var($wpdb->prepare("SELECT code FROM p_assessment_coding_user WHERE group_id = %d AND listener_id = %d", $_GET['id'], $_POST['fileuserdata']));
            echo "Код слушателя: $code";
            if ($gropinfo->moderator_id == get_current_user_id()) {
                $moder_sql = "WHERE id != 3";
            }

        }

        ?>
    </h3>
        <div class="card">
            <?php if($res): ?>
                <a href="/export_to_word/?form=assessment&create_user_id=<?=$res->create_user_id?>&listener_id=<?=$res->listener_id?>&group=<?=$_GET['id']?>" class="btn btn-primary"><?= ASSESSMENT_SECOND[0] ?></a>
                <!--<a href="/groups/?z=group&id=<?= $_GET['id'] ?>&download_rubric=1&category_id=2&rubric_user_id=<?= $_POST['fileuserdata'] ?>" class="btn btn-primary"><?= RUBRIC_DOWNLOAD ?></a>-->
            <?php else: ?>
                <a href="" class="btn btn-primary disabled"><?= ASSESSMENT_SECOND[0] ?></a>
                <!--<a href="" class="btn btn-primary disabled"><?= RUBRIC_DOWNLOAD ?></a>-->
            <?php endif; ?>

            <div class="card-body">
                <table class="table table-bordered no-margin">
                    <tr>
                        <td><?= ASSESSMENT_RUBRIC[1] ?></td>
                        <td><select name="section_a_grade" class="form-control" required="" data-assessment aria-required="true" aria-invalid="false">
                                <option value="">&nbsp;</option>
                                <?php $grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade $moder_sql") ?>
                                <?php foreach ($grades as $grade): ?>
                                    <?php if( $grade->id == $res->section_a_grade ): ?>
                                        <option value="<?= $grade->id ?>" selected><?= $grade->$name_var ?></option>
                                    <?php else: ?>
                                        <option value="<?= $grade->id ?>"><?= $grade->$name_var ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-control-line"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><?= ASSESSMENT_RUBRIC[3] ?></td>
                        <td>
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
                        </td>
                    </tr>
                    <tr>
                        <td><?= ASSESSMENT_RUBRIC[5] ?></td>
                        <td>
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
                        </td>
                    </tr>
                </table>

                <?php
                /*<div class="form-group">
                    <label for="textarea3"><?= ASSESSMENT_SECOND[7] ?></label>
                    <textarea data-assessment name="section_a_description" id="text_area"  class="form-control" rows="6" required="" aria-required="true" aria-invalid="false" placeholder="<?= ($res->section_a_description) ? "" : NOT_FILLED  ?>"><?= $res->section_a_description ?></textarea>
                    <div class="form-control-line"></div>
                </div>*/
                ?>


            </div><!--end .card-body -->

        </div><!--end .card -->



        <div class="card">
        <div class="card-body">
            <h4>
                <?php if($gropinfo->moderator_id == get_current_user_id()) echo DECISION_MODERATION;
                else echo ASSESSMENT_SECOND[5] ?>
            </h4>
            <div class="form-group">
                <label for="textarea3"></label>
                <textarea  data-assessment name="review" id="textarea3" class="form-control" rows="6" required="" aria-required="true" aria-invalid="false" placeholder="<?= ($res->review) ? "" : NOT_FILLED ?>"><?= $res->review ?></textarea>
            </div>
            <div class="form-group">
                <h4>
                    <?php if($gropinfo->moderator_id == get_current_user_id()) echo MODERATION_DECISION;
                    else echo ASSESSMENT_SECOND[4] ?>:
                </h4>
                <select data-assessment name="grading_solution" class="form-control" required="" aria-required="true" aria-invalid="false">
                    <option value="">&nbsp;</option>
                    <?php foreach ($grades as $grade): ?>
                        <?php if( $grade->id == $res->grading_solution ): ?>
                            <option value="<?= $grade->id ?>" selected><?= $grade->$name_var ?></option>
                        <?php else: ?>
                            <option value="<?= $grade->id ?>"><?= $grade->$name_var ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <div class="form-control-line"></div>
            </div>

        </div><!--end .card-body -->

    </div><!--end .card -->

    <div class="boxcoding"></div>
        <div class="card-actionbar">
            <div class="card-actionbar-row">
                <button type="submit" class="btn btn-success"><?= SAVE_TEXT ?></button>
                <input type="hidden" name="link" class="form-control" data-assessment value="assessment/?z=rubric">
                <input type="hidden" name="id" class="form-control" data-assessment value="<?=$_GET['id']?>">
                <input type="hidden" name="dataid" class="form-control" data-assessment value="<?=$res->id?>">
            </div>
        </div><!--end .card-actionbar -->

<?php } ?>
