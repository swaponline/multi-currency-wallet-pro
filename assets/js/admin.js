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
		var tokenIcon = thisForm.find('[name="icon"]').val();
		var iconBg = thisForm.find('.mcwallet-icon-bg').val();
		var howDeposit = window.tinyMCE.get('howdeposit').getContent();
		var howWithdraw = window.tinyMCE.get('howwithdraw').getContent();
		
		mcwalletSpinner(thisBtn);
	
		if ( tokenAddress ){
	
			var data = {
				action: 'mcwallet_add_token',
				nonce: mcwallet.nonce,
				address: tokenAddress,
				name: tokenName,
				icon: tokenIcon,
				bg: iconBg,
				howdeposit: howDeposit,
				howwithdraw: howWithdraw,
			};

			$.post( mcwallet.ajaxurl, data, function(response) {
				
				if( response.status == 'success') {
					var thisHtml = response.html;
					$('.wp-list-tokens tbody').append( thisHtml );
					setTimeout(function(){
						$('.wp-list-tokens tbody').find('.item-fade').removeClass('item-fade');
					},10);
					setTimeout(function(){
						$('.wp-list-tokens tbody').find('.item-adding').removeClass('item-adding');
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
					});
				}
				if ( response == 'false' ) {
					mcwalletNotice( mcwallet.notices.wrong, 'success');
				}

			});
		}
		
	});

	/**
	 * Update Options
	 */
	$('.mcwallet-update-options').on('click',function(e){
		e.preventDefault();
		var thisBtn = $(this);
		var thisParent = $('.mcwallet-form-options');
		var logoUrl  = thisParent.find('[name="logo_url"]').val();
		var pageSlug = thisParent.find('[name="page_slug"]').val();
		var pageHome = thisParent.find('[name="is_home"]');
		var pageAccess = thisParent.find('[name="is_logged"]');
		var btcFee = thisParent.find('[name="btc_fee"]').val();
        var btcMin = thisParent.find('[name="btc_min"]').val();
		var btcFeeAddress = thisParent.find('[name="btc_fee_address"]').val();
		var ethFee = thisParent.find('[name="eth_fee"]').val();
        var ethMin = thisParent.find('[name="eth_min"]').val();
		var ethFeeAddress = thisParent.find('[name="eth_fee_address"]').val();
		var tokensFee = thisParent.find('[name="tokens_fee"]').val();
        var tokensMin = thisParent.find('[name="tokens_min"]').val();
        var fiatCurrency = thisParent.find('[name="fiat_currency"]').val();
        
        var codeHead   = thisParent.find( '[name="mcwallet_head_code"]' ).val();
        var codeBody   = thisParent.find( '[name="mcwallet_body_code"]' ).val();
        var codeFooter = thisParent.find( '[name="mcwallet_footer_code"]' ).val();
        

		var ishome = 'false';
		var isLogged = 'false';
		
		if ( pageHome.is(':checked') ) {
			ishome = 'true';
		}
		
		if ( pageAccess.is(':checked') ) {
			isLogged = 'true';
		}

		var data = {
			action: 'mcwallet_update_options',
			nonce: mcwallet.nonce,
			url: logoUrl,
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
            codeFooter: codeFooter
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
	

})( jQuery );