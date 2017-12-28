<?php
define('WP_CACHE', true); // Added by WP Rocket
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'o,byVWQ6zYQCfU=Vv R<phN$9vX/Z=(jEs4]s@TmF%iqt BEhZ|jLix#{W&93= 1');
define('SECURE_AUTH_KEY',  '`pvnPNB(s$ t/7Hf OW#1@C_PHA~6,kC?!iU)jf@W?gg5Lht]d~ovC8YuuzfzZ?p');
define('LOGGED_IN_KEY',    '2)%2.6e[1f#MLJ.1>}BenWvcG/6w:{;whNdar_Z-N</ljYzEP?~j_,MT*Dau<rVj');
define('NONCE_KEY',        '4>@9dy2L.OPCffhdVrPmF$L%0.R-rjXtU)74AiKf[>^)C2VV|mbh-o3`Hs :>24H');
define('AUTH_SALT',        'tqGJI[{/!5s(tph^S BPH|QG=4iYJRx;3EX-M;~bM.umBd0_0h?_/61(qo+#$[l{');
define('SECURE_AUTH_SALT', 'C<qLJRytX!]xDZbtA@lA<N4{R_Ko{7W(RAbyB@02Z]ODtn,@z]A!%J@N02|dS2_c');
define('LOGGED_IN_SALT',   '`ww%Gsf%*%n^YL^B:;A6d1:(<~6!_ebj+=3AGrE;udJ H;WD{Brub!];0HZ#UEvS');
define('NONCE_SALT',       'gs{hMe1mdvA71<b6;]bN>_7|~,S`OvX3!Y|A$qc<=~U9Wj5R)((bE>`aHy^!,ll;');


/**#@-*/

define('WP_SITEURL','http://localhost:8888/wordpress');
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
//define('WP_DEBUG', false);
//define( 'WP_MEMORY_LIMIT', '256M' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
