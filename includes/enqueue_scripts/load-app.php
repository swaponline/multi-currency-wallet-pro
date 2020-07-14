<?php
/**
 * Load app scripts
 */

if ( version_compare( PHP_VERSION, '7.0.0' ) >= 0) {
	$path_levels = dirname( __FILE__, 6 ) . '/';
} else {
	$path_levels = dirname( __FILE__ ) . '../../../../../../';
}

$version    = md5( ( isset( $_GET['ver'] ) ) ? $_GET['ver'] : 'no' );
$cache_file = 'wp-content/uploads/swap-wallet-app-' . $version . '.js';

require_once $path_levels . 'wp-load.php';

if ( file_exists($path_levels . $cache_file) ) {
	header( "Cache-control: public" );
	header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + 60*60*24 ) . " GMT" );
	header( 'Content-Type: application/javascript; charset=UTF-8' );
	echo file_get_contents( $path_levels . $cache_file );

} else {
	$path = MCWALLET_PATH . 'vendors/swap/app.js';

	$app  = file_get_contents( $path );

	$strings = get_option( 'mcwallet_strings' );

	if ( $strings ) {
		$replacements = array();
		foreach ( $strings as $string ) {
			if ( isset( $string[0] ) && isset( $string[1] ) ) {
				$key                  = '"' . $string[0] . '"';
				$value                = '"' . $string[1] . '"';
				$replacements[ $key ] = $value;
			}
		}
		if ( $replacements ) {
			$app = str_replace( array_keys( $replacements ), $replacements, $app );
		}
	}

	file_put_contents($path_levels . $cache_file, $app);

	header("Cache-control: public");
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + 60*60*24) . " GMT");
	header( 'Content-Type: application/javascript; charset=UTF-8' );

	echo $app;
}
exit;
