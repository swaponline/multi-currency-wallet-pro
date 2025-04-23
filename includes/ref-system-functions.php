<?php
function wp_mcw_refsystem_register_new_user_action( $user_id ){
  $referral_id = isset($_POST['referral']) ? intval($_POST['referral']) : false;
  update_user_meta( $user_id, '_mcwallet_referral_id', [
    'REF_ID' => $referral_id
  ]);
  update_user_meta( $user_id, '_mcwallet_is_activated', [
    'ACTIVATED' => false
  ]);
}

add_action( 'register_new_user', 'wp_mcw_refsystem_register_new_user_action' );

function wp_mcw_refsystem_set_password_action( $password, $user_id ){
  $is_activated = get_user_meta( $user_id, '_mcwallet_is_activated' );
  $referral_id = get_user_meta( $user_id, '_mcwallet_referral_id');
  $is_activated = (isset($is_activated[0]) and isset($is_activated[0]['ACTIVATED'])) ? $is_activated[0]['ACTIVATED'] : false;
  $referral_id = (isset($referral_id[0]) and isset($referral_id[0]['REF_ID'])) ? $referral_id[0]['REF_ID'] : false;
  
  $refsystem_info = [
    'blockchain'      => get_option('mcwallet_refsystem_blockchain', ''),
    'oracleMnemonic'  => get_option('mcwallet_refsystem_oracleMnemonic', ''),
    'oracleAddress'   => get_option('mcwallet_refsystem_oracleAddress', ''),
    'oraclePKey'      => get_option('mcwallet_refsystem_oraclePKey', ''),
    'tokenAddress'    => get_option('mcwallet_refsystem_tokenAddress', ''),
    'tokenDecimals'   => get_option('mcwallet_refsystem_tokenDecimals', ''),
    'tokenSymbol'     => get_option('mcwallet_refsystem_tokenSymbol', ''),
    'contractAddress' => get_option('mcwallet_refsystem_contractAddress', '')
  ];

  $refsystem_configured = true;
  foreach ($refsystem_info as $key=>$value) {
    if ($value == '') {
      $refsystem_configured = false;
      break;
    }
  }
  
  if ($refsystem_configured and (get_option( 'mcwallet_enable_ref_system', 'false') == 'true')) {
    if (!$is_activated) {
      if (is_numeric($referral_id)) {
        // Save to refferal_id
        $user_info = get_userdata($user_id)->data;
        $referral_user = get_userdata($referral_id)->data;
        if ($referral_user and $referral_user->ID) {
          
          $Referral_Info = get_user_meta( $referral_id, '_mcwallet_ref_data');
          if ($Referral_Info
            and isset($Referral_Info[0])
            and isset($Referral_Info[0]['REF_DATA'])
          ) {
            $Referral_Info = $Referral_Info[0]['REF_DATA'];

            $ref_data = get_user_meta( $referral_id, '_mcwallet_referrals');
            if ($ref_data
              and isset($ref_data[0])
              and isset($ref_data[0]['REFERRALS'])
            ) {
              $ref_data = $ref_data[0]['REFERRALS'];
            } else {
              $ref_data = [];
            }
            $ref_data[] = $user_id;
            update_user_meta( $referral_id, '_mcwallet_referrals', [ 'REFERRALS' => $ref_data ]);
            // Do hook
            
            $web3api =  get_option('mcwallet_ref_api_server', 'http://194.67.88.197:3111');
            $data = [
              'mnemonic'          => $refsystem_info['oracleMnemonic'],
              'blockchain'        => $refsystem_info['blockchain'],
              'contract'          => $refsystem_info['contractAddress'],
              'UserId'            => $user_info->ID,
              'UserNickName'      => $user_info->user_nicename,
              'UserEmail'         => $user_info->user_email,
              'ReferrerId'        => $referral_user->ID,
              'ReferrerNickName'  => $referral_user->user_nicename,
              'ReferrerEmail'     => $referral_user->user_email,
              'ReferrerAddress'   => $Referral_Info['address']
            ];
            wp_remote_post( $web3api . '/call', $data);

          }
        }
        
      }
    }
  }
  update_user_meta( $user_id, '_mcwallet_is_activated', ['ACTIVATED' => true ]);
  
}
add_action( 'wp_set_password', 'wp_mcw_refsystem_set_password_action', 10, 3 );



function wp_mcw_refsystem_refferer_field() {
  if( get_option( 'mcwallet_enable_ref_system', 'false') == 'true' ) {
    $referral = isset( $_POST['referral'] ) ? sanitize_text_field( $_POST['referral']) : '';
    if (isset( $_GET['referral'] )) {
      $referral =  sanitize_text_field( $_GET['referral']);
    }
    ?>
    <p>
      <label for="referral"><?php _e( 'Referral', 'multi-currency-wallet' ); ?><br />
        <input type="text" name="referral" id="referral" class="input" value="<?php echo esc_attr( $referral ); ?>" size="25" />
      </label>
    </p>
    <?php
  }
}
add_action( 'register_form', 'wp_mcw_refsystem_refferer_field' );

// Ajax - test generate ref link data
function mcwallet_ref_system_check() {
  $data = json_decode( file_get_contents( 'php://input' ), true );

  // Dev
  $user_id = intval($data['WPuserUid']);
  /*
  // Prod
	if (intval($data['WPuserUid']) !== get_current_user_id()) {
		wp_die('Access deny', 403);
	}

	$user_id        = get_current_user_id();
  */
	$userData       = get_userdata($user_id)->data;
	$userHashString = $user_id.':'.$userData->user_login.':'.$userData->user_registered.':'.$userData->user_pass.':'.NONCE_SALT;
	$user_uniqhash  = md5( $userHashString );

	if ($user_uniqhash != $data['WPuserHash']) {
		wp_die('Access deny', 403);
	}
  $Referral_Info = get_user_meta( $user_id, '_mcwallet_ref_data');
  if (isset($Referral_Info[0])
    and isset($Referral_Info[0]['REF_DATA'])
    and isset($Referral_Info[0]['REF_DATA']['address'])
  ) {
    $user_referrals_ids = get_user_meta( $user_id, '_mcwallet_referrals');
    if ($user_referrals_ids
      and isset($user_referrals_ids[0])
      and isset($user_referrals_ids[0]['REFERRALS'])
    ) {
      $user_referrals_ids = $user_referrals_ids[0]['REFERRALS'];
    } else {
      $user_referrals_ids = [];
    }
    $user_referrals_info = [];
    if (count($user_referrals_ids) > 0) {
      $user_query = new WP_User_Query([
        'include' => $user_referrals_ids,
        'orderby' => 'user_registered',
        'order'   => 'DESC'
      ]);

      $users = $user_query->get_results();
      foreach($users as $user_info) {
        $user_referrals_info[] = [
          'ID' => $user_info->ID,
          'name' => $user_info->user_nicename,
          'email' => $user_info->user_email,
          'registered' => $user_info->user_registered
        ];
      }
    }
    wp_die(json_encode([
      "answer" => "ok",
      "address" => $Referral_Info[0]['REF_DATA']['address'],
      "referrals" => $user_referrals_info
    ]), 200);
  } else {
    wp_die(json_encode([
      "answer" => "not_inited"
    ]), 200);
  }
}
add_action( 'wp_ajax_nopriv_mcwallet_ref_system_check', 'mcwallet_ref_system_check' );
add_action( 'wp_ajax_mcwallet_ref_system_check', 'mcwallet_ref_system_check' );


function mcwallet_ref_test() {
  $refsystem_info = [
    'blockchain'      => get_option('mcwallet_refsystem_blockchain', ''),
    'oracleMnemonic'  => get_option('mcwallet_refsystem_oracleMnemonic', ''),
    'oracleAddress'   => get_option('mcwallet_refsystem_oracleAddress', ''),
    'oraclePKey'      => get_option('mcwallet_refsystem_oraclePKey', ''),
    'tokenAddress'    => get_option('mcwallet_refsystem_tokenAddress', ''),
    'tokenDecimals'   => get_option('mcwallet_refsystem_tokenDecimals', ''),
    'tokenSymbol'     => get_option('mcwallet_refsystem_tokenSymbol', ''),
    'contractAddress' => get_option('mcwallet_refsystem_contractAddress', '')
  ];
  $web3api =  get_option('mcwallet_ref_api_server', 'http://194.67.88.197:3111');
  $data = [
    'mnemonic'          => $refsystem_info['oracleMnemonic'],
    'blockchain'        => $refsystem_info['blockchain'],
    'contract'          => $refsystem_info['contractAddress'],
    'UserId'            => '4',
    'UserNickName'      => 'User4',
    'UserEmail'         => 'user4@mail.ru',
    'ReferrerId'        => '1',
    'ReferrerNickName'  => 'admin',
    'ReferrerEmail'     => 'admin@feo.pw',
    'ReferrerAddress'   => '0x2A8D166495c7f854c5f2510fBD250fDab8ce58d7'
  ];
  $options = array(
      'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
    )
  );

  $context  = stream_context_create($options);
  $result = file_get_contents($web3api . '/call', false, $context);

}
//add_action( 'wp_ajax_mcwallet_ref_test', 'mcwallet_ref_test' );
//add_action( 'wp_ajax_nopriv_mcwallet_ref_test', 'mcwallet_ref_test' );

// Ajax - get info about referrals
function mcwallet_ref_system_info() {
  $data = json_decode( file_get_contents( 'php://input' ), true );

  // Dev
  $user_id = intval($data['WPuserUid']);
  /*
  // Prod
	if (intval($data['WPuserUid']) !== get_current_user_id()) {
		wp_die('Access deny', 403);
	}

	$user_id        = get_current_user_id();
  */
	$userData       = get_userdata($user_id)->data;
	$userHashString = $user_id.':'.$userData->user_login.':'.$userData->user_registered.':'.$userData->user_pass.':'.NONCE_SALT;
	$user_uniqhash  = md5( $userHashString );

	if ($user_uniqhash != $data['WPuserHash']) {
		wp_die('Access deny', 403);
	}
  $Referral_Info = get_user_meta( $user_id, '_mcwallet_ref_data');
  if (isset($Referral_Info[0])
    and isset($Referral_Info[0]['REF_DATA'])
    and isset($Referral_Info[0]['REF_DATA']['address'])
  ) {
    $user_referrals_ids = get_user_meta( $user_id, '_mcwallet_referrals');
    if ($user_referrals_ids
      and isset($user_referrals_ids[0])
      and isset($user_referrals_ids[0]['REFERRALS'])
    ) {
      $user_referrals_ids = $user_referrals_ids[0]['REFERRALS'];
    } else {
      $user_referrals_ids = [];
    }
    $user_referrals_info = [];
    if (count($user_referrals_ids) > 0) {
      $user_query = new WP_User_Query([
        'include' => $user_referrals_ids,
        'orderby' => 'user_registered',
        'order'   => 'DESC'
      ]);

      $users = $user_query->get_results();
      foreach($users as $user_info) {
        $user_referrals_info[] = [
          'ID' => $user_info->ID,
          'name' => $user_info->user_nicename,
          'email' => $user_info->user_email,
          'registered' => $user_info->user_registered
        ];
      }
    }
    wp_die(json_encode([
      "answer" => "ok",
      "address" => $Referral_Info[0]['REF_DATA']['address'],
      "referrals" => $user_referrals_info,
      "joinUrl" => wp_registration_url() . '&referral=' . $user_id
    ]), 200);
  } else {
    wp_die(json_encode([
      "answer" => "not_inited"
    ]), 200);
  }
}
add_action( 'wp_ajax_nopriv_mcwallet_ref_system_info', 'mcwallet_ref_system_info' );
add_action( 'wp_ajax_mcwallet_ref_system_info', 'mcwallet_ref_system_info' );
// Ajax - generate ref link data
function mcwallet_ref_system_generate_link() {
  $data = json_decode( file_get_contents( 'php://input' ), true );

  // Dev
  $user_id = intval($data['WPuserUid']);
  /*
  // Prod
	if (intval($data['WPuserUid']) !== get_current_user_id()) {
		wp_die('Access deny', 403);
	}

	$user_id        = get_current_user_id();
  */
	$userData       = get_userdata($user_id)->data;
	$userHashString = $user_id.':'.$userData->user_login.':'.$userData->user_registered.':'.$userData->user_pass.':'.NONCE_SALT;
	$user_uniqhash  = md5( $userHashString );

	if ($user_uniqhash != $data['WPuserHash']) {
		wp_die('Access deny', 403);
	}

  $refData = [];
  $refData['email'] = $userData->user_email;
  $refData['address'] = $data['address'];

	update_user_meta( $user_id, '_mcwallet_ref_data', ['REF_DATA' => $refData ]);
	wp_die( json_encode([
    "answer" => "ok",
    "url" =>  wp_registration_url() . '&referral=' . $user_id
  ]), 200);
}

add_action( 'wp_ajax_nopriv_mcwallet_ref_system_generate_link', 'mcwallet_ref_system_generate_link' );
add_action( 'wp_ajax_mcwallet_ref_system_generate_link', 'mcwallet_ref_system_generate_link' );


// Ajax - admin save

function mcwallet_ref_system_save_new_contract() {
  check_ajax_referer( 'mcw-refsystem-nonce', 'nonce' );

  if ( ! current_user_can( 'manage_options' ) ) die();
  
  if (isset($_POST['data']) and is_array($_POST['data'])) {
    $indata = $_POST['data'];

    if (isset($indata['blockchain']) and ($indata['blockchain'] != '')
      and isset($indata['oracleMnemonic']) and ($indata['oracleMnemonic'] != '')
      and isset($indata['oracleAddress']) and ($indata['oracleAddress'] != '')
      and isset($indata['oraclePKey']) and ($indata['oraclePKey'] != '')
      and isset($indata['tokenAddress']) and ($indata['tokenAddress'] != '')
      and isset($indata['tokenDecimals']) and ($indata['tokenDecimals'] != '')
      and isset($indata['tokenSymbol']) and ($indata['tokenSymbol'] != '')
      and isset($indata['contractAddress']) and ($indata['contractAddress'] != '')
    ) {
      update_option('mcwallet_refsystem_blockchain', $indata['blockchain']);
      update_option('mcwallet_refsystem_oracleMnemonic', $indata['oracleMnemonic']);
      update_option('mcwallet_refsystem_oracleAddress', $indata['oracleAddress']);
      update_option('mcwallet_refsystem_oraclePKey', $indata['oraclePKey']);
      update_option('mcwallet_refsystem_tokenAddress', $indata['tokenAddress']);
      update_option('mcwallet_refsystem_tokenDecimals', $indata['tokenDecimals']);
      update_option('mcwallet_refsystem_tokenSymbol', $indata['tokenSymbol']);
      update_option('mcwallet_refsystem_contractAddress', $indata['contractAddress']);
      wp_send_json( array(
        'success' => true
      ) );
      exit();
    }
  }
  wp_die('{"answer":"fail"}', 200);
}
add_action( 'wp_ajax_mcwallet_ref_system_save_new_contract', 'mcwallet_ref_system_save_new_contract' );


function mcwallet_ref_system_enable_change() {
  check_ajax_referer( 'mcw-refsystem-nonce', 'nonce' );

  if ( ! current_user_can( 'manage_options' ) ) die();

  if (isset($_POST['data']) and is_array($_POST['data'])) {
    $indata = $_POST['data'];
    if (isset($indata['enabled']) and ($indata['enabled'] == 'true')) {
      update_option('mcwallet_enable_ref_system', 'true');
    } else {
      update_option('mcwallet_enable_ref_system', 'false');
    }
    wp_send_json( array(
      'success' => true
    ) );
    exit();
  }
  wp_die('{"answer":"fail"}', 200);
}
add_action( 'wp_ajax_mcwallet_ref_system_enable_change', 'mcwallet_ref_system_enable_change' );


function mcwallet_ref_system_blockchains() {
  return array(
    'bsc_mainnet' => array(
      'chainId'   => 56,
      'rpc'       => 'https://bsc-dataseed.binance.org/',
      'title'     => 'Binance Smart Chain (ERC20)',
      'etherscan' => 'https://bscscan.com',
      'currency'  => 'BNB'
    ),
    'eth_sepolia'   => array(
      'chainId'   => 11155111,
      'rpc'       => 'https://eth-sepolia.g.alchemy.com/v2/eV40AoRwFdzusyW_9htirAoRXSMssQ0E',
      'title'     => 'Ethereum - Testnet (Sepolia)',
      'etherscan' => 'https://sepolia.etherscan.io/',
      'currency'  => 'ETH'
    ),
    'eth_mainnet'   => array(
      'chainId'   => 1,
      'rpc'       => 'https://mainnet.infura.io/v3/5ffc47f65c4042ce847ef66a3fa70d4c',
      'title'     => 'Ethereum',
      'etherscan' => 'https://etherscan.io',
      'currency'  => 'ETH'
    ),
  );
}
?>