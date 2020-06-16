<?php
/**
 * Load app scripts
 */

if ( version_compare( PHP_VERSION, '7.0.0' ) >= 0) {
	$path_levels = dirname( __FILE__, 6 ) . '/';
} else {
	$path_levels = dirname( __FILE__ ) . '../../../../../../';
}

require_once $path_levels . 'wp-load.php';

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
