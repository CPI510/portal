<?php
get_header();
global $wpdb;
checkPermissons([1]);
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="text-primary">Список контента на главной странице</h1>
    </div>
    <div class="col-lg-12">
        <a href="/action/?z=addfrontpagecontent" class="btn btn-success">Добавить</a>
    </div><!--end .col -->
</div>
<hr>
<?php $allcontents = $wpdb->get_results("SELECT * FROM p_front_page ORDER BY ordercontent") ?>

<div class="row">
    <div id="accordion">
        <?php foreach ($allcontents as $allcontent): ?>
        <?php ++$q ?>
            <div class="card">
                <div class="card-header" id="heading<?=$q?>">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?=$q?>" aria-expanded="true" aria-controls="collapse<?=$q?>">
                            <?= str_replace('\"', '"', $allcontent->name_content) ?>
                        </button>
                    </h5>
                </div>

                <div id="collapse<?=$q?>" class="collapse" aria-labelledby="heading<?=$q?>" data-parent="#accordion">
                    <div class="card-body">
                        <a href="/action/?z=addfrontpagecontent&idcontent=<?= $allcontent->id ?>" class="btn btn-info btn-xs">Редактировать</a><hr>
                        <?= base64_decode($allcontent->content) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
get_footer();
?>