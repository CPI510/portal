<?php 

add_filter('show_admin_bar', '__return_false');

function pageCreate($path){
    if($_GET['z'] && file_exists(get_template_directory() . $path . "{$_GET['z']}.php")) require_once(get_template_directory() . $path . "{$_GET['z']}.php"); ///modules/programs/
    else require_once(get_template_directory() . $path . "index.php");
}

function printAll($data){
    echo "<pre>"; print_r($data); echo "</pre>";
}

function alertStatus($status, $message, $redirect = false){
    echo '
        <div class="alert alert-' .  $status .'" role="alert">
            <strong>' . $message . '</strong>
        </div>'; //success info warning danger

        if($redirect){
            if($_SERVER['HTTP_REFERER']) $url = $_SERVER['HTTP_REFERER'];
            else $url = site_url();

            echo'<meta http-equiv="refresh" content="2;url=' . $url . '" />'; 
            exit();
        }
        
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
    return 'Не входи без надобности!'; //
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
    $res = $wpdb->get_row($wpdb->prepare("SELECT u.access, a.name_ru access_name_ru, a.name_kz access_name_kz FROM p_user_fields u LEFT JOIN p_access a ON u.access = a.id  WHERE u.user_id = %d", $id));
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
    $user = $wpdb->get_row($wpdb->prepare("SELECT surname, name, patronymic, user_id_attached, email FROM p_user_fields WHERE user_id = %d", $id));  
    
    if($num == '4') return $user->user_id_attached;
    elseif($num == '6') return $user->email;
    elseif($num == '5') return "{$user->surname} {$user->name} {$user->patronymic}";
    elseif($num == '3') echo "{$user->surname} {$user->name} {$user->patronymic}";
    elseif($num == '2') echo "{$user->name} {$user->surname}";
    elseif($num == '1') echo "{$user->surname}";
    else echo "Нет данных";
}

function userInfo($id){
    global $wpdb;
    if(isset($id))
    return $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_user_fields WHERE user_id = %d", $id));
    else return "Нет данных!";
}

function groupInfo($id) {
    global $wpdb;
    return $ResultsTr = $wpdb->get_row($wpdb->prepare("SELECT l.name_ru lang_name_ru, l.name_kz lang_name_kz, g.expert_id, g.moderator_id, g.teamleader_id, g.number_group, g.start_date, g.end_date, p.proforma_id, p.p_name, p.id program_id, u.surname, 
u.name, u.patronymic, t.name name_org, t.name_kaz name_org_kaz, g.trener_id, g.trener_date, g.admin_id, g.potok, g.expert_date, g.moderator_date, g.teamleader_date, g.independent_trainer_id, g.independent_trainer_date, g.lang_id
, e.surname expert_surname, e.name expert_name, e.patronymic expert_patronymic
, m.surname moderator_surname, m.name moderator_name, m.patronymic moderator_patronymic
, tm.surname teamleader_surname, tm.name teamleader_name, tm.patronymic teamleader_patronymic, p.single_file, p.name_kaz, g.deleted, g.active, g.program_subsection, p.mail_confirmation
    FROM p_groups g
    LEFT OUTER JOIN p_programs p ON p.id = g.program_id 
    LEFT OUTER JOIN p_lang l ON l.id = g.lang_id 
    LEFT OUTER JOIN p_user_fields u ON u.user_id = g.trener_id
    LEFT OUTER JOIN p_training_center t ON t.id = g.training_center 
    LEFT OUTER JOIN p_user_fields e ON e.user_id = g.expert_id
    LEFT OUTER JOIN p_user_fields m ON m.user_id = g.moderator_id
    LEFT OUTER JOIN p_user_fields tm ON tm.user_id = g.teamleader_id
    WHERE g.id = %d", $id));
}

if( isset($_POST['noGoToLogin'])){
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

    if($user_id == $userattached) return true;

    //echo "$user_id == $userattached / $trener";

    if($trener){

        $resTr = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, p.trener_id FROM p_groups_users g LEFT OUTER JOIN p_groups p ON p.id = g.id_group WHERE g.id_user = %d",$userattached ));

        foreach($resTr as $res){

            if($user_id == $res->trener_id) return true;
            //echo "$user_id == {$res->trener_id}";

            $user = $wpdb->get_row($wpdb->prepare("SELECT user_id_attached FROM p_user_fields WHERE user_id = %d",$user_id ));
            foreach(explode(',', $user->user_id_attached) as $uid){
                if($uid == $res->trener_id) return true;
                $userAtt = $wpdb->get_row($wpdb->prepare("SELECT user_id_attached FROM p_user_fields WHERE user_id = %d",$uid ));
                    
                foreach(explode(',', $userAtt->user_id_attached) as $suid){
                    if($suid == $res->trener_id) return true;
                }
                    
            } 

        }
    }else{

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

function getCourse($id, $field_name = "all"){
    global $wpdb;
    $courseData = $wpdb->get_row($wpdb->prepare("SELECT * FROM p_groups WHERE id = %d", $id));

    if($field_name == "all") return $courseData;
    else return $courseData->{$field_name};
}

function redirectBack(){
    if($_SERVER['HTTP_REFERER']) $url = $_SERVER['HTTP_REFERER'];
    else $url = site_url();
    echo'<meta http-equiv="refresh" content="0;url='.$url.'" />'; exit();
}

function forAdmin(){
    if(getAccess(get_current_user_id())->access != 1) {
        echo "<br>";
        alertStatus('warning', 'Доступ закрыт');

        if($_SERVER['HTTP_REFERER']) $url = $_SERVER['HTTP_REFERER'];
        else $url = site_url();

        echo'<meta http-equiv="refresh" content="2;url=' . $url . '" />'; 
        exit();
    }
}

function checkPermissons($ids){
    $var = 0;
    foreach ($ids as $id){
        if(getAccess(get_current_user_id())->access == $id) {
            $var = 1;
        }
    }
    if($var != 1){
        echo "<br>";
        alertStatus('warning', 'Доступ закрыт');

        if($_SERVER['HTTP_REFERER']) $url = $_SERVER['HTTP_REFERER'];
        else $url = site_url();

        echo'<meta http-equiv="refresh" content="2;url=' . $url . '" />';
        exit();
    }
}

function accessFile($fileUserId, $trener){

    global $wpdb;

    if($trener){

        $resTr = $wpdb->get_results($wpdb->prepare("SELECT g.id_group, g.id_user, p.trener_id FROM p_groups_users g LEFT OUTER JOIN p_groups p ON p.id = g.id_group WHERE g.id_user = %d",$fileUserId ));
        //printAll($resTr);
        //echo $fileUserId;

        foreach($resTr as $data){

            if(get_current_user_id() == $data->trener_id) return true;

        }
    }

    if($fileUserId == get_current_user_id()) return true; // создатель файла равен текущему пользователю
    
    $user = $wpdb->get_row($wpdb->prepare("SELECT user_id_attached FROM p_user_fields WHERE user_id = %d",get_current_user_id() )); //получаем массив закрепленных слушателей у текущего пользователя
    
    //printAll($user);

    foreach(explode(',', $user->user_id_attached) as $uid){

        //echo "$uid == $fileUserId";
                
        if($uid == $fileUserId) return true; // создатель файла равен закрепленному пользователю уровень 1

            $userAtt = $wpdb->get_row($wpdb->prepare("SELECT user_id_attached FROM p_user_fields WHERE user_id = %d",$uid ));
            
            foreach(explode(',', $userAtt->user_id_attached) as $suid){

                if($suid == $fileUserId) return true; // создатель файла равен закрепленному пользователю уровень 2

            }
    }

}

function user_veryfication_data() {
    $dataV = get_the_author_meta('mailveryfication', get_current_user_id());
    if ($dataV == "0"){
        $url = site_url();
        echo "<meta http-equiv='refresh' content='0;url=$url/registration/?z=veryfication' />";
    }
}
add_action( 'init', 'user_veryfication_data', 10, 2 );

function mailVerificationSend( $userID ){
    $userFIO = nameUser($userID,5);
    $userEmail = nameUser($userID,6);
    $courseID = getCourseIdStrMeta($userID);
    $linkCode = site_url() . "/registration/?id=$courseID&code=" . get_the_author_meta('randomstring', $userID);
    $messageText = "Здравствуйте $userFIO! 
<br><br>На портале ЦПИ был запрос на создание учетной записи с указанием Вашего адреса электронной почты.
<br><br>Для подтверждения новой учетной записи пройдите по следующему адресу:
<br><br><a href='$linkCode'>$linkCode</a>
<br><br>В большинстве почтовых программ этот адрес должен выглядеть как синяя ссылка, на которую достаточно нажать. Если это не так, просто скопируйте этот адрес и вставьте его в строку адреса в верхней части окна Вашего браузера.
<br><br>С уважением, администрация портала.";

    $attachments = ""; //array(WP_CONTENT_DIR . '/uploads/attach.zip');
    $headers = array(
        'From: Портал ЦПИ <portal@cpi.nis.edu.kz>',
        'content-type: text/html',
    );

    wp_mail($userEmail, 'Портал ЦПИ: повторное подтверждение учетной записи', $messageText, $headers, $attachments);
}

function mailVeryficationMeta( $userID ) {
    update_user_meta( $userID, 'mailveryfication', '1' );
}

function userRandomStrMeta( $userID, $courseID = 0 ) {
    update_user_meta( $userID, 'randomstring', generateRandomString() );
    update_user_meta( $userID, 'reg_course_id', $courseID );
}
function getRandomStrMeta( $userID ) {
    return get_the_author_meta('randomstring', $userID);
}
function getCourseIdStrMeta( $userID ) {
    return get_the_author_meta('reg_course_id', $userID);
}


function user_last_login( $user_login, $user ) {
    update_user_meta( $user->ID, 'last_login', dateTime() );
}
add_action( 'wp_login', 'user_last_login', 10, 2 );
function lastlogin($user_id) {
    $last_login = get_the_author_meta('last_login', $user_id);
    return $last_login;
}
add_shortcode('lastlogin','lastlogin');

function dateTime(){
    $tz = 'Asia/Almaty';
    $dt = new DateTime("now", new DateTimeZone($tz));
    return $currentDate = $dt->format("Y-m-d H:i:s");
}

function generateRandomString($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function accessUser($accessid) {
    if(is_page('groups') && $accessid == 5) {
        alertStatus('warning', 'Доступ закрыт!', true);
    }
}

function RDir( $path ) {
    // если путь существует и это папка
    if ( file_exists( $path ) AND is_dir( $path ) ) {
        // открываем папку
        $dir = opendir($path);
        while ( false !== ( $element = readdir( $dir ) ) ) {
            // удаляем только содержимое папки
            if ( $element != '.' AND $element != '..' )  {
                $tmp = $path . '/' . $element;
                chmod( $tmp, 0777 );
                // если элемент является папкой, то
                // удаляем его используя нашу функцию RDir
                if ( is_dir( $tmp ) ) {
                    RDir( $tmp );
                    // если элемент является файлом, то удаляем файл
                } else {
                    unlink( $tmp );
                }
            }
        }
        // закрываем папку
        closedir($dir);
        // удаляем саму папку
        if ( file_exists( $path ) ) {
            rmdir( $path );
        }
    }
}

function firstUpperStr($name){
    $first = mb_substr($name,0,1, 'UTF-8');//первая буква
    $last = mb_substr($name,1);//все кроме первой буквы
    $first = mb_strtoupper($first, 'UTF-8');
    $last = mb_strtolower($last, 'UTF-8');
    return $first.$last;
}

function sessionexpired(){
    get_header();
    echo '<br><div class="col-lg-12">
            <div class="card">
                <div class="card-body">';
    echo "Ваша сессия истекла повторите попытку авторизации.  <br> <a href='".site_url()."/login/' class='btn btn-info btn-xs'>Перейти на страницу авторизации</a>";
    echo '</div></div></div>';
    get_footer();
}

/* Filter Email Change Email Text */

function so43532474_custom_change_email_address_change( $email_change, $user, $userdata ) {

   $text_message = 'Здравствуйте, ###USERNAME###!

Это уведомление подтверждает, что ваш адрес email на портале ###SITENAME### успешно изменён на ###EMAIL###.

Это письмо было отправлено на ###EMAIL###

###SITENAME###

###SITEURL###

';

    $new_message_txt = __( $text_message );

    $email_change[ 'message' ] = $new_message_txt;

    return $email_change;

}
add_filter( 'email_change_email', 'so43532474_custom_change_email_address_change', 10, 3 );

function translateDir($id){

    if(groupInfo($id)->lang_id == 1){
        $lang = "kz";
        $name_var = "name_kaz";
    }else{
        $lang = "ru";
        $name_var = "name";
    }

    include_once(get_stylesheet_directory() . '/assets/lang/' . $lang . '.php');
//    echo get_stylesheet_directory() . '/assets/lang/' . $lang . '.php';
    return $name_var;
}

add_filter( 'gettext', 'filter_gettext_login_pass_error', 10, 3 );
function filter_gettext_login_pass_error( $translation, $text, $domain ) {
    if ( $text === '<strong>Error</strong>: The password you entered for the email address %s is incorrect.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = '<b>ОШИБКА</b>: Введённый вами пароль для адреса %s неверен.';
        }else{
            $translation = '<b>Қате:</b> %s адресі үшін Сіз енгізген құпия сөз дұрыс емес.';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_login_pass_forget', 10, 3 );
function filter_gettext_login_pass_forget( $translation, $text, $domain ) {
    if ( $text === 'Lost your password?' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Забыли пароль?';
        }else{
            $translation = 'Құпия сөзді ұмыттыңыз ба?';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_change_pass', 10, 3 );
function filter_gettext_change_pass( $translation, $text, $domain ) {
    if ( $text === 'Please enter your username or email address. You will receive an email message with instructions on how to reset your password.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Введите ваш адрес email. Вы получите email сообщение с инструкциями по сбросу пароля.';
        }else{
            $translation = 'Өзіңіздің Email адресіңізді енгізіңіз. Сіз құпия сөзді қалпына келтіру бойынша нұсқаулықтары бар email хатты аласыз';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_2', 10, 3 );
function filter_gettext_2( $translation, $text, $domain ) {
    if ( $text === 'Get New Password' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Получить новый пароль';
        }else{
            $translation = 'Жаңа құпия сөз алу';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_3', 10, 3 );
function filter_gettext_3( $translation, $text, $domain ) {
    if ( $text === 'Check your email for the confirmation link, then visit the <a href="%s">login page</a>.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Проверьте вашу почту для ссылки с подтверждением, затем зайдите на <a href="%s">страницу входа</a>';
        }else{
            $translation = 'Растау сілтемесі жіберілетін поштаңызды тексеріңіз, содан кейін <a href="%s">кіру бетіне</a> кіріңіз';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_4', 10, 3 );
function filter_gettext_4( $translation, $text, $domain ) {
    if ( $text === 'Remember Me' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Запомнить меня';
        }else{
            $translation = 'Мені есте сақтау';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_5', 10, 3 );
function filter_gettext_5( $translation, $text, $domain ) {
    if ( $text === 'Log In' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Войти';
        }else{
            $translation = 'Кіру';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_6', 10, 3 );
function filter_gettext_6( $translation, $text, $domain ) {
    if ( $text === 'Enter your new password below.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Введите свой новый пароль.';
        }else{
            $translation = 'Жаңа құпия сөз енгізіңіз.';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_7', 10, 3 );
function filter_gettext_7( $translation, $text, $domain ) {
    if ( $text === 'New password' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Новый пароль';
        }else{
            $translation = 'Жаңа құпия сөз.';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_8', 10, 3 );
function filter_gettext_8( $translation, $text, $domain ) {
    if ( $text === 'Your password reset link appears to be invalid. Please request a new link below.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Ваша ссылка для сброса пароля некорректна. Пожалуйста, запросите новую ссылку ниже.';
        }else{
            $translation = 'Құпия сөзді өшіру сілтемесі дұрыс емес. Төмендегі жаңа сілтемені сұраңыз.';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_9', 10, 3 );
function filter_gettext_9( $translation, $text, $domain ) {
    if ( $text === 'Hint: The password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Подсказка: Рекомендуется задать пароль длиной не менее двенадцати символов. Чтобы сделать его надёжнее, используйте буквы верхнего и нижнего регистра, числа и символы наподобие ! " ? $ % ^ & ).';
        }else{
            $translation = 'Кеңес: Құпия сөзді кемінде он екі таңбадан қою ұсынылады. Оны сенімді ету үшін жоғарғы және төменгі регистрдегі әріптерді, сандар мен таңбаларды қолданыңыз. " ? $ % ^ & ).';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_10', 10, 3 );
function filter_gettext_10( $translation, $text, $domain ) {
    if ( $text === 'Reset Password' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Задать пароль';
        }else{
            $translation = 'Құпия сөзді енгізу';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_11', 10, 3 );
function filter_gettext_11( $translation, $text, $domain ) {
    if ( $text === 'Confirm use of weak password' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Разрешить использование слабого пароля';
        }else{
            $translation = 'Әлсіз құпия сөз қолдануға рұқсат беру';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_12', 10, 3 );
function filter_gettext_12( $translation, $text, $domain ) {
    if ( $text === 'Your password reset link has expired. Please request a new link below.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Срок действия ссылки для сброса пароля истёк. Пожалуйста, запросите новую ссылку ниже.';
        }else{
            $translation = 'Құпия сөзді өшіру сілтемесінің жарамдылық мерзімі аяқталды. Төмендегі жаңа сілтемені сұраңыз.';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_13', 10, 3 );
function filter_gettext_13( $translation, $text, $domain ) {
    if ( $text === 'Unknown username. Check again or try your email address.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Неизвестный email. Перепроверьте ваш адрес email.';
        }else{
            $translation = 'Белгісіз электрондық пошта. Электрондық пошта мекенжайыңызды қайта тексеріңіз.';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_14', 10, 3 );
function filter_gettext_14( $translation, $text, $domain ) {
    if ( $text === '<strong>Error</strong>: The password field is empty.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = '<strong>ОШИБКА</strong>: Вы не ввели пароль.';
        }else{
            $translation = '<strong>ҚАТЕ</strong>: Сіз құпия сөзді енгізген жоқсыз.';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_15', 10, 3 );
function filter_gettext_15( $translation, $text, $domain ) {
    if ( $text === 'Unknown email address. Check again or try your username.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Неизвестный адрес email. Перепроверьте или попробуйте ваше имя пользователя.';
        }else{
            $translation = 'Белгісіз электрондық пошта мекенжайы. Пайдаланушы атыңызды екі рет тексеріңіз немесе көріңіз.';
        }
    }
    return $translation;
}

add_filter( 'gettext', 'filter_gettext_16', 10, 3 );
function filter_gettext_16( $translation, $text, $domain ) {
    if ( $text === 'Your password has been reset.' && $domain === 'default' ) {
        if($_SESSION['lang'] == 'ru'){
            $translation = 'Ваш новый пароль вступил в силу.';
        }else{
            $translation = 'Сіздің жаңа құпия сөзіңіз күшіне енді.';
        }
    }
    return $translation;
}



add_filter( 'retrieve_password_message', 'my_retrieve_password_message', 10, 4 );
function my_retrieve_password_message( $message, $key, $user_login, $user_data ) {

    if($_SESSION['lang'] == 'ru'){
        // Start with the default content.
        $site_name = 'Система для координации процедур оценивания "Центр Педагогических измерений"';
        $message = "Кто-то запросил сброс пароля для следующей учетной записи\r\n\r\n";
        /* translators: %s: site name */
        $message .= $site_name . "\r\n\r\n";
        /* translators: %s: user login */
        $message .= "Имя пользователя: $user_login \r\n\r\n";
        $message .= "Если произошла ошибка, просто проигнорируйте это письмо, и ничего не произойдет.\r\n\r\n";
        $message .= "Чтобы сбросить пароль, перейдите по следующей ссылке:\r\n\r\n";
    }else{
        // Start with the default content.
        $site_name = '"Педагогикалық өлшеулер орталығы" бағалау рәсімдерін координациялау жүйесі';
        $message = "Біреу келесі есептік жазба үшін құпия сөзді өшіруді сұрады \r\n\r\n";
        /* translators: %s: site name */
        $message .= $site_name . "\r\n\r\n";
        /* translators: %s: user login */
        $message .= "Пайдаланушының аты: $user_login \r\n\r\n";
        $message .= "Егер қате орын алса, бұл хатты елемеңіз, ештеңе болмайды.\r\n\r\n";
        $message .= "Құпия сөзді өшіру үшін келесі сілтемеге өтіңіз:\r\n\r\n";
    }

    $message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";


    /*
     * If the problem persists with this filter, remove
     * the last line above and use the line below by
     * removing "//" (which comments it out) and hard
     * coding the domain to your site, thus avoiding
     * the network_site_url() function.
     */
    // $message .= '<http://yoursite.com/wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ) . ">\r\n";

    // Return the filtered message.
    return $message;

}

add_filter ( 'retrieve_password_title', 'my_retrieve_password_subject_filter', 10, 1 );
function my_retrieve_password_subject_filter($old_subject) {
    // $old_subject is the default subject line created by WordPress.
    // (You don't have to use it.)

    if($_SESSION['lang'] == 'ru'){
        $subject = "Система для координации процедур оценивания «Центра педагогических измерений»: Новый пароль";
    }else{
        $subject = "Педагогикалық өлшеулер орталығы бағалау рәсімдерін үйлестіру жүйесі: Жаңа құпия сөз";
    }
    //$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    //$subject = sprintf( __('[%s] Password Reset'), $blogname );
    // This is how WordPress creates the subject line. It looks like this:
    // [Doug's blog] Password Reset
    // You can change this to fit your own needs.

    // You have to return your new subject line:
    return $subject;
}


function authAnoher($id){
    if($id != 1){
        if(get_current_user_id() == 1 || get_current_user_id() == 349 || get_current_user_id() == 1663 || get_current_user_id() == 340){
            nocache_headers();
            wp_clear_auth_cookie();
            wp_set_auth_cookie( $id );
        }
    }

}

function setRepeatersGroupName(){
    global $wpdb;
    $counter = $wpdb->get_var("SELECT COUNT(`program_assessment`) FROM p_groups WHERE `program_assessment` = 2");
    return "Повторное оценивание " . ($counter +1);
}