<?php
/**
 * FAQ
 */

/**
 * Register Post Type mcwallet_faq
 */
function mcwallet_faq_post_type() {

	$show_ui = true;
	if ( ! get_option( 'mcwallet_purchase_code' ) ) {
		$show_ui = false;
	}

	$labels = array(
		'name'                  => esc_html__( 'FAQ', 'multi-currency-wallet' ),
		'singular_name'         => esc_html__( 'Faq', 'multi-currency-wallet' ),
		'menu_name'             => esc_html__( 'FAQ', 'multi-currency-wallet' ),
		'name_admin_bar'        => esc_html__( 'FAQ', 'multi-currency-wallet' ),
		'all_items'             => esc_html__( 'All Faq', 'multi-currency-wallet' ),
		'add_new_item'          => esc_html__( 'Add New Faq', 'multi-currency-wallet' ),
		'add_new'               => esc_html__( 'Add New', 'multi-currency-wallet' ),
		'new_item'              => esc_html__( 'New Faq', 'multi-currency-wallet' ),
		'edit_item'             => esc_html__( 'Edit Faq', 'multi-currency-wallet' ),
		'update_item'           => esc_html__( 'Update Faq', 'multi-currency-wallet' ),
		'search_items'          => esc_html__( 'Search Faq', 'multi-currency-wallet' ),
		'not_found'             => esc_html__( 'Not found', 'multi-currency-wallet' ),
		'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'multi-currency-wallet' ),
	);
	$args = array(
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => $show_ui,
		'show_in_menu'          => false,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'post',
	);
	register_post_type( 'mcwallet_faq', $args );

}
add_action( 'init', 'mcwallet_faq_post_type' );

/**
 * Add page link to submenu
 */
function mcwallet_faq_menu_page() {
	if ( ! get_option( 'mcwallet_purchase_code' ) ) {
		return;
	}
	add_submenu_page(
		'mcwallet',
		esc_html__( 'FAQ', 'multi-currency-wallet' ),
		esc_html__( 'FAQ', 'multi-currency-wallet' ),
		'manage_options',
		'edit.php?post_type=mcwallet_faq',
		'',
		20
	);
}
add_action('admin_menu', 'mcwallet_faq_menu_page');
