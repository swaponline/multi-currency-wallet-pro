<?php
/**
 * Add License page to submenu
 */
function mcwallet_license_submenu_page() {
	add_submenu_page(
		'mcwallet',
		esc_html__( 'License', 'multi-currency-wallet' ),
		esc_html__( 'License', 'multi-currency-wallet' ),
		'manage_options',
		'mcwallet-license',
		'mcwallet_license_page',
		2
	);
}
add_action('admin_menu', 'mcwallet_license_submenu_page');

/**
 * Widget Page
 */
function mcwallet_license_page() {

	$erc20tokens = get_option('mcwallet_tokens');

?>

<div class="wrap">
	<h2><?php echo get_admin_page_title(); ?></h2>
	<div class="notice mcwallet-notice hide-all"><p></p></div>

	<div class="welcome-panel mcwallet-welcome-panel">
		<div class="welcome-panel-content">
			<h3>License Activation</h3>
			<p>The active support gives access to the latest version from the developer's server. An expired license DOES NOT AFFECT the plugin's functionality</p>

			<form method="post">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label><?php esc_html_e( 'Purchase Code', 'multi-currency-wallet' );?></label>
							</th>
							<td>
								<input name="mcwallet_purchase_code" type="text" class="regular-text" value="<?php echo esc_attr( get_option( 'mcwallet_purchase_code' ) );?>" placeholder="00000000-0000-0000-0000-000000000000">
							</td>
						</tr>
						<tr>
						<th scope="row"></th>
						<td>
							<?php
								submit_button( esc_attr__( 'Activate License', 'multi-currency-wallet' ), 'primary', false );
							?>
						</td>
					</tr>
				</tbody>
			</table><!-- .form-table -->
		</form>

<pre><?php 


//67ae17cd-8cfc-46ff-979c-c1a866fce34b
$code = '';
if ( isset( $_POST['mcwallet_purchase_code'] ) ) {
	$code = $_POST['mcwallet_purchase_code'];
}


$result = mcwallet_get_license_info( $code );

//print_r($result);

// wp_remote_retrieve_response_code().

if ( isset( $result->sold_at ) ) {
	echo 'Sold at: ' . $result->sold_at . '<br>';
	echo 'License: ' . $result->license . '<br>';
	echo 'Support Until: ' . $result->supported_until . '<br>';
	echo 'Item ID: ' . $result->item->id . '<br>';
	echo 'Item URL: ' . $result->item->url . '<br>';

	
}

?></pre>

		</div>
	</div>
</div>

<?php
}
