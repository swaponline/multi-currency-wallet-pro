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
function mcwallet_disable_if_no_license( $status ) {
	if ( ! get_option( 'mcwallet_purchase_code' ) ) {
		$status = true;
	}
	return $status;
}
add_filter( 'mcwallet_disable_desing_submenu', 'mcwallet_disable_if_no_license' );
add_filter( 'mcwallet_disable_banner', 'mcwallet_disable_if_no_license' );

/**
 * Update Admin Page Footer info.
 */
function mcwallet_info_bar_custom_content( $content ) {
	$filename = MCWALLET_PATH . 'multi-currency-wallet-pro.php';
	$update_time = gmdate( 'H\h : i\m : s\s', time() - filectime( $filename ) );
	$content = sprintf( esc_html__( 'Plugin version: %s | Build version: %s | Updated: %s ago.', 'multi-currency-wallet' ), MCWALLET_VER, MCWALLET_BUILD_VER, $update_time );
	return $content;
}
add_filter( 'mcwallet_info_bar_content', 'mcwallet_info_bar_custom_content' );
