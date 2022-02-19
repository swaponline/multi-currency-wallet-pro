<?php
/**
 * Admin Page Pro
 * 
 * @package Envato API Functions
 */

/**
 * Admin Page License
 */
require MCWALLET_PATH . 'pro/admin-page-license.php';

/**
 * Disable Design page if no license
 */
function mcwallet_disable_desing_submenu_page( $status ) {
	if ( ! get_option( 'mcwallet_purchase_code' ) ) {
		$status = true;
	}
	return $status;
}
add_filter( 'mcwallet_disable_desing_submenu', 'mcwallet_disable_desing_submenu_page' );
