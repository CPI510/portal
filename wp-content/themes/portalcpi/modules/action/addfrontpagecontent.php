<?php
get_header();
?>
<?php
global $wpdb;
checkPermissons([1]);
//printAll($_POST);

//echo $_POST['editor1'];
$editor = base64_encode(str_replace('\"','',$_POST['editor1']));
$name_content = trim($_POST['name_content']);
//$name_content = base64_encode(trim($_POST['name_content']));
//$editor = str_replace('&', '&amp;', $_POST['editor1']);
//$editor = str_replace('&amp;nbsp;', '', $editor);
//echo $editor = str_replace('\"', '"', $editor);

if( isset($_POST['insert']) && $wpdb->insert('p_front_page', [
        'name_content' => $name_content,
        'ordercontent' => $_POST['ordercontent'],
        'content' => $editor,
        'user_id' => get_current_user_id()
],[
    '%s', '%d'
])){
    echo'<meta http-equiv="refresh" content="0;url=/action/?z=listfrontpage" />';
}elseif ( isset($_POST['update']) && $wpdb->update('p_front_page',[
        'ordercontent' => $_POST['ordercontent'],
        'name_content' => $name_content,
        'content' => $editor
    ],[
            'id' => $_POST['update']
    ],[
            '%d', '%s', '%s'
    ],[
            '%d'
    ])){
    echo'<meta http-equiv="refresh" content="0;url=/action/?z=listfrontpage" />';
}

$datacontent = $wpdb->get_row($wpdb->prepare('SELECT * FROM p_front_page WHERE id = %d', $_GET['idcontent']));
?>

    <div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Добавление контента</h1>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <div class="form-group">
                        <label>Порядок на странице</label>
                        <?php $ordersbusy = $wpdb->get_col('SELECT ordercontent FROM p_front_page ');
                        array_unshift($ordersbusy, NULL);
                        unset($ordersbusy[0]);?>

                        <select class="form-control" required name="ordercontent">
                            <?php for( $i=1; $i <= 30; $i++ ): ?>
                            <?php $key = array_search($i, $ordersbusy) ?>
                            <?php if($key != null){
                                    $disabledtxt = "disabled";
                                    $text = " (используется)";
                                }else{
                                    $disabledtxt = "";
                                    $text = "";
                                } ?>
                                <?php if($datacontent->ordercontent == $i): ?>
                                        <option value="<?= $i ?>" selected><?= $i ?> </option>
                                <?php else: ?>
                                        <option value="<?= $i ?>" <?= $disabledtxt ?>> <?= $i ?> <?= $text ?> </option>
                                <?php endif; ?>

                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Заголовок контента</label>
                        <input type="text" class="form-control" name="name_content" value="<?= str_replace('\"', '"', $datacontent->name_content) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Контент</label>
                        <textarea name="editor1" class="ckeditor" rows="10" cols="80" required>
                            <?= base64_decode($datacontent->content) ?>
                        </textarea>
                    </div>

                    <br>
                    <p><input type="submit" value="Сохранить" class="btn btn-success"></p>
                    <input type="hidden" name="<?= ( isset($datacontent->id) ) ? "update":"insert" ?>" value="<?= ( isset($datacontent->id) ) ? $datacontent->id : "1" ?>">
                </form>

                <script src="<?= bloginfo('template_url') ?>/assets/editor/ckeditor/ckeditor.js"></script>

            </div>
        </div>
    </div>

<script>
    // CKEDITOR.replace( 'editor1', {
    //     uiColor: '#14B8C4',
    //     width:['100%']
    //
    // });
    CKEDITOR.config.height = '800px';
</script>
<?php
get_footer();
?>

