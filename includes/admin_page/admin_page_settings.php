<?php
/**
 * Widget Register Settings
 */
function mcwallet_register_settings() {
	register_setting(
		'mcwallet',
		'mcwallet_tokens'
	);
}
add_action( 'admin_init', 'mcwallet_register_settings' );

/**
 * Widget Do Setting Sectios
 */
function mcwallet_do_settings_sections(){
	?>

	<h3><?php esc_html_e( 'Add new token', 'multi-currency-wallet' );?></h3>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'ERC20 contract address', 'multi-currency-wallet' );?></label>
				</th>
				<td>
					<input name="address" type="text" class="regular-text">
					<p class="description"><?php _e('Select from the list <a href="https://etherscan.io/tokens" target="blank">https://etherscan.io/tokens</a> or create own <a href="https://vittominacori.github.io/erc20-generator/" target="_blank">https://vittominacori.github.io/erc20-generator/</a>','multi-currency-wallet');?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'Token name', 'multi-currency-wallet' );?> (<?php esc_html_e( 'not required', 'multi-currency-wallet' );?>)</label>
				</th>
				<td>
					<input name="name" type="text" class="regular-text">
					<p class="description"><?php esc_html_e( 'If the field is empty then the token name will be substituted automatically', 'multi-currency-wallet' );?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'Icon image url', 'multi-currency-wallet' );?></label>
				</th>
				<td>
					<div class="mcwallet-form-inline">
						<input name="icon" type="text" class="large-text mcwallet-input-icon">
						<button class="button button-secondary mcwallet-load-icon"><?php esc_html_e( 'Upload icon', 'multi-currency-wallet');?></button>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'Icon Background Color', 'multi-currency-wallet' );?></label>
				</th>
				<td>
					<input name="color" class="mcwallet-icon-bg" type="text" value="">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'How To Deposit', 'multi-currency-wallet' );?></label>
				</th>
				<td>
					<?php
					$how_deposit_content = '';
					wp_editor( $how_deposit_content, 'howdeposit', array(
						'textarea_name' => 'howdeposit',
						'textarea_rows' => 10,
                        'quicktags'     => false
					) );
					?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'How To Withdraw', 'multi-currency-wallet' );?></label>
				</th>
				<td>
					<?php
					$how_deposit_content = '';
					wp_editor( $how_deposit_content, 'howwithdraw', array(
						'textarea_name' => 'howwithdraw',
						'textarea_rows' => 10,
                        'quicktags'     => false
					) );
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"></th>
				<td>
					<?php
						submit_button( esc_attr__( 'Add new token', 'multi-currency-wallet' ), 'primary mcwallet-add-token', 'mcwallet-add-token', false );
					?>
					<span class="spinner"></span>
				</td>
			</tr>
		</tbody>
	</table>

<?php
}
