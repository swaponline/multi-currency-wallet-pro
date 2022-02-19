<?php
/**
 * Functions Pro
 * 
 * @package Envato API Functions
 */

/**
 * Hide admin tabs if no license.
 */
function mcwallet_admin_page_tabs_init( $tabs ){
	if ( ! get_option( 'mcwallet_purchase_code' ) ) {
		$tabs = array(
			'no-license' => esc_html__( 'No license', 'multi-currency-wallet' ),
		);
	}
	return $tabs;
}
add_filter( 'mcwallet_admin_page_tabs', 'mcwallet_admin_page_tabs_init' );

/**
 * Add content to no license admin tab.
 */
function mcwallet_admin_page_tab_custom( $content, $slug ){
	if ( 'no-license' === $slug ) {
		$license_page_url = admin_url( 'admin.php?page=mcwallet-license' );
		$content = '
			<div class="mcwallet-shortcode-panel-row">
				<h3>' . esc_html__( 'Please activate MCW plugin license', 'multi-currency-wallet' ) . '</h3>
				<p><a href="' . esc_url( $license_page_url ) . '" class="button button-primary">' . esc_html__( 'Go to license page', 'multi-currency-wallet' ) . '</a></p>
			</div>
		';
	}
	return $content;
}
add_filter( 'mcwallet_admin_page_tab', 'mcwallet_admin_page_tab_custom', 10, 2 );

/**
 * Add license info to global window variables.
 */
function mcwallet_window_variable_license( $variables ){
	$variables['licenceInfo'] = mcwallet_support_days_left();
	return $variables;
}
add_filter( 'mcwallet_window_variables', 'mcwallet_window_variable_license', 10, 2 );
