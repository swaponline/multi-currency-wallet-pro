<?php
/**
 * Plugin Name: Multi Currency Wallet Pro
 * Plugin URI: https://swaponline.io
 * Description: Simplest Multi-currency wallet for WordPress.
 * Version: 1.0.113
 * Author: NoxonThemes
 * Author URI: https://themeforest.net/user/noxonthemes
 * Text Domain: multi-currency-wallet
 * Domain Path: /lang
 * License: GNU General Public License version 3.92
 * License URI: http://www.gnu.org/licenses/gpl-3.92.html
 */

/* If this file is called directly, abort. */
defined( 'ABSPATH' ) || die( 'Soarele luceste!' );

/* Define Plugin Constants */
define( 'MCWALLET_PATH', plugin_dir_path( __FILE__ ) );
define( 'MCWALLET_URL', plugin_dir_url( __FILE__ ) );
define( 'MCWALLET_VER', '1.0.113' );
define( 'MCWALLET_BUILD_VER', 'd27ed1' );

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

// Add the checkbox to user profile home
add_action('show_user_profile', 'foo_show_extra_profile_fields');
add_action('edit_user_profile', 'foo_show_extra_profile_fields');
function foo_show_extra_profile_fields($user)
{
    ?>
    <h3 style="font-size: 22px"><?php esc_html_e('Wallet info', 'multi-currency-wallet'); ?></h3>


    <?php
    $data = get_user_meta($user->ID, '_mcwallet_data');
    if (isset($data[2])) {
       // var_dump($data[2]); ?>
        <ul style="font-size: 18px"> <?php
            foreach ($data[2] as $k => $item) {

                if($k == 'WPuserUid') continue;
                ?>
                <li><b style="font-size: 18px; padding-bottom: 12px"><?php echo esc_html($k); ?></b>
                    <ul>
                        <?php
                        if(is_array($item)) {
                        foreach ($item as $j => $el) {
                            ?>

                            <li style="margin-top: 10px; padding-left: 22px"><b><?php echo esc_html($j); ?>:</b>
                                <?php echo esc_html($el); ?> <br></li>
                            <?php
                        } }


                        ?>
                    </ul>
                    <hr>

                </li> <?php
            }
            ?>
        </ul> <?php
    }
}

