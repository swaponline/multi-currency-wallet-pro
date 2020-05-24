<?php
/**
 * Plugin Name: TinyMCE Custom Class
 * Plugin URI: http://sitepoint.com
 * Version: 1.0
 * Author: Tim Carr
 * Author URI: http://www.n7studios.co.uk
 * Description: TinyMCE Plugin to wrap selected text in a custom CSS class, within the Visual Editor
 * License: GPL2
 */

function mcwallet_media_button( $editor_id ) {

	$howdeposit_text = '<h2>Transfer euro via your bank using this details:</h2><p>Bank: COMMERZBANK AG, D-60311 Frankfurt am Main, Germany<br>Account: 400800094501EUR<br>Description: {userAddress}</p>
	<p>Your payment will be processed in 24 hours. With any questions email us: <a href="mailto:payments@mydomain.com">payments@mydomain.com</a>';
	
	$howwithdraw_text = '<h2>Enter amount to withdraw in form below</h2><p>Enter account details and click "Request payment"<br>
Your payment will be processed in 24 hours<br>Email with any questions <a href="mailto:payments@mydomain.com">payments@mydomain.com</a>';

	if( $editor_id == 'howdeposit' ){
    	echo ' <a href="#" class="button insert-text-template" data-editor-id="howdeposit" data-text="' . esc_html( $howdeposit_text ) . '">' . esc_html__('Default Template: Euro bank transfer') . '</a>';
	}
	if( $editor_id == 'howwithdraw' ){
    	echo ' <a href="#" class="button insert-text-template" data-editor-id="howwithdraw" data-text="' . esc_html( $howwithdraw_text ) . '">' . esc_html__('Default Template: Simple form') . '</a>';
	}
}
add_action('media_buttons', 'mcwallet_media_button');
 