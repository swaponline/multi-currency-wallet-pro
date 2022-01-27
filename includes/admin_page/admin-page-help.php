<?php
/**
 * Add Help page to submenu
 */

function mcwallet_help_submenu_page() {
	add_submenu_page(
		'mcwallet',
		esc_html__( 'Helping links', 'multi-currency-wallet' ),
		esc_html__( 'Help', 'multi-currency-wallet' ),
		'manage_options',
		'mcwallet-help',
		'mcwallet_help_page',
		10
	);
}
add_action( 'admin_menu', 'mcwallet_help_submenu_page' );

/**
 * Page
 */
function mcwallet_help_page() {

?>

<div class="wrap">
	<h2><?php echo get_admin_page_title(); ?></h2>
	<div class="mcwallet-welcome-panel">
		<div class="mcwallet-welcome-panel-content">

			<h3><a href="https://support.swaponline.io/" target="_blank" id="mcwallet_open_help">https://support.swaponline.io/</a> - most common questions</h3>
			<h3><a href="https://discord.gg/fcs8u9jm5P" target="_blank" id="mcwallet_open_help">https://discord.gg/fcs8u9jm5P</a> - ask the community!</h3>
			<h3><a href="https://t.me/swaponlinebot/" target="_blank" id="mcwallet_open_help">https://t.me/swaponlinebot</a> - contact team if you have another question</h3>
			<h3>Are you familar with GitHub? <a href="https://github.com/swaponline/MultiCurrencyWallet/issues/" target="_blank" id="mcwallet_open_help">Create an issue</a></h3>

		</div>
	</div>
</div>

<?php
}
