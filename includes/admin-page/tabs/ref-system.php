<?php
/**
 * Tab Ref System Template
 * 
 * @package Multi Currency Wallet
 */

?>

<?php
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
?>
<div class="mcwallet-shortcode-panel-row">
  <?php if ($refsystem_configured) { ?>
  <input type="hidden" id="refsystem_is_configured" value="1" />
  <?php } else { ?>
  <input type="hidden" id="refsystem_is_configured" value="0" />
  <?php } ?>
	<h3><?php esc_html_e( 'Referal system', 'multi-currency-wallet' );?></h3>
  <table class="form-table" id="refsystem_holder_connect_wallet">
    <tr>
      <th scope="row" colspan="2">
        <button class="button button-primary" id="mcwallet_ref_initmetamask">Connect metamask</button>
      </th>
    </tr>
  </table>
  <table class="form-table" style="display: none;" id="refsystem_holder_connected_wallet">
    <tbody>
      <tr>
        <th scope="row">
          <?php esc_html_e( 'Enable Referal system', 'multi-currency-wallet' ); ?>
        </th>
        <td>
          <div class="refsystem-form-inline">
            <label for="mcwallet_enable_ref_system">
              <input 
                name="mcwallet_enable_ref_system"
                type="checkbox"
                id="mcwallet_enable_ref_system"
                <?php checked( 'true', get_option( 'mcwallet_enable_ref_system', 'false') ); ?>
              >
              <span><?php esc_html_e( 'Allow referal system' , 'multi-currency-wallet' ); ?></span>
            </label>
            <a class="button button-secondary" id="mcwallet_ref_system_enable_save">Save</a>
          </div>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e( 'Web3 Api server', 'multi-currency-wallet'); ?>
        </th>
        <td>
          <div class="refsystem-form-inline">
            <input
              name="mcwallet_ref_api_server"
              id="mcwallet_ref_api_server"
              type="text"
              class="large-text"
              value="<?php esc_html_e( get_option('mcwallet_ref_api_server', 'http://194.67.88.197:3111') );?>" 
            />
            <a class="button button-secondary" id="refsystem_update_api_server">[Update]</a>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <a class="button button-primary"
            data-ref-action-show="#refsystem_deploy_new_holder"
          >Configure new referral program</a>
        </td>
      </tr>
    </tbody>
    <tbody id="refsystem_deploy_new_holder" style="display: none;">
      <tr>
        <th colspan="2">
          <h3>Configure new referral program</h3>
        </th>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e( 'Blockchain', 'multi-currency-wallet'); ?>
        </th>
        <td>
          
          <select name="mcwallet_ref_blockchain" id="mcwallet_ref_blockchain" class="regular-text">
            <option value="">Select network</option>
            <?php
              $chains = mcwallet_ref_system_blockchains();
              foreach($chains as $key=>$chain_info) {
                ?>
                <option value="<?php echo $key?>" <?php echo ($refsystem_info['blockchain'] == $key) ? 'selected' : ''?>><?php esc_html_e($chain_info['title']);?></option>
                <?php
              }
            ?>
          </select>
          <div>
            <label for="mcwallet_ref_blockchain">
              <?php esc_html_e('Select the blockchain where the reward will be issued', 'multi-currency-wallet' );?>
            </label>
          </div>
        </td>
      </tr>
      <tr class="showOnChainSelected" style="display: none">
        <th scope="row">
          Oracle mnemonic:
        </th>
        <td>
          <div class="refsystem-form-inline">
            <input name="mcwallet_ref_oracle_mnemonic" id="mcwallet_ref_oracle_mnemonic" class="large-text" type="text" value="" />
            <a class="button button-secondary" id="refsystem_generate_oracle_from_mnemonic">
              <?php echo esc_html__( 'Generate Oracle wallet', 'multi-currency-wallet' ) ?>
            </a>
          </div>
          <p>Enter 12-word phrase for generate oracle wallet</p>
        </td>
      </tr>
      <tr class="showOnChainSelected" style="display: none">
        <th scope="row">
          Oracle address:
        </th>
        <td>
          <input name="mcwallet_ref_oracle_address" id="mcwallet_ref_oracle_address" class="large-text" type="text" value="" readonly="true" />
          <p>This address will be used to manage the contract.</p>
          <p>This address must have enough native currency on its balance to make transactions.</p>
          <input type="hidden" name="mcwallet_ref_oracle_pkey" id="mcwallet_ref_oracle_pkey" value="" />
        </td>
      </tr>
      <tr class="showOnChainSelected" style="display: none">
        <th scope="row">
          <?php esc_html_e( 'Reward token', 'multi-currency-wallet'); ?>
        </th>
        <td>
          <div class="refsystem-form-inline">
            <input name="mcwallet_ref_reward_token"
              id="mcwallet_ref_reward_token"
              type="text"
              class="large-text"
              value="<?php esc_html_e( get_option('mcwallet_ref_reward_token', '') );?>"
            />
            <a class="button button-secondary" id="refsystem_fetch_token_info">
              <?php echo esc_html__( 'Fetch Info', 'multi-currency-wallet' ) ?>
            </a>
          </div>
        </td>
      </tr>
      <tr id="refsystem_token_info" style="display: none">
        <td colspan="2">
          <table class="form-table">
            <thead>
              <tr>
                <td colspan="2">
                  <strong>Reward token info</strong>
                </td>
              </tr>
            </thead>
            <tr>
              <th scope="row">
                Symbol:
              </th>
              <td>
                <strong id="refsystem_token_symbol_view">12</strong>
                <input type="hidden" id="refsystem_token_symbol" />
              </td>
            </tr>
            <tr>
              <th scope="row">
                Name:
              </th>
              <td>
                <strong id="refsystem_token_name_view">2</strong>
                <input type="hidden" id="refsystem_token_name" />
              </td>
            </tr>
            <tr>
              <th scope="row">
                Decimals:
              </th>
              <td>
                <strong id="refsystem_token_decimals_view">3</strong>
                <input type="hidden" id="refsystem_token_decimals" />
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr id="refsystem_reward_amount_holder" style="display: none">
        <th scope="row">
          Reward amount:
        </th>
        <td>
          <input type="number" min="0" step="any" id="refsystem_reward_amount" name="refsystem_reward_amount" value="1" />
          <div>
            Amount of token <span id="refsystem_token_symbol_view_2"></span> per each registered user
          </div>
        </td>
      </tr>
      <tr id="refsystem_deploy_holder" style="display: none">
        <th></th>
        <td>
          <a class="button button-primary" id="refsystem_deploy_button">Deploy New Referral Program Contract</a>
        </td>
      </tr>
    </tbody>
    <tbody id="refsystem_deployed_holder" style="display: none">
      <tr>
        <th scope="row">
          Contract address:
        </th>
        <td>
          <input name="mcwallet_ref_contract_address"
            id="mcwallet_ref_contract_address"
            type="text"
            class="large-text"
            value="<?php esc_html_e( get_option('mcwallet_ref_contract_address', '') );?>"
          />
        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <a class="button button-primary" id="refsystem_save_deployed">Save deployed contract</a>
        </td>
      </tr>
    </tbody>
    <?php if ($refsystem_configured) { ?>
      <tbody>
        <tr>
          <td colspan="2">
            <h3>Configured referral program</h3>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Blockchain:
          </th>
          <td>
            <strong>
              <?php
                $chain_info = mcwallet_ref_system_blockchains()[$refsystem_info['blockchain']];
                echo $chain_info['title'];
              ?>
            </strong>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Oracle Address:
          </th>
          <td>
            <strong><?php echo $refsystem_info['oracleAddress']?></strong>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Oracle at contract:
          </th>
          <td>
            <strong id="refsystem_oracle_address">...</strong>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Oracle mnemonic:
          </th>
          <td>
            <a id="refsystem_oracle_mnemonic_show"
              data-ref-action-hide="#refsystem_oracle_mnemonic_show"
              data-ref-action-show="#refsystem_oracle_mnemonic_holder"
              class="button button-secondary"
            >[Click here for show oracle mnemonic phrase]</a>
            <div class="refsystem-form-inline" id="refsystem_oracle_mnemonic_holder" style="display: none">
              <input type="text" class="large-text" readonly="true" value="<?php echo $refsystem_info['oracleMnemonic']?>" />
              <a id="refsystem_oracle_mnemonic_hide"
                data-ref-action-show="#refsystem_oracle_mnemonic_show"
                data-ref-action-hide="#refsystem_oracle_mnemonic_holder"
                class="button button-secondary"
              >[Hide]</a>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Oracle Private key:
          </th>
          <td>
            <a id="refsystem_oracle_pkey_show"
              data-ref-action-hide="#refsystem_oracle_pkey_show"
              data-ref-action-show="#refsystem_oracle_pkey_holder"
              class="button button-secondary"
            >[Click here for show oracle private key]</a>
            <div class="refsystem-form-inline" id="refsystem_oracle_pkey_holder" style="display: none">
              <input type="text" class="large-text" readonly="true" value="<?php echo $refsystem_info['oraclePKey']?>" />
              <a id="refsystem_oracle_pkey_hide"
                data-ref-action-show="#refsystem_oracle_pkey_show"
                data-ref-action-hide="#refsystem_oracle_pkey_holder"
                class="button button-secondary"
              >[Hide]</a>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Oracle balance:
          </th>
          <td>
            <div class="refsystem-form-inline">
              <div>
                <strong id="refsystem_oracle_balance_holder">...</strong>
                <strong>&nbsp;<?php echo $chain_info['currency']?>&nbsp;</strong>
              </div>
              <a id="refsystem_oracle_balance_update" class="button button-secondary">[Update]</a>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Referral contract:
          </th>
          <td>
            <strong><?php echo $refsystem_info['contractAddress']?></strong>
            <input type="hidden" id="mcw_configured_referral_contract" value="<?php echo  $refsystem_info['contractAddress']?>" />
          </td>
        </tr>
        <tr>
          <th scope="row">
            Contract owner:
          </th>
          <td>
            <strong id="refsystem_contract_owner">...</strong>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Reward token:
          </th>
          <td>
            <strong id="refsystem_reward_token"><?php echo $refsystem_info['tokenAddress']?></strong>
            &nbsp;
            <strong>(<?php echo $refsystem_info['tokenSymbol']?>)</strong>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Tokens balance:
          </th>
          <td>
            <div class="refsystem-form-inline">
              <strong id="refsystem_contract_balance">...</strong>
              <strong>&nbsp;<?php echo $refsystem_info['tokenSymbol']?>&nbsp;</strong>
              <a data-mcw-action="update" class="button button-secondary">[Update]</a>
              <a id="refsystem_contract_balance_withdraw" class="button button-secondary">[Withdraw tokens]</a>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Reward amount:
          </th>
          <td>
            <input type="hidden" id="refsystem_token_decimals_at_contract" value="0" />
            <div class="refsystem-form-inline" id="refsystem_reward_amount_view">
              <strong id="refsystem_contract_reward_amount">...</strong>
              <strong>&nbsp;<?php echo $refsystem_info['tokenSymbol']?>&nbsp;</strong>
              <a id="refsystem_contract_reward_amount_change"
                data-ref-action-hide="#refsystem_reward_amount_view"
                data-ref-action-show="#refsystem_reward_amount_edit"
                class="button button-secondary"
              >[Change]</a>
            </div>
            <div class="refsystem-form-inline" id="refsystem_reward_amount_edit" style="display: none">
              <input
                type="number"
                min="0"
                value="0"
                step="any"
                id="refsystem_reward_amount_new"
              />
              <strong><?php echo $refsystem_info['tokenSymbol']?></strong>
              <a id="refsystem_reward_amount_change" class="button button-secondary">[Set new amount]</a>
              <a id="refsystem_reward_amount_cancel"
                data-ref-action-hide="#refsystem_reward_amount_edit"
                data-ref-action-show="#refsystem_reward_amount_view"
                class="button button-secondary"
              >[Cancel]</a>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Reward allocated:
          </th>
          <td>
            <div class="refsystem-form-inline">
              <strong id="refsystem_reward_allocated">...</strong>
              <strong>&nbsp;<?php echo $refsystem_info['tokenSymbol']?>&nbsp;</strong>
              <a data-mcw-action="update" class="button button-secondary">[Update]</a>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Reward pending:
          </th>
          <td>
            <div class="refsystem-form-inline">
              <strong id="refsystem_reward_pending">...</strong>
              <strong>&nbsp;<?php echo $refsystem_info['tokenSymbol']?>&nbsp;</strong>
              <a data-mcw-action="update" class="button button-secondary">[Update]</a>
              <a id="refsystem_allocate_pending" class="button button-secondary">[Allocate tokens]</a>
            </div>
          </td>
        </tr>
      </tbody>
    <?php } ?>
    <tbody style="display: none">
      <tr>
        <th scope="row">
          <?php esc_html_e( 'Bonus token address', 'multi-currency-wallet' ); ?>
        </th>
        <td>
          <strong>0x00000000000</strong>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e('Token symbol', 'multi-currency-wallet' ); ?>
        </th>
        <td>
          <strong>RK</strong>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e( 'Oracle seed', 'multi-currency-wallet' );?>
        </th>
        <td>
          <input name="mcwallet_ref_oracle_seed" id="mcwallet_ref_oracle_seed" class="large-text" type="text" value="<?php esc_html_e( get_option(' mcwallet_ref_oracle_seed', '') ); ?>" />
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e('Oracle address', 'multi-currency-wallet' ); ?>
        </th>
        <td>
          <strong>0x000000000000</strong>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e('Oracle balance', 'multi-currency-wallet' );?>
        </th>
        <td>
          <strong>0.11 BNB</strong>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e( 'Contract', 'multi-currency-wallet'); ?>
        </th>
        <td>
          <input name="mcwallet_ref_contract" id="mcwallet_ref_contract" type="text" class="large-text" value="<?php esc_html_e( get_option('mcwallet_ref_contract', '') );?>" />
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e( 'Web3 Api server', 'multi-currency-wallet'); ?>
        </th>
        <td>
          <input name="mcwallet_ref_api_server" id="mcwallet_ref_api_server" type="text" class="large-text" value="<?php esc_html_e( get_option('mcwallet_ref_api_server', '') );?>" />
        </td>
      </tr>
    </tbody>
  </table>
<?php /*
	<table class="form-table">
		<tr>
			<td>
				<span class="mcwallet-submit-group">
					<input type="submit" name="mcwallet-update-menu" id="mcwallet-update-menu" class="button button-primary" value="<?php esc_attr_e( 'Update menu items', 'multi-currency-wallet' ); ?>" >
					<span class="spinner"></span>
				</span>
			</td>
		</tr>
	</table>
*/ ?>
</div>
<div id="refsystem_loaderOverlay" class="refsystem-overlay">
  <div class="refsystem-loader"></div>
  <div class="refsystem-loader-status" id="refsystem_loaderStatus">Loading...</div>
</div>
<div class="notice refsystem-notice hide-all"><p></p></div>