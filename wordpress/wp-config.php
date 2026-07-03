<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'D?zOZ3!j0s0::mB1Nc#=u,<.DJt&OXf9Uk*cJ*s~.7V+qvq4D],#-_g6QeT>$8u&' );
define( 'SECURE_AUTH_KEY',  'L$*q+on)Mj!eYgSo,Z$9mwgNIbH eX:=bgt}Cf0C~-Tcb_BTO2tT(,ju5Q6j#.zh' );
define( 'LOGGED_IN_KEY',    'r.y=,#]L`/f=i_Y_h.p?g:C@#8^U.yVdlwrUynf5*8@gg(r[Viz9Q)eiEO4`, PK' );
define( 'NONCE_KEY',        '9=l8_tM21N}uJ*tx$aC<BlHq5nYwef!)E4Jr]TZ:Vu*,1_}TJ}OGPfcxwzfzbCI ' );
define( 'AUTH_SALT',        'c3?w$EC5O^,n;vdrxEp!N#B%;XyC5zO1#Qy~#bqqBVd?c$+h|#w_w8QVM&&A:o34' );
define( 'SECURE_AUTH_SALT', 'HJ/DCtvN>jJUPa:|oC}a5e]|%7tEC:%!**5Y<ix6=I14yV)tj,GxfOf1V nZK(tn' );
define( 'LOGGED_IN_SALT',   '$N>P^USCB*C7%+=}NB_MTGBQigynDz3P_j2vw|`62:nFNd+[e)@HgTPYJH#pNxvX' );
define( 'NONCE_SALT',       '+o=b%4fd&#5z{!N0t#K.00N,UIU>bI~-!:D1~wa#r0;[O@B_nvF/n!Ww]E1?z1(H' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
