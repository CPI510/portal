<?php
global $wpdb;
$_POST = json_decode(file_get_contents("php://input"), true);
//printAll($_POST);
if($_POST['code'] && $_POST['id']){

    $linktext = str_replace(" ", '', $_POST['linktext']);
    if( $_POST['dataid']  == "" ){
        if($wpdb->insert('p_assessment_coding_user', [
            'group_id' => $_POST['id'],
            'code' => $_POST['code'],

            'listener_id' => $_POST['fileuserdata'],
            'admin_id' => get_current_user_id()
        ],[ '%d', '%s', '%d', '%d'])) alertStatus('success',"Код добавлен!"); //else alertStatus('danger',"Код не добавлен!");
    }elseif( $_POST['dataid'] != "" ){
        if($wpdb->update( 'p_assessment_coding_user',
            [ 'code' => $_POST['code'] ],
            [ 'group_id' => $_POST['id'], 'listener_id' => $_POST['fileuserdata'] ],
            [ '%s'],
            [ '%d', '%d' ]
        )) alertStatus('success',"Код обновлен!"); //else alertStatus('danger',"Код не обновлен!");
    }else{
        alertStatus('danger',"Нет данных!");
    }

}else{


?>
<div class="boxcoding"></div>

    <?php $res = $wpdb->get_row($s=$wpdb->prepare("SELECT * FROM p_assessment_coding_user WHERE group_id = %d AND listener_id = %d", $_GET['id'], $_POST['fileuserdata'] )); ?>
<div class="card">
    <div class="card-body"><h3><?= nameUser($_POST['fileuserdata'], 5) ?></h3>
        <div class="form-group">
            <label for="textarea3">Код</label>
            <input type="text" name="code" class="form-control" data-assessment required value="<?=$res->code?>" placeholder="<?= ($res->code) ? "" : "Не заполнено" ?>">
        </div>


            <input type="hidden" name="id" class="form-control" data-assessment value="<?=$_GET['id']?>">
            <input type="hidden" name="dataid" class="form-control" data-assessment value="<?=$res->id?>">
            <input type="hidden" name="link" class="form-control" data-assessment value="assessment/?z=coding">

    </div><!--end .card-body -->
</div><!--end .card -->

<input type="submit" class="btn btn-success" value="Сохранить">

<?php }