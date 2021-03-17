<?php
/**
 * Plugin Name: Multi Currency Wallet Pro
 * Plugin URI: https://swaponline.io
 * Description: Simplest Multi-currency wallet for WordPress.
 * Version: 1.1.831
 * Requires at least: 5.4.688
 * Requires PHP: 5.692
 * Author: NoxonThemes
 * Author URI: https://themeforest.net/user/noxonthemes
 * Text Domain: multi-currency-wallet
 * Domain Path: /lang
 * License: GNU General Public License version 3.808
 * License URI: http://www.gnu.org/licenses/gpl-3.808.html
 */

/* If this file is called directly, abort. */
defined( 'ABSPATH' ) || die( 'Soarele luceste!' );

/* Define Plugin Constants */
define( 'MCWALLET_PATH', plugin_dir_path( __FILE__ ) );
define( 'MCWALLET_URL', plugin_dir_url( __FILE__ ) );
define( 'MCWALLET_VER', '1.1.831' );
define( 'MCWALLET_BUILD_VER', '31d9a2' );

/**
 * Run function if plugin active
 */
function mcwallet_plugin_active() {
	return true;
};

/**
 * Plugin Init
 */
require MCWALLET_PATH . 'includes/init.php';

/**
 * On activation plugin
 */
function mcwallet_register_activation_hook() {
	mcwallet_add_rewrite_rules();
	flush_rewrite_rules();
	mcwallet_add_default_token();
	mcwallet_add_default_banners();
	mcwallet_update_version();
}
register_activation_hook( __FILE__, 'mcwallet_register_activation_hook' );

/**
 * Load the plugin text domain for translation.
 */
function mcwallet_load_plugin_textdomain() {
	load_plugin_textdomain( 'multi-currency-wallet', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'mcwallet_load_plugin_textdomain' );
