<?php
/**
 * Supported Chains
 * 
 * List of supported networks, used to create "Disable network" options.
 */
function mcwallet_supperted_chains() {
	return array(
		'btc'      => 'BTC',
		'eth'      => 'ETH',
		'bnb'      => 'BNB',
		'matic'    => 'MATIC',
		'arbitrum' => 'ARBITRUM',
		'xdai'     => 'XDAI'
	);
}
