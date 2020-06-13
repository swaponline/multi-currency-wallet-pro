<?php
/**
 * Load app scripts
 */

require_once dirname( __FILE__, 6 ) . '/wp-load.php';

$path = MCWALLET_PATH . 'vendors/swap/app.js';

$app  = file_get_contents( $path );

$strings = get_option( 'mcwallet_strings' );

if ( $strings ) {
	foreach ( $strings as $string ) {
		$key                  = '"' . $string[0] . '"';
		$value                = '"' . $string[1] . '"';
		$replacements[ $key ] = $value;
	}
	if ( $replacements ) {
		$app = str_replace( array_keys( $replacements ), $replacements, $app );
	}
}

header( 'Content-Type: application/javascript; charset=UTF-8' );

echo $app;

exit;
