<br><?php
$userID = get_current_user_id();
alertStatus('warning', 'Email не подтвержден!');
wp_logout();
$url = site_url();
echo "<meta http-equiv='refresh' content='0;url=$url/login/?veryficationerror=1&id=".$userID."' />";
exit();
?>