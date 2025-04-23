<?php
/**
 * Plugin Name: Multi Currency Wallet Pro
 * Plugin URI: https://swaponline.io
 * Description: Simplest Multi-currency wallet for WordPress.
 * Version: 1.2.7-rs
 * Requires at least: 5.3.1176
 * Requires PHP: 5.0.1176
 * Author: NoxonThemes
 * Author URI: https://themeforest.net/user/noxonthemes
 * Text Domain: multi-currency-wallet
 * Domain Path: /lang
 * License: GNU General Public License version 3.1414
 * License URI: http://www.gnu.org/licenses/gpl-3.1414.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'MCWALLET' ) ) {

	/* Define Constants */
	define( 'MCWALLET', true );
	define( 'MCWALLET_PATH', plugin_dir_path( __FILE__ ) );
	define( 'MCWALLET_URL', plugin_dir_url( __FILE__ ) );
	define( 'MCWALLET_FILE', __FILE__ );
	define( 'MCWALLET_VER', '1.2.7-rs' );
	define( 'MCWALLET_BUILD_VER', '4ba6ec' );
  
  
  define( 'MC_WALLET_USED_TOKEN_MODULE_STANDART' ,array('phi20_v2', 'fkw20', 'phpx20'));
	/**
	 * Plugin Init
	 */
	require MCWALLET_PATH . 'includes/init.php';

}
