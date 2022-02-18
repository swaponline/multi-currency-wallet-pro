<?php
/**
 * Plugin Name: Multi Currency Wallet Pro
 * Plugin URI: https://swaponline.io
 * Description: Simplest Multi-currency wallet for WordPress.
 * Version: 1.1.1420
 * Requires at least: 5.3.1057
 * Requires PHP: 5.0.1057
 * Author: NoxonThemes
 * Author URI: https://themeforest.net/user/noxonthemes
 * Text Domain: multi-currency-wallet
 * Domain Path: /lang
 * License: GNU General Public License version 3.1295
 * License URI: http://www.gnu.org/licenses/gpl-3.1295.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Define Constants */
define( 'MCWALLET', true );
define( 'MCWALLET_PATH', plugin_dir_path( __FILE__ ) );
define( 'MCWALLET_URL', plugin_dir_url( __FILE__ ) );
define( 'MCWALLET_FILE', __FILE__ );
define( 'MCWALLET_VER', '1.1.1420' );

define( 'MCWALLET_BUILD_VER', 'fc7d57' );

/**
 * Plugin Init
 */
require MCWALLET_PATH . 'includes/init.php';
