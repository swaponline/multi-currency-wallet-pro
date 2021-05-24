<?php
/**
 * Envato API Functions
 */

/**
 * If Active License
 */
function mcwallet_is_active_license() {
	return true;
}




// wp_remote_retrieve_response_code().

function mcwallet_get_license_info( $code = null ){

	$url = 'https://wallet.wpmix.net/wp-json/license/info?code=' . $code;

	$response = wp_remote_get( $url,
		array(
			'headers' => array(
				'timeout' => 120,
			),
		)
	);

	$response = wp_remote_retrieve_body( $response );

	return json_decode( $response );

}



