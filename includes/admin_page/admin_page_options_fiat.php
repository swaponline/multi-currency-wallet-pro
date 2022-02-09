<div class="mcwallet-shortcode-panel-row">
  <h3><?php esc_html_e( 'Fiat options', MCWALLET_LANG_MARKER ); ?></h3>
  <table class="form-table">
    <tbody>
      <tr>
        <th scope="row">
          <label><?php esc_html_e( 'Default fiat currency', MCWALLET_LANG_MARKER );?></label>
        </th>
        <td>
          <select type="text" name="fiat_currency" class="regular-text">
            <?php foreach( mcwallet_get_valutes() as $key => $valute ) { ?>
              <option value="<?php echo esc_attr( $key ); ?>" <?php selected( get_option( 'fiat_currency', 'USD' ), $key ); ?>><?php echo esc_attr( $valute ); ?></option>
            <?php } ?>
          </select>
          <!-- https://noxon.wpmix.net/worldCurrencyPrices.php -->
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <h3><?php esc_html_e( 'Service (api) for getting up-to-date courses', MCWALLET_LANG_MARKER ); ?></h3>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <label><?php esc_html_e( 'Source', MCWALLET_LANG_MARKER ); ?></label>
        </th>
        <td>
          <select type="text" name="fiat_course_source" class="regular-text">
            <option value="swaponline"><?php esc_html_e( 'Default swaponline.io backend', MCWALLET_LANG_MARKER );?></option>
            <option value="custom"><?php esc_html_e( 'Own', MCWALLET_LANG_MARKER );?></option>
          </select>
          <p class="description"><?php esc_html_e( 'Default backend has limitations:', MCWALLET_LANG_MARKER );?></p>
          <p class="description"><?php esc_html_e( '1. Supported fiat currencies: USD, CAD, RUB, EUR, GBT, GBP, INR (We add new ones on request)', MCWALLET_LANG_MARKER); ?></p>
          <p class="description"><?php esc_html_e( '2. Update time - 6 hours' );?></p>
          <p class="description"><?php esc_html_e( 'If you select &quot;Own&quot;, you will be able to adjust the update time and it will work for your fiat currency', MCWALLET_LANG_MARKER); ?></p>
        </td>
      </tr>
    </tbody>
    <tbody>
      <tr>
        <td colspan="2">
          <h3><?php esc_html_e( 'Own fiat backend options', MCWALLET_LANG_MARKER); ?></h3>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <label><?php esc_html_e( 'API key', MCWALLET_LANG_MARKER); ?></label>
        </th>
        <td>
          <input type="text" name="fiat_backend_apikey" class="large-text" />
          <p class="description">
            <?php
              esc_html_e(
                'Own backend uses &quot;coinmarketcap.com&quot; service API. you need to register and use the generated API-key',
                MCWALLET_LANG_MARKER
              );
            ?>
            <a href="#" target="_blank"><?php esc_html_e('Click here to open &quot;coinmarketcap.com&quot;', MCWALLET_LANG_MARKER);?></a>
          </p>
        </td>
      </tr>
    </tbody>
    <tbody>
      <tr>
        <td colspan="2">
          <h3><?php esc_html_e( 'Buy cryptocurrency with fiat options', MCWALLET_LANG_MARKER ); ?></h3>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <label><?php esc_html_e( 'Fiat Gateway Url', MCWALLET_LANG_MARKER );?></label>
        </th>
        <td>
          <input name="fiat_gateway_url" type="text" class="large-text" value="<?php echo esc_attr( get_option( 'fiat_gateway_url', 'https://itez.swaponline.io/?DEFAULT_FIAT={DEFAULT_FIAT}&locale={locale}&btcaddress={btcaddress}') );?>">
        </td>
      </tr>
      <tr>
        <th scope="row">
          <label>
            <?php esc_html_e( 'Transak API key', MCWALLET_LANG_MARKER );?>
            (<a target=_blank href="https://transak.com/">?</a>)
          </label>
        </th>
        <td>
          <input name="transak_api_key" type="text" class="large-text" value="<?php echo esc_attr( get_option( 'transak_api_key', '') );?>">
          <p class="description"><?php esc_html_e( 'With this key, your payment method will be automatically replaced with the Transak service', MCWALLET_LANG_MARKER );?></p>
        </td>
      </tr>
    </tbody>
  </table>
</div>