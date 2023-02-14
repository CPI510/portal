<?php 
global $wpdb;
?>
<?php $result = $wpdb->get_row($wpdb->prepare( "SELECT `id`, `date_create`, `number_group`, `program_id`, `start_date`, `end_date`, `trener_id`, `training_center`, `active`  
FROM p_groups WHERE id = %d", $_GET['id'])); ?>	
<?php if($result && is_user_logged_in()): ?>
<?php printAll($_POST); ?>
<div class="card">
    <div class="card-head">
        <header>Card with actions</header>
    </div>
    <div class="card-body text-default-light">
        <p>Ad ius duis dissentiunt, an sit harum primis persecuti, adipisci tacimates mediocrem sit et. Id illud voluptaria omittantur qui, te affert nostro mel. Cu conceptam vituperata temporibus has.</p>
    </div><!--end .card-body -->
    <div class="card-actionbar">
        <div class="card-actionbar-row">
            <a href="javascript:void(0);" class="btn btn-flat btn-default ink-reaction">Cancel</a>
            <a href="javascript:void(0);" class="btn btn-flat btn-accent ink-reaction">Submit</a>
        </div>
    </div><!--end .card-actionbar -->
</div>
<?php else: ?>
<script>
document.location.href = "/registration/?id=<?= $_GET['id'] ?>" ;
</script>
<?php endif; ?>