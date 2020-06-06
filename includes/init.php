<?php
/**
 * Multi Currency Wallet Init
 */

/**
 * Wallet Functions
 */
require MCWALLET_PATH . 'includes/functions.php';

/**
 * Etherscan api functions
 */
require MCWALLET_PATH . 'includes/etherscan_api.php';

/**
 * Multi Currency Wallet Admin Page
 */
require MCWALLET_PATH . 'includes/admin_page/admin_page.php';

/**
 * Ajax
 */
if ( wp_doing_ajax() ) {
	require MCWALLET_PATH . 'includes/ajax.php';
}

/**
 * Tags
 */
require MCWALLET_PATH . 'includes/tags.php';

/**
 * Widget Admin Enqueue Scripts
 */
require MCWALLET_PATH . 'includes/enqueue_scripts/admin_enqueue_scripts.php';

/**
 * Widget Enqueue Scripts
 */
require MCWALLET_PATH . 'includes/enqueue_scripts/wp_enqueue_scripts.php';

/**
 * Actions
 */
require MCWALLET_PATH . 'includes/actions.php';

/**
 * Widget Shortcode
 */
require MCWALLET_PATH . 'includes/shortcode/shortcode.php';

/**
 * Custom Tinymce
 */
require MCWALLET_PATH . 'includes/tinymce.php';

/**
 * Banners
 */
require MCWALLET_PATH . 'includes/banners.php';
