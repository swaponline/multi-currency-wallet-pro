<?php
/**
 * Список поддерживаемых сетей, используется для создания опций "Отключить сеть"
 */
function mcwallet_supperted_chains() {
  return array(
    'btc'       => 'BTC',
    'eth'       => 'ETH',
    'bnb'       => 'BNB',
    'matic'     => 'MATIC',
    'arbitrum'  => 'ARBITRUM',
    'xdai'       => 'XDAI'
  );
}