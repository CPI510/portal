<?php 

add_filter('show_admin_bar', '__return_false');

function pageCreate($path){
    if($_GET['z'] && file_exists(get_template_directory() . $path . "{$_GET['z']}.php")) require_once(get_template_directory() . $path . "{$_GET['z']}.php"); ///modules/programs/
    else require_once(get_template_directory() . $path . "index.php");
}

function printAll($data){
    echo "<pre>"; print_r($data); echo "</pre>";
}

function alertStatus($status, $message){
    echo '
        <div class="alert alert-' .  $status .'" role="alert">
            <strong>' . $message . '</strong>
        </div>'; //success info warning danger
} 

function only_admin()
{
    if ( ! current_user_can( 'manage_options' ) && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'] ) {
        wp_redirect( site_url() );
    }
}
add_action( 'admin_init', 'only_admin', 1 );

function alter_login_headerurl() {
    return '';
}
add_action('login_headerurl','alter_login_headerurl');

function alter_login_headertitle() {
    return 'Не входи без надобности!'; //здесь изменяем на свой title
}
add_action('login_headertitle','alter_login_headertitle');

/*function my_login_logo() { ?>
    <style type="text/css">
        .login form, .login #login_error, .login .message, .login .success {
            border-radius: 15px;
        }
        body.login div#login h1 a {
            background-image: url(<?php echo get_bloginfo('template_directory'); ?>/images/logo_cpi1.jpeg) !important;
            background-size: auto;
            width: auto;}
        body {
            background: #e3f3e9 !important;
        }
        .login #backtoblog a, .login #nav a {
            padding: 2px;
            border-radius: 5px;
            background-color: snow;
        }</style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );*/

add_filter('pre_site_transient_update_core',create_function('$a', "return null;"));
wp_clear_scheduled_hook('wp_version_check');

function getAccess($id){
    global $wpdb;
    $res = $wpdb->get_row($wpdb->prepare("SELECT u.access, a.name access_name FROM p_user_fields u LEFT JOIN p_access a ON u.access = a.id  WHERE u.user_id = %d", $id));
    if($res) return $res;
    else return false;
}

function authAll(){
    if(!is_user_logged_in()) {
        auth_redirect();
    }
}

function nameUser($id, $num = '3'){
    global $wpdb;
    $user = $wpdb->get_row($wpdb->prepare("SELECT surname, name, patronymic, user_id_attached FROM p_user_fields WHERE user_id = %d", $id));   
    if($num == '4') return $user->user_id_attached;
    elseif($num == '3') echo "{$user->surname} {$user->name} {$user->patronymic}";
    elseif($num == '2') echo "{$user->name} {$user->surname}";
    elseif($num == '1') echo "{$user->surname}";
    else echo "Нет данных";
}

if( $_POST['noGoToLogin']){
    function my_front_end_login_fail( $username ) { 

        $referrer = $_SERVER['HTTP_REFERER'];  // откуда пришел запрос

        // Если есть referrer и это не страница wp-login.php
        if( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
            wp_redirect( add_query_arg('login', 'failed', $referrer ) );  // редиркетим и добавим параметр запроса ?login=failed
            exit;
        }
    }
    add_action( 'wp_login_failed', 'my_front_end_login_fail' );
}


function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function userAttach($userattached, $trener = false, $user_id = false){

    global $wpdb;

    $user_id = ($user_id) ? $user_id : get_current_user_id();
    if(getAccess($user_id)->access == 1) return true;

    if($trener){

        $resTr = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, p.trener_id FROM p_groups_users g LEFT OUTER JOIN p_groups p ON p.id = g.id_group WHERE g.id_user = %d",$userattached ));

        foreach($resTr as $res){

            $user = $wpdb->get_row($wpdb->prepare("SELECT user_id_attached FROM p_user_fields WHERE user_id = %d",get_current_user_id() ));
            foreach(explode(',', $user->user_id_attached) as $uid){
                if($uid == $res->trener_id) return true;
                $userAtt = $wpdb->get_row($wpdb->prepare("SELECT user_id_attached FROM p_user_fields WHERE user_id = %d",$uid ));
                    
                foreach(explode(',', $userAtt->user_id_attached) as $suid){
                    if($suid == $res->trener_id) return true;
                }
                    
            } 

        }
    }else{

        if($user_id == $userattached) return true;

        else{
            $user = $wpdb->get_row($wpdb->prepare("SELECT user_id_attached FROM p_user_fields WHERE user_id = %d",$user_id ));

            foreach(explode(',', $user->user_id_attached) as $uid){
                
                if($uid == $userattached) return true;

                    $userAtt = $wpdb->get_row($wpdb->prepare("SELECT user_id_attached FROM p_user_fields WHERE user_id = %d",$uid ));
                    
                    foreach(explode(',', $userAtt->user_id_attached) as $suid){
                        if($suid == $userattached) return true;
                    }
            } 
        }
    }

    

}

function redirectBack(){
    if($_SERVER['HTTP_REFERER']) $url = $_SERVER['HTTP_REFERER'];
    else $url = site_url();
    echo'<meta http-equiv="refresh" content="0;url='.$url.'" />'; exit();
}