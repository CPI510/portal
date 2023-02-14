<?php
global $wpdb;
if( isset( $_POST['action'] ) && isset( $_POST['userid'] )  && isset( $_POST['id'] ) && isset( $_POST['section_a_grade'] ) && isset( $_POST['section_b_grade'] ) && isset( $_POST['section_c_grade'] ) && isset( $_POST['grading_solution'] ) ){

    if( $_POST['action']  == "insert" ){
        if($wpdb->insert('p_assessment_rubric', [
            'group_id' => $_POST['id'],
            'listener_id' => $_POST['userid'],
            'create_user_id' => get_current_user_id(),
            'section_a_grade' => $_POST['section_a_grade'],
            'section_b_grade' => $_POST['section_b_grade'],
            'section_c_grade' => $_POST['section_c_grade'],
            'grading_solution' => $_POST['grading_solution'],
            'review' => $_POST['review']
        ],['%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s'])) alertStatus('success',"Форма добавлена!"); //else alertStatus('danger',"Код не добавлен!");
        echo $wpdb->print_error();
    }elseif( $_POST['action'] == "update" ){
        if($wpdb->update( 'p_assessment_rubric',
            [ 'section_a_grade' => $_POST['section_a_grade'],
                'section_b_grade' => $_POST['section_b_grade'],
                'section_c_grade' => $_POST['section_c_grade'],
                'grading_solution' => $_POST['grading_solution'],
                'review' => $_POST['review'],
                'date_update' => dateTime()
            ],
            [ 'group_id' => $_POST['id'], 'listener_id' => $_POST['userid'],'create_user_id' => get_current_user_id() ],
            [ '%d', '%d', '%d', '%d', '%s', '%s' ],
            [ '%d', '%d', '%d' ]
        )) alertStatus('success',"Форма обновлена!"); else alertStatus('danger',"Код не обновлен!");

    }else{
        alertStatus('danger',"Нет данных!");
    }

    echo'<meta http-equiv="refresh" content="0;url=/groups/?z=group&id='.$_POST['id'].'&tmModal=1&userid='.$_POST['userid'].'&moderatorid='.$_POST['moderatorid'].'&save_all=done" />';

}elseif( isset($_GET['id']) && isset($_GET['userid']) ) {
    $name_var = translateDir($_GET['id']);
    $gropinfo = groupInfo($_GET['id']);

    if($res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_GET['userid'], get_current_user_id() ))){//Оценка тимлидера
        $action = 'update';
    } elseif ($res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM  p_assessment_rubric WHERE group_id = %d AND listener_id = %d AND create_user_id = %d", $_GET['id'], $_GET['userid'], $gropinfo->moderator_id ))){//Оценка модератора
        $action = 'insert';
    }
if($_GET['save_all'] == 'done')alertStatus('success', 'Данные сохранены!');
?>
<form method="post" action="/assessment/?z=tm7">
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered no-margin">
                <tr>
                    <td><?= ASSESSMENT_RUBRIC[1] ?></td>
                    <td><select name="section_a_grade" class="form-control" required="" data-assessment aria-required="true" aria-invalid="false">
                            <option value="">&nbsp;</option>
                            <?php $grades = $wpdb->get_results("SELECT * FROM p_assessment_rubric_grade ") ?>
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
                <?= DECISION_MODERATION ?>
            </h4>
            <div class="form-group">
                <label for="textarea3"></label>
                <textarea  data-assessment name="review" id="textarea3" class="form-control" rows="6" required="" aria-required="true" aria-invalid="false" placeholder="<?= ($res->review) ? "" : NOT_FILLED ?>"><?= $res->review ?></textarea>
            </div>
            <div class="form-group">
                <h4>
                    <?= MODERATION_DECISION ?>:
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
    <button type="submit" class="btn btn-success"><?= SAVE_TEXT ?></button>
    <input type="hidden" name="action" value="<?= $action ?>">
    <input type="hidden" name="userid" value="<?= $_GET['userid'] ?>">
    <input type="hidden" name="moderatorid" value="<?= $gropinfo->moderator_id ?>">
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
</form>
<?php } ?>


