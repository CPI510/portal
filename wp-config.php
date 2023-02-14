<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'cpinis' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'portaluser' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', '35666500++' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'jhI(O21TX}7WZgY@gR](pyU#%v:BGVH(?OJLA`@MAHi+RU&Tc8#fT&8tuglA~rFy' );
define( 'SECURE_AUTH_KEY',  '5E7)G#lGP^i;s>ND7vlA,kR1+)vVj9KUDJ-R`|YLV}.>Fo1B`jLqoAs]NKO=T55w' );
define( 'LOGGED_IN_KEY',    'YEp5dC;D!Xl2.Y<]baw*W]O|1<SYHU05~kH78R]ikr,7RKY<A*|m/5U4UZ]Pv-Sk' );
define( 'NONCE_KEY',        '*ZZ7=*<Oyzqc@lb!i$.LR0A:T$1$&Bd%`HfbUDbTwT-qdf;sQZAr^:@eu`#}RDsY' );
define( 'AUTH_SALT',        '8IO}Y}Ls%%~~9HU.m|Thb13N)|KPTTAlf4I#(k*NOVAR9kp:iSD-?&803Xqt^##J' );
define( 'SECURE_AUTH_SALT', 'Wwd=E0vt5e&lg3z$Um<!Y56`Cx.q)`2~=y@To^z28#i^h=J,00uc|aKG8Y;nFrBg' );
define( 'LOGGED_IN_SALT',   'sh$/|bA6mB}]y~0#GVNSU5W<wrC+n7XmCC0.[=)ZxF[v_4m CMbcpn{+St|LAo:I' );
define( 'NONCE_SALT',       '<r f~~@R3@fcI-5u():7fvIG#M0T8p%[26XE$JC4u`Nt=c|TO_5m:n4|K7K7T{($' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false );
define('FS_METHOD', 'direct');
define('ALLOW_UNFILTERED_UPLOADS', true);
/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
