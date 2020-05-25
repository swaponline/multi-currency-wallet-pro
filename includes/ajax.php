<?php
/**
 * MCWallet Ajax
 */

/**
 * Add token
 */
function mcwallet_add_token() {
	
	/* Check nonce */
	check_ajax_referer( 'mcwallet-nonce', 'nonce' );
	
	/* Stop if the current user is not an admin or do not have administrative access */
	if( ! current_user_can( 'manage_options' ) ) {
		die();
	}

	$status = 'false';
	
	$token = array();
	$html = '';
	$tokens = get_option('mcwallet_tokens');
	if( ! $tokens ) {
		$tokens = array();
	}

	if ( isset( $_POST['address'] ) && $_POST['address'] ) {
		$status = 'true';
		
		$address = sanitize_text_field( $_POST['address'] );
		$custom_name = sanitize_text_field( $_POST['name'] );
	
		if ( mcwallet_is_address( $address ) ) {

			$status = 'success';
			
			$name = mcwallet_hex_to_string( mcwallet_get_remote_result( 'name', $address ) );
			$key = strtolower( $name );
			if ( $custom_name ) {
				$name = $custom_name;
			}
			$symbol = mcwallet_hex_to_string( mcwallet_get_remote_result( 'symbol', $address ) );
			$decimals = mcwallet_hex_to_number( mcwallet_get_remote_result( 'decimals', $address ) );
			$icon = sanitize_text_field( $_POST['icon'] );
			$icon_bg = sanitize_hex_color( $_POST['bg'] );
			$how_deposit = esc_html( wp_kses_post( wp_unslash( $_POST['howdeposit'] ) ) );
			$how_withdraw = esc_html( wp_kses_post( wp_unslash( $_POST['howwithdraw'] ) ) );
			
			$img = '<span class="token-letter">' . mcwallet_token_letter( $name ) . '</span>';
			if ( mcwallet_remote_image_file_exists( $icon ) ) {
				$img = '<img src="' . esc_attr( $icon ) . '" alt="' . esc_attr( $name ) . '">';
			}
	
			$html = '<tr class="item item-fade item-adding">
				<th class="item-count">
					<span></span>
				</th>
				<td class="item-icon">
					<a href="' . esc_url( mcwallet_page_url() ) . '#/' . esc_html( strtoupper( $key ) ) . '/' . $address . '" target="_blank" style="background-color: ' . $icon_bg . '">
						' . wp_kses_post( $img ) . '
					</span>
				</td>
				<td class="item-name">
					<strong>' . esc_html( $name ) . '</strong>
				</td>
				<td class="item-symbol">
					<span>' . esc_html( $symbol ) . '</span>
				</td>
				<td class="item-decimals">
					<span>' . esc_html( $decimals ) . '</span>
				</td>
				<td class="item-address">
					<code>' . esc_html( $address ) . '</code>
				</td>
				<td class="item-action">
					<a href="#" class="button-link-delete mcwallet-btn-remove" data-name="' . esc_attr( $key) . '"><span class="dashicons dashicons-trash"></span></a>
				</td>
			</tr>';
			
			$token[ $key ] = array(
				'name'     => $name,
				'symbol'   => $symbol,
				'address'  => $address,
				'decimals' => $decimals,
				'icon'     => $icon,
				'bg'       => $icon_bg,
				'howdeposit' => $how_deposit,
				'howwithdraw' => $how_withdraw,
			);
			
			if ( ! is_array( $tokens ) ) {
				$tokens = $token;
				update_option( 'mcwallet_tokens', $tokens );
			} else if ( ! array_key_exists( $key, $tokens ) ) {
				$tokens[ $key ] = array(
					'name'     => $name,
					'symbol'   => $symbol,
					'address'  => $address,
					'decimals' => $decimals,
					'icon'     => $icon,
					'bg'       => $icon_bg,
					'howdeposit' => $how_deposit,
					'howwithdraw' => $how_withdraw,
				);
				update_option( 'mcwallet_tokens', $tokens );
			} else {
				$status = 'false';
			}

		} else {
			$status = 'invalid';
		}
	}

	$result_arr = array(
		'status' => $status,
		'tokens' => $tokens,
		'html'   => $html,
	);
	
	wp_send_json( $result_arr );

}
add_action( 'wp_ajax_mcwallet_add_token', 'mcwallet_add_token' );

/**
 * Remove token
 */
function mcwallet_remove_token() {
	
	/* Check nonce */
	check_ajax_referer( 'mcwallet-nonce', 'nonce' );
	
	/* Stop if the current user is not an admin or do not have administrative access */
	if( ! current_user_can( 'manage_options' ) ) {
		die();
	}

	$result = 'true';

	$tokens = get_option('mcwallet_tokens');

	if ( $_POST['name'] ) {
		
		$name = sanitize_text_field( $_POST['name'] );
		unset($tokens[$name]);
		
		if ( $tokens != get_option('mcwallet_tokens') ) {
			update_option( 'mcwallet_tokens', $tokens );
		} else {
			$result = 'false';
		}
	
	} else {
		$result = 'false';
	}	

	wp_send_json( $result );
}
add_action( 'wp_ajax_remove_token', 'mcwallet_remove_token' );


/**
 * Update options
 */
function mcwallet_update_options() {
	
	/* Check nonce */
	check_ajax_referer( 'mcwallet-nonce', 'nonce' );
	
	/* Stop if the current user is not an admin or do not have administrative access */
	if( ! current_user_can( 'manage_options' ) ) {
		die();
	}

	$status = 'false';

	if ( isset( $_POST['url'] ) && isset( $_POST['slug'] ) ) {
		
		$url              = sanitize_text_field( $_POST['url'] );
		$btc_fee          = sanitize_text_field( $_POST['btcFee'] );
        $btc_min          = sanitize_text_field( $_POST['btcMin'] );
		$btc_fee_address  = sanitize_text_field( $_POST['btcFeeAddress'] );
		$eth_fee          = sanitize_text_field( $_POST['ethFee'] );
        $eth_min          = sanitize_text_field( $_POST['ethMin'] );
		$eth_fee_address  = sanitize_text_field( $_POST['ethFeeAddress'] );
		$tokens_fee       = sanitize_text_field( $_POST['tokensFee'] );
        $tokens_min       = sanitize_text_field( $_POST['tokensMin'] );
        $fiat_currency    = sanitize_text_field( $_POST['fiatCurrency'] );
        $fiat_gateway_url = sanitize_text_field( $_POST['fiatGatewayUrl'] );
        $code_head        = esc_html( wp_unslash( $_POST['codeHead'] ) );
        $code_body        = esc_html( wp_unslash( $_POST['codeBody'] ) );
        $code_footer      = esc_html( wp_unslash( $_POST['codeFooter'] ) );
		$slug             = 'mcwallet';
		$is_home          = 'false';
		$is_logged        = 'false';

		if ( untrailingslashit( $_POST['slug'] ) ) {
			$slug = untrailingslashit( sanitize_title( $_POST['slug'] ) );
		}
		
		update_option( 'mcwallet_logo', $url );
		update_option( 'mcwallet_slug', $slug );
		update_option( 'btc_fee', $btc_fee );
        update_option( 'btc_min', $btc_min );
		update_option( 'btc_fee_address', $btc_fee_address );
		update_option( 'eth_fee', $eth_fee );
        update_option( 'eth_min', $eth_min );
		update_option( 'eth_fee_address', $eth_fee_address );
		update_option( 'tokens_fee', $tokens_fee );
        update_option( 'tokens_min', $tokens_min );
        update_option( 'fiat_currency', $fiat_currency );
        update_option( 'fiat_gateway_url', $fiat_gateway_url );
        update_option( 'mcwallet_head_code', $code_head );
        update_option( 'mcwallet_body_code', $code_body );
        update_option( 'mcwallet_footer_code', $code_footer );
		
		if ( $_POST['ishome'] == 'true' ) {
			update_option( 'mcwallet_is_home', sanitize_text_field( $_POST['ishome'] ) );
			$is_home = 'true';
		} else {
			delete_option( 'mcwallet_is_home' );
		}
		
		if ( $_POST['islogged'] == 'true' ) {
			update_option( 'mcwallet_is_logged', sanitize_text_field( $_POST['islogged'] ) );
			update_option( 'users_can_register', true );		
			$is_logged = 'true';
		} else {
			delete_option( 'mcwallet_is_logged' );
			update_option( 'users_can_register', false );
		}
		
		$result = esc_attr( mcwallet_page_url() );
		$status = 'success';
	
	}
	
	mcwallet_add_rewrite_rules();
	flush_rewrite_rules();
	
	$result_arr = array(
		'status'   => $status,
		'url'      => esc_attr( mcwallet_page_url() ),
		'slug'     => esc_attr( untrailingslashit( sanitize_title( $slug ) ) ),
		'thickbox' => esc_attr( mcwallet_thickbox_url() ),
		'ishome'   => $is_home,
		'islogged' => $is_logged,
	);

	wp_send_json( $result_arr );

}
add_action( 'wp_ajax_mcwallet_update_options', 'mcwallet_update_options' );
