<?php
/**
 * Enqueue Scripts
 */
function mcwallet_enqueue_scripts() {

	/* Register styles */
	wp_register_style( 'mcwallet-bootstrap', MCWALLET_URL . 'assets/css/bootstrap.min.css', false, '4.3.1' );
	wp_register_style( 'fontawesome', MCWALLET_URL . 'assets/css/fontawesome.min.css', false, '5.7.21-' . MCWALLET_VER );
	wp_register_style( 'swiper', MCWALLET_URL . 'assets/css/swiper.min.css', false, '4.5.1' );
	wp_register_style( 'mcwallet-style', MCWALLET_URL . 'assets/css/style.css', false, MCWALLET_VER . '-' . MCWALLET_BUILD_VER );
	/* Google Fonts */
	wp_register_style( 'mcwallet-google-fonts', mcwallet_fonts_url(), array(), MCWALLET_VER . '-' . MCWALLET_BUILD_VER );
	wp_register_style( 'mcwallet-app', MCWALLET_URL . 'vendors/swap/app.css', false, MCWALLET_VER . '-' . MCWALLET_BUILD_VER );
	/* Register Scripts */
	wp_register_script( 'swiper', MCWALLET_URL . 'assets/js/swiper.min.js', array(), '4.5.1', true );
	wp_register_script( 'mcwallet-vendor', MCWALLET_URL . 'vendors/swap/vendor.js', array( 'react-dom', 'swiper' ), MCWALLET_VER . '-' . MCWALLET_BUILD_VER, true );
  /*
  $path = MCWALLET_PATH . 'vendors/swap/';
  $chunks_files = scandir($path);

  foreach($chunks_files as $fkey=>$file) {
    $file_ext = explode('.', $file);
    $file_ext = $file_ext[count($file_ext)-1];
    if (strtolower($file_ext) === 'js') {
      $is_chunk = explode('.', $file);
      if ((count($is_chunk) === 4)
        and ($is_chunk[0] === '1')
        and ($is_chunk[2] === 'chunk')
      ) {
        wp_register_script( 'mcwallet-vendor', MCWALLET_URL . 'vendors/swap/' . $file , array( 'react-dom', 'swiper' ), MCWALLET_VER . '-' . MCWALLET_BUILD_VER, true );
      }
    }
  }
  */
	wp_register_script( 'mcwallet-app', MCWALLET_URL . 'includes/enqueue_scripts/load-app.php', array( 'mcwallet-vendor' ), MCWALLET_VER . '-' . MCWALLET_BUILD_VER, true );

	wp_add_inline_script( 'mcwallet-vendor', mcwallet_inline_build_script(), 'before' );
	wp_add_inline_script( 'mcwallet-vendor', mcwallet_inline_script(), 'before' );

	/* Translatable string */
	wp_localize_script('mcwallet-vendor', 'mcwallet',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'mcwallet-nonce' ),
		)
	);

}
//add_action( 'wp_loaded', 'mcwallet_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'mcwallet_enqueue_scripts' );

/**
 * Widget Page Print Styles
 */
function mcwallet_print_head_styles() {
	echo mcwallet_head_meta();
	wp_print_styles( 'mcwallet-bootstrap' );
	wp_print_styles( 'fontawesome' );
	wp_print_styles( 'swiper' );
	wp_print_styles( 'mcwallet-style' );
	wp_print_styles( 'mcwallet-google-fonts' );
	wp_print_styles( 'mcwallet-app' );
	echo '<script>' . "\n";
	echo '  var isWidgetBuild = "true";' . "\n";
	echo '</script>' . "\n";
	?>
<!-- ya coment -->
	<style>
	  :root {
        --primary:<?php echo get_option( 'mcwallet_colors_primary', '#6144e5' ); ?>;
        --primary-hover: <?php echo get_option( 'mcwallet_colors_primary_hover', '#7371ff' ); ?>;
        --primary-background: <?php echo get_option( 'mcwallet_colors_primary_background', 'rgba(97, 68, 229, 0.1)' ); ?>;
        --alternate: <?php echo get_option( 'mcwallet_colors_alternate', '#e91db4' ); ?>;
        --alternate-hover:  <?php echo get_option( 'mcwallet_colors_alternate_hover', '#ff1fc5' ); ?>;
      }
      
      #WEB3_CONNECT_MODAL_ID {
        position: relative;
        z-index: 1000000;
      }
	</style>
	<?php
}
add_action( 'mcwallet_head', 'mcwallet_print_head_styles' );

/**
 * Page Print Scripts
 */
function mcwallet_print_scripts_widget_footer() {
	wp_print_scripts( 'mcwallet-app' );
}
add_action( 'mcwallet_footer', 'mcwallet_print_scripts_widget_footer' );

/**
 * Swap Head Metas
 */
function mcwallet_head_meta() {
	$meta  = '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
	// Disable google translate.
	$meta .= '<meta name="google" content="notranslate" />' . "\n";
	$meta .= '<meta name="mobile-web-app-capable" content="yes">' . "\n";
	$meta .= '<meta name="theme-color" content="#fff">' . "\n";
	$meta .= '<meta name="application-name" content="swap.online">' . "\n";
	return $meta;
}

/**
 * Inline scripts
 */
function mcwallet_inline_build_script() {

	$script = ' const getNavigatorLanguage = () => {
		if (navigator.languages && navigator.languages.length) {
			return navigator.languages[0];
		} else {
			return navigator.userLanguage || navigator.language || navigator.browserLanguage || "en";
		}
	};

  var localStorageIsOk = true;
  try {
    var testLocalStorage = window.localStorage;
  } catch (e) {
    localStorageIsOk = false
    document.getElementById("onFailLocalStorageLink").href = window.location.href;
    document.getElementById("onFailLocalStorageMessage").classList.remove("d-none");
    var sendErrorFeedback = function () {
      var msg = "localStorage error: agent("+navigator.userAgent+")";
          msg+= ", location("+window.location.href+")";

      var url = "https://noxon.wpmix.net/counter.php?msg="+encodeURI(msg);

      window.jQuery.ajax({
        type: "POST",
        url: url
      });
    };
    if (!window.jQuery) {
      // inject jQuery for send request to counter for feedback
      var jsScriptTag = document.createElement("SCRIPT");
        jsScriptTag.src = "https://code.jquery.com/jquery-3.5.1.min.js";
        console.log("jsScriptTag", jsScriptTag);
      document.getElementsByTagName("BODY")[0].appendChild(jsScriptTag);
      var waitJQLoad = function (onLoaded) {
        if (window.jQuery) {
          onLoaded();
        } else {
          window.setTimeout( function () {
            waitJQLoad(onLoaded);
          }, 100);
        };
      };
      waitJQLoad(sendErrorFeedback);
    } else {
      sendErrorFeedback();
    };
  };

	function setCookie(name, value, options) {
		options = options || {};
		var expires = options.expires;
		if (typeof expires == "number" && expires) {
			var d = new Date();
			d.setTime(d.getTime() + expires * 1000);
			expires = options.expires = d;
		}
		if (expires && expires.toUTCString) {
			options.expires = expires.toUTCString();
		}

		value = encodeURIComponent(value);
		var updatedCookie = name + "=" + value;

		for (var propName in options) {
			updatedCookie += "; " + propName;
			var propValue = options[propName];
			if (propValue !== true) {
				updatedCookie += "=" + propValue;
			}
		}
		document.cookie = updatedCookie;
	}

	function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(";");
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == " ") {
			  c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}

	var advice = document.getElementById("beforeJSTip");

	const wrapper = document.getElementById("wrapper_element");

	if ( window.localStorage.getItem("isDark") ) {
		wrapper.classList.add("dark");
	} else {
		wrapper.classList.remove("dark");
	}

	var lang = getCookie("mylang");

	// detect browser lang
	if (!lang) {
		var browserLang = getNavigatorLanguage();
		lang = browserLang.indexOf("ru") > -1 ? "ru" : "en";
		setCookie("mylang", lang);
	}

	const locale = lang.toLowerCase();
	const locationName = lang.toUpperCase();

	advice.innerText = "' . esc_html__( 'Do not forget to save your private keys!', 'multi-currency-wallet' ) . '";

	var information = document.getElementById("usersInform");

	if ((locationName === "ru" || (locale === "ru" && locationName !== "en")) && localStorage.length === 0) {
	  information.innerText = "' . esc_html__( 'Generating private keys for your multi-currency wallet right now, \ n it may take one minute', 'multi-currency-wallet' ) . '";
	}

	if (localStorage.length === 0) {
	  information.innerText = "' . esc_html__( 'Please wait while the application is loading,\n it may take one minute...', 'multi-currency-wallet' ) . '";
	}

	';

	return $script;
}

/**
 * Inline scripts
 */
function mcwallet_inline_script() {

	$script = '';

	$tokens = get_option( 'mcwallet_tokens' );

	if ( false !== $tokens && empty( $tokens ) ) {
		$tokens = mcwallet_default_token();
	}

	if ( $tokens ) {
		$script = "window.widgetERC20Tokens = {" . "\n";
		$i      = 0;
		$count  = count( $tokens );

		foreach ( $tokens as $name => $token ) {
			$i++;
			$separator = '';
			if ( $count != $i ) {
				$separator = ',';
			}
			$name     = strtolower( $name );
			$address  = $token['address'];
			$decimals = $token['decimals'];
			$fullname = $token['name'];
			$symbol   = strtolower( $token['symbol'] );
			$icon     = $token['icon'];
			$rate     = '';
			if ( isset( $token['rate'] ) ) {
				$rate = $token['rate'];
			}
			$icon_bg = '';
			if ( isset( $token['bg'] ) ) {
				$icon_bg = $token['bg'];
			}
			$how_deposit = '';
			if ( isset( $token['howdeposit'] ) ) {
				$how_deposit = $token['howdeposit'];
			}
			$how_withdraw = '';
			if ( isset( $token['howwithdraw'] ) ) {
				$how_withdraw = $token['howwithdraw'];
			}
			$script .= "    " . $symbol . ": {
		address: '" . $address . "',
		decimals: " . $decimals . ",
		fullName: '" . $fullname . "',
		icon: '" . $icon . "',
		customEcxchangeRate: '" . $rate . "',
		iconBgColor: '" . $icon_bg . "',
		howToDeposit: '" . wp_specialchars_decode( $how_deposit ) . "',
		howToWithdraw: '" . wp_specialchars_decode( $how_withdraw ) . "',
	}" . $separator . "\n";
		}
		$script .= "}\n\n";

	}

	$default_fiat = 'USD';
	if ( get_option( 'fiat_currency' ) ) {
		$default_fiat = get_option( 'fiat_currency' );
	}

	$is_user_loggedin = 'false';
	if ( is_user_logged_in() && get_option( 'mcwallet_is_logged' ) == 'true' ) {
		$is_user_loggedin = 'true';
	}

	$show_howitworks = 0;
	if ( get_option( 'show_howitworks' ) ) {
		$show_howitworks = 1;
	}

	$window_arr = array(
		'prerenderReady'               => 'false',
		'CUSTOM_LOGO'                  => 'false',
		'LOGO_REDIRECT_LINK'           => mcwallet_get_logo_redirect_link(),
		'logoUrl'                      => mcwallet_logo_url(),
		'darkLogoUrl'                  => mcwallet_dark_logo_url(),
		'publicUrl'                    => MCWALLET_URL . 'vendors/swap/',
		'defaultWindowTitle'           => get_option( 'mcwallet_page_title', esc_html__( 'Hot Wallet with p2p exchange', 'multi-currency-wallet' ) ),
		'DEFAULT_FIAT'                 => $default_fiat,
		'isUserRegisteredAndLoggedIn'  => $is_user_loggedin,
		'buyViaCreditCardLink'         => get_option( 'fiat_gateway_url', 'https://itez.swaponline.io/?DEFAULT_FIAT={DEFAULT_FIAT}&locale={locale}&btcaddress={btcaddress}' ),
		'logoutUrl'                    => wp_logout_url( mcwallet_page_url() ),
		'showHowItWorksOnExchangePage' => $show_howitworks,
		'widgetName'                   => get_bloginfo(),
    'EXCHANGE_DISABLED'            => get_option( 'mcwallet_exchange_disabled', 'false' ),
    'CUR_BTC_DISABLED'             => get_option( 'mcwallet_btc_disabled', 'false' ),
    'CUR_ETH_DISABLED'             => get_option( 'mcwallet_eth_disabled', 'false' ),
    'CUR_GHOST_DISABLED'           => (get_option( 'mcwallet_ghost_enabled') == 'true') ? 'false' : 'true',
    'CUR_NEXT_DISABLED'            => (get_option( 'mcwallet_next_enabled') == 'true') ? 'false' : 'true',
    '_ui_footerDisabled'           =>  get_option( 'mcwallet_disable_footer', 'false')
	);

	if ( get_current_user_id() ) {
		$window_arr['setItemPlugin'] = "saveUserData";
		$window_arr['WPuserUid'] = esc_html(get_current_user_id());
		$window_arr['userDataPluginApi']  = admin_url( 'admin-ajax.php' ).'?action=mcwallet_update_user_meta';

    // Нужно сгенерировать уникальный хеш из данных пользователя
    // Нельзя передавать просто userId, это не безопастно
    // Помимо userId в backend передаем его хеш, по которому проверяем,
    // Действительно ли это отправил пользователь или злоумышлиник, обычным подбором userId
    $userData = get_userdata(get_current_user_id())->data;
    $userHashString = get_current_user_id().':'.$userData->user_login.':'.$userData->user_registered.':'.$userData->user_pass.':'.NONCE_SALT;
    $user_uniqhash = md5($userHashString);

    $window_arr['WPuserHash'] = esc_html($user_uniqhash);
    if (get_option( 'mcwallet_remember_userwallet' ) == 'true') {
      $window_arr['backupPlugin'] = 'backupUserData';
      $window_arr['backupUrl'] = admin_url( 'admin-ajax.php' ).'?action=mcwallet_backup_userwallet';
      $window_arr['restoreUrl'] = admin_url( 'admin-ajax.php' ).'?action=mcwallet_restore_userwallet';
    }
	}

	foreach ( $window_arr as $var => $value ) {
		if ( $value != 'true' && $value != 'false' && $value != '1' && 'false' && $value != '0' ) {
			$value = '\'' . $value . '\'';
		}
		$script .= 'window.' . $var . ' = ' . $value . ';' . "\n";
	}
	$script .= "\n";

	$fees = array();

	if ( get_option( 'btc_fee' ) ) {
		$fees['btc']['fee'] = get_option( 'btc_fee' );
	}
	if ( get_option( 'btc_min' ) ) {
		$fees['btc']['min'] = get_option( 'btc_min' );
	}
	if ( get_option( 'btc_fee_address' ) ) {
		$fees['btc']['address'] = get_option( 'btc_fee_address' );
	}
	if ( get_option( 'eth_fee' ) ) {
		$fees['eth']['fee'] = get_option( 'eth_fee' );
	}
	if ( get_option( 'eth_min' ) ) {
		$fees['eth']['min'] = get_option( 'eth_min' );
	}
	if ( get_option( 'eth_fee_address' ) ) {
		$fees['eth']['address'] = get_option( 'eth_fee_address' );
	}
	if ( get_option( 'tokens_fee' ) ) {
		$fees['erc20']['fee'] = get_option( 'tokens_fee' );
	}
	if ( get_option( 'tokens_min' ) ) {
		$fees['erc20']['min'] = get_option( 'tokens_min' );
	}
	if ( get_option( 'eth_fee_address' ) ) {
		$fees['eth']['address']   = get_option( 'eth_fee_address' );
		$fees['erc20']['address'] = get_option( 'eth_fee_address' );
	}

	$script .= 'window.widgetERC20Comisions = ' . wp_json_encode( $fees, JSON_PRETTY_PRINT ) . ';' . "\n\n";

	$args = array(
		'post_type'      => 'mcwallet_banner',
		'posts_per_page' => -1,
		'order'          => 'ASC',
	);

	$query = new WP_Query( $args );

	$banners_js = '""';
	if ( $query->have_posts() ) :
		$banners_js = '[';
		while ( $query->have_posts() ) : $query->the_post();
			$banner_bg = '';
			if ( get_post_meta( get_the_ID(), 'banner_color', true ) ) {
				$banner_bg = get_post_meta( get_the_ID(), 'banner_color', true );
				$banner_bg = ltrim( $banner_bg, '#' );
			}
			if ( get_post_meta( get_the_ID(), 'banner_image', true ) ) {
				$banner_bg = get_post_meta( get_the_ID(), 'banner_image', true );
			}
			$banner_arr = array(
				wp_json_encode( get_the_title() ),
				wp_json_encode( get_the_title() ),
				wp_json_encode( get_post_meta( get_the_ID(), 'banner_text', true ) ),
				wp_json_encode( $banner_bg ),
				wp_json_encode( get_post_meta( get_the_ID(), 'banner_url', true ) ),
				wp_json_encode( get_post_meta( get_the_ID(), 'banner_icon', true ) ),
			);
			$banners_js .= '[' . implode( ',', $banner_arr ) . '],';

		endwhile;
		$banners_js = rtrim( $banners_js, ',' );
		$banners_js .= ']';

	endif;
	wp_reset_postdata();

	$script .= 'window.bannersOnMainPage = ' . $banners_js . ';' . "\n\n";

	return $script;
}

/**
 * Register Google fonts.
 */
function mcwallet_fonts_url() {

	$fonts_url = '';
	$subsets   = 'latin,cyrillic';
	$fonts     = array();

	$fonts[] = 'Roboto:400,500,700,900';
	$fonts[] = 'Roboto Mono';

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family'  => implode( rawurlencode( '|' ), $fonts ),
			'display' => 'swap',
			'subset'  => rawurlencode( $subsets ),
		),
		'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Remove all styles and scripts.
 */
function mcwallet_remove_all_styles_and_scripts() {
	/**
	 * Remove all styles.
	 */
	function mcwallet_remove_all_styles() {
		global $wp_styles;
		$wp_styles->queue = array();
	}
	add_action( 'wp_print_styles', 'mcwallet_remove_all_styles' );

	/**
	 * Remove all scripts.
	 */
	function mcwallet_remove_all_scripts() {
		global $wp_scripts;
		$wp_scripts->queue = array();
	}
	add_action( 'wp_print_scripts', 'mcwallet_remove_all_scripts' );
}
add_action( 'mcwallet_before_template', 'mcwallet_remove_all_styles_and_scripts' );

/**
 * Remove WordPress Admin Bar CSS.
 */
function mcwallet_remove_admin_bar_bump_cb() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action( 'mcwallet_before_template', 'mcwallet_remove_admin_bar_bump_cb' );
