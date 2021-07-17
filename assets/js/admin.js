/**
 * Widget Admin Scripts
 */
(function( $ ){
	"use strict";

	/**
	 * Tabs
	 */
	$('.mcwallet-nav-tabs > a').on( 'click', function(e) {
		e.preventDefault();
		var tab = $(this).attr('href');
		// set active navigation tab
		$('.mcwallet-nav-tabs > a').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');

		// set active tab from content
		$('.mcwallet-panel-tab').removeClass('panel-tab-active');
		$(tab).addClass('panel-tab-active');

	});

	/**
	 * Notices
	 */
	var noticeEl = $('.mcwallet-notice');
	function mcwalletNotice( text, status ){
		noticeEl.find('p').text( text );
		noticeEl.addClass('notice-' + status ).fadeIn();
		setTimeout(function(){
			noticeEl.fadeOut(function(){
				noticeEl.removeClass('notice-' . status );
				noticeEl.removeClass('notice-success notice-error');
			});
		},6000);
	}

	/**
	 * Spinner
	 */
	function mcwalletSpinner( button ){
		button.next('.spinner').toggleClass('is-active');
	}

	/** 
	 * Add token
	 */
	$('.mcwallet-add-token').on('click',function(e){
		e.preventDefault();
		var thisBtn = $(this);
		var thisForm = $(this).parents('form');
		var tokenAddress = thisForm.find('[name="address"]').val();
		var tokenName = thisForm.find('[name="name"]').val();
		var tokenStandard = thisForm.find('[name="standard"]').val();
		var tokenIcon = thisForm.find('[name="icon"]').val();
		var tokenRate = thisForm.find('[name="rate"]').val();
		var iconBg = thisForm.find('.mcwallet-icon-bg').val();
		var howDeposit = window.tinyMCE.get('howdeposit').getContent();
		var howWithdraw = window.tinyMCE.get('howwithdraw').getContent();
		var tokenOrder = thisForm.find('[name="order"]').val();
		mcwalletSpinner(thisBtn);

		if ( tokenAddress ){

			var data = {
				action: 'mcwallet_add_token',
				nonce: mcwallet.nonce,
				address: tokenAddress,
				name: tokenName,
				standard: tokenStandard,
				icon: tokenIcon,
				rate: tokenRate,
				bg: iconBg,
				howdeposit: howDeposit,
				howwithdraw: howWithdraw,
				order: tokenOrder,
			};

			$.post( mcwallet.ajaxurl, data, function(response) {

				if( response.status == 'success') {
					var thisHtml = response.html;
					$('.wp-list-tokens tbody').find('.item-empty').remove();
					// If no tokens, add to tbody.
					$('.wp-list-tokens tbody').append( thisHtml );

					setTimeout(function(){
						$('.wp-list-tokens tbody').find('.item-fade').removeClass('item-fade');
						$(this).scrollTop(0);
					},10);
					setTimeout(function(){
						$('.wp-list-tokens tbody').find('.item-adding').removeClass('item-adding');
						
						//location.reload();
					},2000);
					
					mcwalletNotice( mcwallet.notices.success, 'success');
					thisForm.find('[type="text"]').val('');
					
					window.tinyMCE.get('howdeposit').setContent('');
					window.tinyMCE.get('howwithdraw').setContent('');
					
				}
				if ( response.status == 'false' ) {
					mcwalletNotice( mcwallet.notices.wrong, 'error');
				}
				if ( response.status == 'invalid' ) {
					mcwalletNotice( mcwallet.notices.invalid, 'error');
				}
				
				mcwalletSpinner(thisBtn);

			});
		} else {
			mcwalletNotice( mcwallet.notices.empty, 'error');
			mcwalletSpinner(thisBtn);
		}

	});

	/**
	 * Remove token
	 */
	$(document).on('click','.mcwallet-remove-token', function(e){
		e.preventDefault();
		var thisName = $(this).data('name');
		var thisItem = $(this).parents('.item');

		if ( thisName ){

			var data = {
				action: 'remove_token',
				nonce: mcwallet.nonce,
				name: thisName,
			};

			$.post( ajaxurl, data, function(response) {

				if( response == 'true') {
					thisItem.addClass('removing');
					thisItem.fadeOut( function(){
						thisItem.remove();
						mcwalletNotice( mcwallet.notices.removed, 'success');
						if( ! $('.wp-list-tokens tbody .item').length ) {
							$('.wp-list-tokens tbody ').html( '<tr class="item item-empty"><td colspan="8"><span>' + mcwallet.notices.noTokens + '</span></td></tr>' );
						}
					});
				}
				if ( response == 'false' ) {
					mcwalletNotice( mcwallet.notices.wrong, 'success');
				}

			});
		}
	});

	/**
	 * If user must be logged in - save user data
	 */
	$('.mcwallet-form-options [name="is_logged"]').on('change', function (e) {
		if ($('.mcwallet-form-options [name="is_logged"]').is(':checked')) {
			$('.mcwallet-form-options [name="remeber_userwallet"]').prop('checked', true)
		}
	})
	
	/**
	 * Update Options
	 */
	$('.mcwallet-update-options').on('click',function(e){
		e.preventDefault();
		var thisBtn        = $(this);
		var thisParent     = $('.mcwallet-form-options');
		var logoUrl        = thisParent.find( '[name="logo_url"]' ).val();
		var darkLogoUrl    = thisParent.find( '[name="dark_logo_url"]' ).val();
		var logoLink       = thisParent.find( '[name="logo_link"]' ).val();
		var pageTitle      = thisParent.find( '[name="mcwallet_page_title"]' ).val();
		var pageSlug       = thisParent.find( '[name="page_slug"]' ).val();
		var pageHome       = thisParent.find( '[name="is_home"]' );
		var pageAccess     = thisParent.find( '[name="is_logged"]' );
		var btcFee         = thisParent.find( '[name="btc_fee"]' ).val();
		var btcMin         = thisParent.find( '[name="btc_min"]' ).val();
		var btcFeeAddress  = thisParent.find( '[name="btc_fee_address"]' ).val();
		var ethFee         = thisParent.find( '[name="eth_fee"]' ).val();
		var ethMin         = thisParent.find( '[name="eth_min"]' ).val();
		var ethFeeAddress  = thisParent.find( '[name="eth_fee_address"]' ).val();
		var tokensFee      = thisParent.find( '[name="tokens_fee"]' ).val();
		var tokensMin      = thisParent.find( '[name="tokens_min"]' ).val();
		var fiatCurrency   = thisParent.find( '[name="fiat_currency"]' ).val();
		var fiatGatewayUrl = thisParent.find( '[name="fiat_gateway_url"]' ).val();
		var showHowitworks = thisParent.find( '[name="show_howitworks"]' );
		var codeHead       = thisParent.find( '[name="mcwallet_head_code"]' ).val();
		var codeBody       = thisParent.find( '[name="mcwallet_body_code"]' ).val();
		var codeFooter     = thisParent.find( '[name="mcwallet_footer_code"]' ).val();
		var statisticDisabled = thisParent.find( '[name="statistic_disabled"]' );
		var btcDisabled    = thisParent.find( '[name="btc_disabled"]' );
		var ethDisabled    = thisParent.find( '[name="eth_disabled"]' );
		var bnbDisabled    = thisParent.find( '[name="bnb_disabled"]' );
		var maticDisabled    = thisParent.find( '[name="matic_disabled"]' );
		var arbitrumDisabled = thisParent.find( '[name="arbitrum_disabled"]' );
		var ghostEnabled   = thisParent.find( '[name="ghost_enabled"]' );
		var nextEnabled   = thisParent.find( '[name="next_enabled"]' );
		var exchangeDisabled = thisParent.find( '[name="exchange_disabled"]' );
		var invoiceEnabled = thisParent.find( '[name="invoice_enabled"]' );

		var rememberUserWallet = thisParent.find( '[name="remeber_userwallet"]' );

		var footerDisabled = thisParent.find( '[name="disable_footer"]' );

		var useTestnet = thisParent.find( '[name="use_testnet"]' );
		// click handler

		var strings = '';
		if ( $('.mcwallet-string-input').length ) {
			 strings = $('.mcwallet-string-input').serializeArray();
		}

		var ishome = 'false';
		var isLogged = 'false';
		var isHowitworks = 'false';

		statisticDisabled = statisticDisabled.is(':checked') ? 'true' : 'false';
		btcDisabled = btcDisabled.is(':checked') ? 'true' : 'false';
		ethDisabled = ethDisabled.is(':checked') ? 'true' : 'false';
		ghostEnabled = ghostEnabled.is(':checked') ? 'false' : 'true';
		nextEnabled = nextEnabled.is(':checked') ? 'false' : 'true';
		bnbDisabled = bnbDisabled.is(':checked') ? 'true' : 'false';
		maticDisabled = maticDisabled.is(':checked') ? 'true' : 'false';
		arbitrumDisabled = arbitrumDisabled.is(':checked') ? 'true' : 'false';

		useTestnet = useTestnet.is(':checked') ? 'true' : 'false';

		exchangeDisabled = exchangeDisabled.is(':checked') ? 'true' : 'false';

		invoiceEnabled = invoiceEnabled.is(':checked') ? 'true' : 'false';

		rememberUserWallet = rememberUserWallet.is(':checked') ? 'true' : 'false';

		footerDisabled = footerDisabled.is(':checked') ? 'true' : 'false';

		if ( pageHome.is(':checked') ) {
			ishome = 'true';
		}
		
		if ( pageAccess.is(':checked') ) {
			isLogged = 'true';
			rememberUserWallet = 'true';
		}
		
		if ( showHowitworks.is(':checked') ) {
			isHowitworks = 'true';
		}

		var data = {
			action: 'mcwallet_update_options',
			nonce: mcwallet.nonce,
			url: logoUrl,
						darkLogoUrl: darkLogoUrl,
						logoLink: logoLink,
			pageTitle: pageTitle,
			slug: pageSlug,
			btcFee: btcFee,
			btcMin: btcMin,
			btcFeeAddress: btcFeeAddress,
			ethFee: ethFee,
			ethMin: ethMin,
			ethFeeAddress: ethFeeAddress,
			tokensFee: tokensFee,
			tokensMin: tokensMin,
			fiatCurrency: fiatCurrency,
			ishome: ishome,
			islogged: isLogged,
			codeHead: codeHead,
			codeBody: codeBody,
			codeFooter: codeFooter,
			fiatGatewayUrl: fiatGatewayUrl,
			isHowitworks: isHowitworks,
			strings: strings,
			statisticDisabled: statisticDisabled,
			btcDisabled: btcDisabled,
			ethDisabled: ethDisabled,
			bnbDisabled: bnbDisabled,
			maticDisabled: maticDisabled,
			arbitrumDisabled: arbitrumDisabled,
			ghostEnabled: ghostEnabled,
			nextEnabled: nextEnabled,
			exchangeDisabled: exchangeDisabled,
			useTestnet: useTestnet,

			invoiceEnabled: invoiceEnabled,

			rememberUserWallet: rememberUserWallet,

			footerDisabled: footerDisabled,
		};

		mcwalletSpinner(thisBtn);

		$.post( ajaxurl, data, function(response) {

			if( response.status == 'success' ) {
				mcwalletNotice( mcwallet.notices.updated, 'success');
				$('.mcwallet-page-url').val( response.url );
				$('.mcwallet-page-slug').val( response.slug );
				$('.mcwallet-button-url').attr('href', response.url );
				$('.mcwallet-button-thickbox').attr('href', response.thickbox );
			}
			if ( response.status == 'false' ) {
				mcwalletNotice( mcwallet.notices.wrong, 'error');
			}
			mcwalletSpinner(thisBtn);
		});
		
	});

	/**
	 * Select/Upload icon
	 */
	$('body').on('click', '.mcwallet-load-icon', function(e){
		e.preventDefault();

		var button = $(this),
			custom_uploader = wp.media({
				title: mcwallet.uploader.title,
				library : {
					type : 'image'
				},
			button: {
				text: mcwallet.uploader.button
			},
			multiple: false
		}).on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$('.mcwallet-input-icon').val( attachment.url );
		})
		.open();
	});

	/**
	 * Select/Upload logo
	 */
	$('body').on('click', '.mcwallet-load-logo', function(e){
		e.preventDefault();

		var button = $(this),
			custom_uploader = wp.media({
				title: mcwallet.uploader.title,
				library : {
					type : 'image'
				},
			button: {
				text: mcwallet.uploader.button
			},
			multiple: false
		}).on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$('.mcwallet-input-logo').val( attachment.url );
		})
		.open();
	});
		
		/**
	 * Select/Upload dark logo
	 */
	$('body').on('click', '.mcwallet-load-dark-logo', function(e){
		e.preventDefault();

		var button = $(this),
			custom_uploader = wp.media({
				title: mcwallet.uploader.title,
				library : {
					type : 'image'
				},
			button: {
				text: mcwallet.uploader.button
			},
			multiple: false
		}).on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$('.mcwallet-input-dark-logo').val( attachment.url );
		})
		.open();
	});
	
	/**
	 * Select/Upload Image
	 */
	$('body').on('click', '.mcwallet-load-image', function(e){
		e.preventDefault();
 
		var button = $(this),
			input = button.prev(),
			custom_uploader = wp.media({
				title: mcwallet.uploader.title,
				library : {
					type : 'image'
				},
			button: {
				text: mcwallet.uploader.button
			},
			multiple: false
		}).on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			input.val( attachment.url );
		})
		.open();
	});
	
	/**
	 * Add String
	 */
	$('body').on('click', '.mcwallet-add-string', function(e){
		e.preventDefault();
		
		if ( $('.mcwallet-strings-empty-row').length ) {
			$('.mcwallet-strings-empty-row').remove();
		}
		
		var count = $('.mcwallet-strings-row').length;
		
		var rowString = '<div class="mcwallet-strings-row">' +
							'<div class="mcwallet-string-col">' +
								'<input type="text" name="string_' + count + '" class="large-text mcwallet-string-input" value="">' + 
							'</div>' +
							'<div class="mcwallet-string-col">' +
								 '<input type="text" name="string_' + count + '" class="large-text mcwallet-string-input" value="">' + 
							'</div>' +
							'<div class="mcwallet-string-action">' +
								'<a href="#" class="button-link-delete mcwallet-remove-string"><span class="dashicons dashicons-trash"></span></a>' +
							'</div>' +
						'</div>';
		 count++;
		$('.mcwallet-strings-body').append( rowString );
	});
	
	/**
	 * Add String
	 */
	$('body').on('click', '.mcwallet-remove-string', function(e){
		e.preventDefault();
		if ( ! $('.mcwallet-strings-row').length ) {
			var emptyString = '<div class="mcwallet-strings-empty-row">no strings</div>';
			$('.mcwallet-strings-body').append( emptyString );
		}
		$(this).parents('.mcwallet-strings-row').remove();
	});
	
	
	/**
	 * Enable/Disable edit url
	 */
	$('#mcwallet_is_home').on( 'change', function(e) {
		var ishome = 'false';
		if ( $(this).is(':checked') ) {
			$('.mcwallet-page-slug').attr('disabled','true');
			$('.mcwallet-button-url').addClass('disabled');
		} else {
			$('.mcwallet-page-slug').removeAttr('disabled');
			$('.mcwallet-button-url').removeClass('disabled');
		}
	});

	/**
	 * Select Color
	 */
	$('.mcwallet-icon-bg').wpColorPicker();
	$('.mcwallet-color-picker').wpColorPicker();
	
	/**
	 * Timynce text template
	 */
	$('.insert-text-template').on('click', function(e){
		e.preventDefault();
		var thisEditor = $(this).data('editor-id');
		var thisText = $(this).data('text');
		window.tinyMCE.execCommand('mceFocus',false,thisEditor);
		setTimeout(function(){
			wp.media.editor.insert(thisText);
		}, 50);
	});

	/**
	 * Sortable
	 */
	 $('.wp-list-table tbody').sortable({
		axis: 'y',
		cursor: 'move',
		//cancel: '.item-address,.item-name',
		handle: '.item-count',
		placeholder: 'ui-state-highlight',
		update: function( event, ui ) {

			var tableItems = $('.wp-list-table tbody .item');
			var items = [];
			tableItems.each(function( index ) {
				items.push( $( this ).data('name')); 
			});

			var data = {
				action: 'reorder_token',
				nonce: mcwallet.nonce,
				items: items,
			};

			$.post( ajaxurl, data, function(response) {
				console.log(response);
			});

		}
	});

})( jQuery );
