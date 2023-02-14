<?php 
global $wpdb;
?>

<?php $result = $wpdb->get_row($wpdb->prepare( "SELECT `id`, `date_create`, `number_group`, `program_id`, `start_date`, `end_date`, `trener_id`, `training_center`, `active`  
FROM p_groups WHERE id = %d", $_GET['id'])); ?>	

<?php if($result): ?>

<?php else: ?>

<?php endif; ?>