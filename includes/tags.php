<?php
/**
 * Template tags
 */

/**
 * Register Post Type mcwallet_banner
 */
function mcwallet_info_bar_markup() {
	?>
	<div class="mcwallet-info-bar">
		<?php
			$filename = MCWALLET_PATH . 'multi-currency-wallet-pro.php';
			$update_time = gmdate( 'H\h : i\m : s\s', time() - filectime( $filename ) );
			printf( esc_html__( 'Plugin version: %s | Build version: %s | Updated: %s ago.', 'multi-currency-wallet' ), MCWALLET_VER, MCWALLET_BUILD_VER, $update_time );
		?>
	</div>
	<?php
}
