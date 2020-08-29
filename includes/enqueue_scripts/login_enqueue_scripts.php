<?php
/**
 * Login Enqueue Scripts
 */
function mcwallet_login_enqueue_scripts() {

	/* Register styles */
	wp_enqueue_style( 'mcwallet-login', MCWALLET_URL . 'assets/css/login.css', array( 'login' ), MCWALLET_VER . '-' . MCWALLET_BUILD_VER.'1' );

	$image = mcwallet_logo_url();
	$login_css = "
		:root {
			--mcwallet-login-logo: url({$image});
		}";
	wp_add_inline_style( 'login', $login_css );

}
add_action( 'login_enqueue_scripts', 'mcwallet_login_enqueue_scripts' );
