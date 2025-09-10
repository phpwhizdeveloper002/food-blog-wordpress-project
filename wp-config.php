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
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         'x;2m]8Hfqtp~4@)OLA~{qw/;B@D:hzJuCL/A_P!vL}IX)Pou$dvT}.V=$%mUQZ>y' );
define( 'SECURE_AUTH_KEY',  '}7~:7^CrKK>vP>B&25ARy?*vc(.5)1IS4suS{X#FJlrer>p#=QC4/X/2d{,MZ#I]' );
define( 'LOGGED_IN_KEY',    '?zlY-%l-^hhPLAlo#K,~d#o.ZVO.ZqNa~Yv[C)KWx&pk}63:n8]c=m*wItmoT#)q' );
define( 'NONCE_KEY',        'A&!seyygAxQ_fuznIg8N]<oWZhgBVeScPu4J*d9n%%s?jp US^+th.zOK*l`h{^/' );
define( 'AUTH_SALT',        '1ge<Tth0}f_*M`:!IPg3_R=4 1der:n:$Bg]$GrG2Dv3`cfA03L;cwxDPRvtcUCt' );
define( 'SECURE_AUTH_SALT', ';Gl0Cp3+,K~+uq,OBTc0!,e<fH;zyo^KsAjoB.adWym]4dTdxX<g*J3jIxgwm7sP' );
define( 'LOGGED_IN_SALT',   'd3Wh?+_2JtHEZfEhC)+^:?%^~89[Jnw30O,Tsn1z33JRhAS!&@9T@!?qT)lD--C4' );
define( 'NONCE_SALT',       '?KZJgfvcu_0!1rF).&^I-X5Az6A :sH}N~0J;.Chn/}UOuO.z^hd:ZxNG:`nsmN-' );

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
