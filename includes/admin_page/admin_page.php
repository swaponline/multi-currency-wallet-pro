<?php
/**
 * Multi Currency Wallet Menu Page
 */
function mcwallet_menu_page() {
	$menu_page = add_menu_page(
		esc_html__( 'Multi Currency Wallet', 'multi-currency-wallet' ),
		esc_html__( 'MCWallet', 'multi-currency-wallet' ),
		'manage_options',
		'mcwallet',
		'mcwallet_page',
		'dashicons-swap-logo',
		81
	);
}
add_action( 'admin_menu', 'mcwallet_menu_page' );

/**
 * Widget Page
 */
function mcwallet_page() {

	$erc20tokens = get_option('mcwallet_tokens');

?>

<div class="wrap">
	<h2><?php echo get_admin_page_title(); ?></h2>
	<div class="notice mcwallet-notice hide-all"><p></p></div>

	<div class="welcome-panel mcwallet-welcome-panel">
		<div class="welcome-panel-content">

			<h2 class="nav-tab-wrapper mcwallet-nav-tabs wp-clearfix">
				<a href="#mcwallet-tab-1" class="nav-tab nav-tab-active"><?php esc_html_e( 'Tokens list', 'multi-currency-wallet' ); ?></a>
				<a href="#mcwallet-tab-2" class="nav-tab"><?php esc_html_e( 'Options', 'multi-currency-wallet' ); ?></a>
				<a href="#mcwallet-tab-3" class="nav-tab"><?php esc_html_e( 'Custom HTML', 'multi-currency-wallet' ); ?></a>
				<a href="#mcwallet-tab-4" class="nav-tab"><?php esc_html_e( 'Strings Editor', 'multi-currency-wallet' ); ?></a>
			</h2><!-- .nav-tab-wrapper -->
			
			<div class="welcome-panel-column-container mcwallet-panel-tab panel-tab-active" id="mcwallet-tab-1">
				<div class="mcwallet-shortcode-panel-row">

					<table class="wp-list-table widefat striped wp-list-tokens">
						<thead>
							<tr>
								<td class="item-count">
									<span>#</span>
								</td>
								<td class="item-icon">
									<span><?php esc_html_e( 'Icon', 'multi-currency-wallet' ); ?></span>
								</td>
								<td class="item-name">
									<span><?php esc_html_e( 'Token name', 'multi-currency-wallet' ); ?></span>
								</td>
								<td class="item-symbol">
									<span><?php esc_html_e( 'Token symbol', 'multi-currency-wallet' ); ?></span>
								</td>
								<td class="item-decimals">
									<span><?php esc_html_e( 'Decimals', 'multi-currency-wallet' ); ?></span>
								</td>
								<td class="item-address">
									<span><?php esc_html_e( 'Contract address', 'multi-currency-wallet' ); ?></span>
								</td>
								<td class="item-echange-rate">
									<span><?php esc_html_e( 'Exchange Rate', 'multi-currency-wallet' ); ?></span>
								</td>
								<td class="item-action">
									<span><?php esc_html_e( 'Action', 'multi-currency-wallet' ); ?></span>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php if ( $erc20tokens ) { ?>
								<?php foreach( $erc20tokens as $name => $token ) {
									$img = '<span class="token-letter">' . mcwallet_token_letter( $token['name'] ) . '</span>';
									if ( mcwallet_remote_image_file_exists( $token['icon'] ) ) {
										$img = '<img src="' . esc_attr( $token['icon'] ) . '" alt="' . esc_attr( $name ) . '">';
									}
									$token_bg = '';
									if ( isset( $token['bg'] ) ) {
										$token_bg = $token['bg'];
									}
									$token_rate = '';
									if ( isset( $token['rate'] ) && $token['rate'] ) {
										$token_rate = $token['rate'];
									}
									if ( ! isset( $token['standard'] ) ) {
										$token['standard'] = 'erc20';
									}
								?>
								<tr class="item">
									<th class="item-count">
										<span></span>
									</th>
									<td class="item-icon">
										<a href="<?php echo esc_url( mcwallet_page_url() ) . '#/' . esc_url( $name );?>-wallet" target="_blank" style="background-color: <?php echo esc_attr( $token_bg ); ?>">
											<?php echo wp_kses_post( $img ); ?>
										</a>
									</td>
									<td class="item-name">
										<strong><?php echo esc_html( $token['name'] );?></strong>
									</td>
									<td class="item-symbol">
										<span><?php echo esc_html( $token['symbol'] );?></span>
									</td>
									<td class="item-decimals">
										<span><?php echo esc_html( $token['decimals'] );?></span>
									</td>
									<td class="item-address">
										<code><?php echo esc_html( $token['standard'] );?></code>
										<code><?php echo esc_html( $token['address'] );?></code>
									</td>
									<td class="item-exchange-rate">
										<span><?php echo esc_html( $token_rate );?></span>
									</td>
									<td class="item-action">
										<a href="#" class="button-link-delete mcwallet-remove-token" data-name="<?php echo esc_html( $name );?>"><span class="dashicons dashicons-trash"></span></a>
									</td>
								</tr>
							<?php } ?>
							<?php } else { ?>

							<?php } ?>
							<tr class="item item-empty">
								<td colspan="6">
									<span><?php esc_html_e( 'No tokens', 'multi-currency-wallet' );?></span>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td class="item-count">
									<span>#</span>
								</td>
								<td class="item-icon">
									<span><?php esc_html_e( 'Icon', 'multi-currency-wallet' );?></span>
								</td>
								<td class="item-name">
									<span><?php esc_html_e( 'Token name', 'multi-currency-wallet' );?></span>
								</td>
								<td class="item-symbol">
									<span><?php esc_html_e( 'Token symbol', 'multi-currency-wallet' );?></span>
								</td>
								<td class="item-decimals">
									<span><?php esc_html_e( 'Decimals', 'multi-currency-wallet' );?></span>
								</td>
								<td class="item-address">
									<span><?php esc_html_e( 'Сontract address', 'multi-currency-wallet' );?></span>
								</td>
								<td class="item-echange-rate">
									<span><?php esc_html_e( 'Exchange Rate', 'multi-currency-wallet' ); ?></span>
								</td>
								<td class="item-action">
									<span><?php esc_html_e( 'Action', 'multi-currency-wallet' );?></span>
								</td>
							</tr>
						</tfoot>
					</table><!-- .wp-list-tokens -->

				</div><!-- .mcwallet-shortcode-panel-row -->

				<div class="mcwallet-shortcode-panel-row">

					<form action="" class="wp-mcwallet-widget-form">
						<?php settings_fields( 'mcwallet' ); ?>
						<?php mcwallet_do_settings_sections();?>
					</form>

				</div><!-- .mcwallet-shortcode-panel-row -->

			</div><!-- .mcwallet-panel-tab -->

			<div class="welcome-panel-column-container mcwallet-panel-tab mcwallet-form-options" id="mcwallet-tab-2">
				<div class="mcwallet-shortcode-panel-row">

					<h3><?php esc_html_e( 'Options', 'multi-currency-wallet' );?></h3>

					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"></th>
								<td>
									<label for="mcwallet_use_testnet">
										<input name="use_testnet" type="checkbox" id="mcwallet_use_testnet" <?php checked( 'true', get_option( 'mcwallet_use_testnet' ) ); ?>>
										<?php esc_html_e( 'Use Testnet blockchain.', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Wallet front page title', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<input name="mcwallet_page_title" type="text" class="regular-text" value="<?php echo esc_attr( get_option( 'mcwallet_page_title', esc_html__( 'Hot Wallet with p2p exchange', 'multi-currency-wallet' ) ) );?>">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Logo url', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<div class="mcwallet-form-inline">
										<input name="logo_url" type="text" value="<?php echo esc_attr( mcwallet_logo_url() );?>" class="large-text mcwallet-input-logo">
										<button class="button button-secondary mcwallet-load-logo"><?php esc_html_e( 'Upload logo', 'multi-currency-wallet');?></button>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Dark Logo url', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<div class="mcwallet-form-inline">
										<input name="dark_logo_url" type="text" value="<?php echo esc_attr( mcwallet_dark_logo_url() );?>" class="large-text mcwallet-input-dark-logo">
										<button class="button button-secondary mcwallet-load-dark-logo"><?php esc_html_e( 'Upload logo', 'multi-currency-wallet');?></button>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Logo link', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<input name="logo_link" type="text" value="<?php echo esc_attr( get_option('mcwallet_logo_link', get_home_url( '/' ) ) );?>" class="large-text">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Permalink', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<code><?php echo esc_url( home_url('/') );?></code>
									<input name="page_slug" type="text" value="<?php echo esc_attr( mcwallet_page_slug() );?>" class="regular-text code mcwallet-page-slug" <?php disabled( get_option( 'mcwallet_is_home' ), 'true' ); ?>>
									<code>/</code>
									<a href="<?php echo mcwallet_page_url();?>" class="button mcwallet-button-url<?php if( get_option( 'mcwallet_is_home' ) ) { echo ' disabled';}?>" target="_blank"><?php esc_html_e( 'View page', 'multi-currency-wallet' );?></a>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Use as home page', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<label for="mcwallet_is_home">
										<input name="is_home" type="checkbox" id="mcwallet_is_home" value="true" <?php checked( 'true', get_option( 'mcwallet_is_home' ) ); ?>>
										<?php esc_html_e( 'Use Multi Currency Wallet template as home page.', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Wallet page access', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<label for="mcwallet_is_logged">
										<input name="is_logged" type="checkbox" id="mcwallet_is_logged" value="true" <?php checked( 'true', get_option( 'mcwallet_is_logged' ) ); ?>>
										<?php esc_html_e( 'Users must be registered and logged for access wallet.', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
              <tr>
								<th scope="row"></th>
								<td>
									<label for="mcwallet_btc_disabled">
										<input name="btc_disabled" type="checkbox" id="mcwallet_btc_disabled" <?php checked( 'true', get_option( 'mcwallet_btc_disabled' ) ); ?>>
										<?php esc_html_e( 'Disable BTC wallet.', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
              <tr>
								<th scope="row"></th>
								<td>
									<label for="mcwallet_eth_disabled">
										<input name="eth_disabled" type="checkbox" id="mcwallet_eth_disabled" <?php checked( 'true', get_option( 'mcwallet_eth_disabled' ) ); ?>>
										<?php esc_html_e( 'Disable ETH wallet.', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
              <tr>
								<th scope="row"></th>
								<td>
									<label for="mcwallet_ghost_enabled">
										<input name="ghost_enabled" type="checkbox" id="mcwallet_ghost_enabled" <?php checked( 'true', (!get_option('mcwallet_ghost_enabled')) ? 'true' : 'false' ); ?>>
										<?php esc_html_e( 'Disable GHOST wallet.', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
              <tr>
								<th scope="row"></th>
								<td>
									<label for="mcwallet_next_enabled">
										<input name="next_enabled" type="checkbox" id="mcwallet_next_enabled" <?php checked( 'true', (!get_option('mcwallet_next_enabled')) ? 'true' : 'false' ); ?>>
										<?php esc_html_e( 'Disable NEXT wallet.', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
              <tr>
								<th scope="row"></th>
								<td>
									<label for="mcwallet_exchange_disabled">
										<input name="exchange_disabled" type="checkbox" id="mcwallet_exchange_disabled" <?php checked( 'true', get_option( 'mcwallet_exchange_disabled' ) ); ?>>
										<?php esc_html_e( 'Disable exchange', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
              <tr>
								<th scope="row"></th>
								<td>
									<label for="mcwallet_invoice_enabled">
										<input name="invoice_enabled" type="checkbox" id="mcwallet_invoice_enabled" <?php checked( 'true', get_option( 'mcwallet_invoice_enabled' ) ); ?>>
										<?php esc_html_e( 'Enable Invoices', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
              <tr>
                <th scope="row"></th>
                <td>
                  <label for="mcwallet_remember_userwallet">
                    <input name="remeber_userwallet" type="checkbox" id="mcwallet_remember_userwallet" <?php checked( 'true', get_option( 'mcwallet_remember_userwallet' ) ); ?>>
                    <?php esc_html_e( "Save private information (keys, etc..) in user's profile (Custodial mode)", 'multi-currency-wallet' );?>
                  </label>
                </td>
              </tr>
						</tbody>
					</table><!-- .form-table -->

					<h3><?php esc_html_e( 'Transaction fees', 'multi-currency-wallet' );?> (<a target=_blank href="https://support.swaponline.io/docs/fpr-business/admin-fees-formula-wallet-only/">?</a>)</h3>

					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Bitcoin', 'multi-currency-wallet' );?></label>
								</th>
								<td>

									<input name="btc_fee" type="text" value="<?php echo esc_attr( get_option('btc_fee') );?>" class="tiny-text textright"> %, no less than <input name="btc_min" type="text" value="<?php echo esc_attr( get_option('btc_min') );?>" size="7" class="textright" placeholder="Enter Min. fee (ex. 0.0001)"> <?php esc_html_e( 'BTC', 'multi-currency-wallet' ); ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'BTC Adress where to collect fees', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<input name="btc_fee_address" type="text" class="regular-text" value="<?php echo esc_attr( get_option('btc_fee_address') );?>">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Ethereum', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<input name="eth_fee" type="text" value="<?php echo esc_attr( get_option('eth_fee') );?>" class="tiny-text textright"> %, no less than <input name="eth_min" type="text" value="<?php echo esc_attr( get_option('eth_min') );?>" size="7" class="textright" placeholder="Enter Min. fee (ex. 0.0001)"> <?php esc_html_e( 'ETH', 'multi-currency-wallet' ); ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Ethereum Adress where to collect fees', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<input name="eth_fee_address" type="text" class="regular-text" value="<?php echo esc_attr( get_option('eth_fee_address') );?>">
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Other tokens', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<input name="tokens_fee" type="text" value="<?php echo esc_attr( get_option('tokens_fee') );?>" class="tiny-text textright"> %, no less than <input name="tokens_min" type="text" value="<?php echo esc_attr( get_option('tokens_min') );?>" size="7" class="textright" placeholder="Enter Min. fee (ex. 0.0001)"> <?php esc_html_e( 'Tokens', 'multi-currency-wallet' ); ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Other tokens Adress where to collect fees', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<input type="text" value="<?php echo esc_attr( get_option('eth_fee_address') );?>" class="regular-text" disabled>
									<p class="description"><?php esc_html_e( 'Address the same as Ethereum', 'multi-currency-wallet' );?></p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Default fiat currency', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<select type="text" name="fiat_currency" class="regular-text">
										<?php foreach( mcwallet_get_valutes() as $key => $valute ) { ?>
											<option value="<?php echo esc_attr( $key ); ?>" <?php selected( get_option( 'fiat_currency', 'USD' ), $key ); ?>><?php echo esc_attr( $valute ); ?></option>
										<?php } ?>
									</select>
									<!-- https://noxon.wpmix.net/worldCurrencyPrices.php -->
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Fiat Gateway Url', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<input name="fiat_gateway_url" type="text" class="large-text" value="<?php echo esc_attr( get_option( 'fiat_gateway_url', 'https://itez.swaponline.io/?DEFAULT_FIAT={DEFAULT_FIAT}&locale={locale}&btcaddress={btcaddress}') );?>">
								</td>
							</tr>
						</tbody>
					</table><!-- .form-table -->

					<h3><?php esc_html_e( 'Custom Options', 'multi-currency-wallet' );?></h3>

					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Show "How it works" block', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<label for="mcwallet_show_howitworks">
										<input name="show_howitworks" type="checkbox" id="mcwallet_show_howitworks" value="true" <?php checked( 'true', get_option( 'show_howitworks' ) ); ?>>
										<?php esc_html_e( 'Show "How it works" block on Exchange page', 'multi-currency-wallet' );?>
									</label>
								</td>
							</tr>
              <tr>
                <th scope="row"></th>
                <td>
                  <label for="mcwallet_disable_footer">
                    <input name="disable_footer" type="checkbox" id="mcwallet_disable_footer" <?php checked( 'true', get_option( 'mcwallet_disable_footer' ) ); ?>>
                    <?php esc_html_e( "Hide footer", 'multi-currency-wallet' );?>
                  </label>
                </td>
              </tr>
							<tr>
								<th scope="row"></th>
								<td>
									<?php
										submit_button( esc_attr__( 'Update options', 'multi-currency-wallet' ), 'primary mcwallet-update-options', 'mcwallet-update-options', false );
									?>
									<span class="spinner"></span>
								</td>
							</tr>
						</tbody>
					</table><!-- .form-table -->

					<?php if ( mcwallet_show_admin_use() ) { ?>

						<hr>

						<h3><?php esc_html_e( 'For use', 'multi-currency-wallet' );?></h3>

						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row">
										<label><?php esc_html_e( 'Page url', 'multi-currency-wallet' ); ?></label>
									</th>
									<td>
										<input type="text" onfocus="this.select();" readonly="readonly" class="large-text mcwallet-page-url" value="<?php echo esc_attr( mcwallet_page_url() );?>">
										<p class="desciption"><em><?php esc_html_e( 'Direct link to widget page', 'multi-currency-wallet');?></em></p>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label><?php esc_html_e( 'Shortcode', 'multi-currency-wallet' ); ?></label>
									</th>
									<td>
										<input type="text" onfocus="this.select();" readonly="readonly" class="regular-text" value="[mcwallet_widget]">
										<p class="desciption"><em><?php esc_html_e( 'Copy and paste this shortcode in your page content.', 'multi-currency-wallet');?></em></p><br>
										<input type="text" onfocus="this.select();" readonly="readonly" class="regular-text" value="<?php echo esc_html( '<?php echo do_shortcode( \'[mcwallet_widget]\' );?>' );?>">
										<p class="desciption"><em><?php esc_html_e( 'Or add this code to the place of the template where you need to display widget.', 'multi-currency-wallet');?></em><br></p>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label><?php esc_html_e( 'Demo', 'multi-currency-wallet' );?></label>
									</th>
									<td>
										<a href="<?php echo mcwallet_thickbox_url();?>" class="button thickbox mcwallet-button-thickbox" title="<?php esc_attr_e( 'MCWallet Widget Demo', 'multi-currency-wallet' );?>"><?php esc_html_e( 'See Modal Widget Demo', 'mcwallet' );?></a>
										<a href="<?php echo mcwallet_page_url(); ?>" class="button mcwallet-button-url" target="_blank"><?php esc_html_e( 'View page', 'multi-currency-wallet' ); ?></a>
									</td>
								</tr>
							</tbody>
						</table><!-- .form-table -->

					<?php } ?>

				</div><!-- .mcwallet-shortcode-panel-row -->
			</div><!-- .mcwallet-panel-tab -->

			<div class="welcome-panel-column-container mcwallet-panel-tab mcwallet-form-options" id="mcwallet-tab-3">
				<div class="mcwallet-shortcode-panel-row">

					<h3><?php esc_html_e( 'Custom code', 'multi-currency-wallet' ); ?></h3>

					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Before close tag &lt;/head&gt;', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<textarea name="mcwallet_head_code" class="large-text" rows="10"><?php echo get_option( 'mcwallet_head_code' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'After open tag &lt;body&gt;', 'multi-currency-wallet' );?></label>
								</th>
								<td>
									<textarea name="mcwallet_body_code" class="large-text" rows="10"><?php echo get_option( 'mcwallet_body_code' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php esc_html_e( 'Before close tag &lt;/body&gt;', 'multi-currency-wallet' ); ?></label>
								</th>
								<td>
									<textarea name="mcwallet_footer_code" class="large-text" rows="10"><?php echo get_option( 'mcwallet_footer_code' ); ?></textarea>
								</td>
							</tr>
							<tr>
								<th scope="row"></th>
								<td>
									<?php
										submit_button( esc_attr__( 'Update options', 'multi-currency-wallet' ), 'primary mcwallet-update-options', 'mcwallet-update-options', false );
									?>
									<span class="spinner"></span>
								</td>
							</tr>
						</tbody>
					</table><!-- .form-table -->

				</div><!-- .mcwallet-shortcode-panel-row -->
			</div><!-- .mcwallet-panel-tab -->

			<div class="welcome-panel-column-container mcwallet-panel-tab mcwallet-form-options" id="mcwallet-tab-4">

				<div class="mcwallet-strings-section">
					<div class="mcwallet-strings-header">
						<h3><?php esc_html_e( 'Original String', 'multi-currency-wallet' ); ?></h3>
						<h3><?php esc_html_e( 'Replacement String', 'multi-currency-wallet' ); ?></h3>
					</div>

					<div class="mcwallet-strings-body">
						<?php
						$strings = get_option( 'mcwallet_strings');

						if ( $strings ) {
							foreach ( $strings as $key => $string ) {
								?>
							<div class="mcwallet-strings-row">
								<div class="mcwallet-string-col">
									<input type="text" name="<?php echo esc_attr( $key ); ?>" class="large-text mcwallet-string-input" value="<?php echo esc_attr( $string[0] ); ?>">
								</div>
								<div class="mcwallet-string-col">
									<input type="text" name="<?php echo esc_attr( $key ); ?>" class="large-text mcwallet-string-input" value="<?php echo esc_attr( $string[1] ); ?>">
								</div>
								<div class="mcwallet-string-action">
									<a href="#" class="button-link-delete mcwallet-remove-string"><span class="dashicons dashicons-trash"></span></a>
								</div>
							</div>
						<?php }
						} else { ?>
							<div class="mcwallet-strings-empty-row"><?php esc_html_e( 'no strings', 'multi-currency-wallet' ); ?></div>
						<?php } ?>
					</div>
					<div class="mcwallet-strings-footer">
						<span>
							<?php
								submit_button( esc_attr__( 'Update options', 'multi-currency-wallet' ), 'primary mcwallet-update-options', 'mcwallet-update-options', false );
							?>
							<span class="spinner"></span>
						</span>
						<button class="button button-secondary mcwallet-add-string"><?php esc_html_e( 'Add string', 'multi-currency-wallet' ); ?></button>
					</div>
					<div class="mcwallet-strings-info">
						<?php esc_html_e( 'How it works:',  'multi-currency-wallet' );?> <a href="https://youtu.be/NB1bvM7ZE3w" target="_blank">https://youtu.be/NB1bvM7ZE3w</a>
					</div>
				</div>

			</div><!-- .mcwallet-shortcode-panel-row -->
		</div><!-- .mcwallet-panel-tab -->

		<?php mcwallet_info_bar_markup(); ?>

	</div><!-- .welcome-panel-content -->

</div><!-- .welcome-panel -->

	<?php
}

/**
 * Widget Admin Page Settings
 */
require MCWALLET_PATH . 'includes/admin_page/admin_page_settings.php';

/**
 * Admin Page License
 */
//require MCWALLET_PATH . 'includes/admin_page/admin-page-license.php';
