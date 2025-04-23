/**
 * Admin Scripts
 */
(function( $ ){
	"use strict";

  const getUnixTimeStamp = () => Math.floor(new Date().getTime() / 1000);
/*
  
  var fetchButton     = document.getElementById('lotteryfactory_fetchcontract_button');
  var fetchStatus     = document.getElementById('lotteryfactory_fetchstatus');
	
  
  
  var lotteryAddress  = document.getElementById('lottery_address');
  var startLottery    = document.getElementById('lotteryfactory_startlottery');
  var closeAndGoDraw  = document.getElementById('lottery_current_close_goto_draw');
  var drawNumbers     = document.getElementById('lotteryfactory_draw_numbers');
*/
  var deployButton    = document.getElementById('refsystem_deploy_button');
  var loaderOverlay   = document.getElementById('refsystem_loaderOverlay');
  var contractAddress = document.getElementById('mcwallet_ref_contract_address');
  var tokenAddress    = document.getElementById('mcwallet_ref_reward_token');
  var tokenSymbol     = document.getElementById('refsystem_token_symbol');
  var tokenDecimals   = document.getElementById('refsystem_token_decimals');
  var rewardAmount    = document.getElementById('refsystem_reward_amount');
  var fetchToken      = document.getElementById('refsystem_fetch_token_info');
  var initMetamask     = document.getElementById('mcwallet_ref_initmetamask');
  var selectedChain    = document.getElementById('mcwallet_ref_blockchain');
  var oracleAddress   = document.getElementById('mcwallet_ref_oracle_address');
  var oracleMnemonic  = document.getElementById('mcwallet_ref_oracle_mnemonic');
  var oraclePKey      = document.getElementById('mcwallet_ref_oracle_pkey');
  var generateOracleAddress = document.getElementById('refsystem_generate_oracle_from_mnemonic');
  var saveDeployed    = document.getElementById('refsystem_save_deployed');
  
  var isConfigured = (document.getElementById('refsystem_is_configured').value == "1") ? true : false;
/*
  var saveWinningPercents = document.getElementById('lottery-winning-percent-save');

  

  const postId        = document.getElementById('lotteryfactory_post_id').value;
  var numbersCountChange = document.getElementById('lottery_numbers_count_change');
*/

  var loaderStatusText = document.getElementById('refsystem_loaderStatus')
  

  let thisIsOperator = false
  let thisIsOwner = false

  var getValue = (id) => { return document.getElementById(id).value }
  var setValue = (id, value) => { document.getElementById(id).value = value }
  var setHtml = (id, value) => { document.getElementById(id).innerHTML = value }
  var showBlock = (id) => { document.getElementById(id).style.display = '' }
  var hideBlock = (id) => { document.getElementById(id).style.display = 'none' }
  var showLoader = () => {
    loaderStatusText.innerText = ''
    loaderOverlay.classList.add('visible')
  }
  var setLoaderStatus = (message) => {
    loaderStatusText.innerText = message
  }
  var hideLoader = () => { loaderOverlay.classList.remove('visible') }

  var showNotice = ( text, status = 'success' ) => {
    const noticeEl = $('.refsystem-notice');
		noticeEl.find('p').text( text );
		noticeEl.addClass('notice-' + status ).fadeIn();
		setTimeout(function(){
			noticeEl.fadeOut(function(){
				noticeEl.removeClass('notice-' . status );
				noticeEl.removeClass('notice-success notice-error');
			});
		},6000);
	}

  const langMsg = (msg, replaces = {}) => {
    Object.keys(replaces).forEach((key) => {
      msg = msg.replaceAll(`{${key}}`, escape(replaces[key]))
    })
    return msg
  }

  var getDateDiffText = (dateStart, dateEnd) => {
    var diff = dateEnd - dateStart
 
    var d = Number(diff)
    var h = Math.floor(d / 3600)
    var m = Math.floor(d % 3600 / 60)
    var s = Math.floor(d % 3600 % 60)

    var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : ""
    var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : ""
    var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : ""
    return hDisplay + mDisplay + sDisplay
  }

  var errMessage = (message) => { alert(message) }
  var getFloat = (id) => {
    var val = document.getElementById(id).value
    try {
      val = parseFloat(val,10)
      return val
    } catch (e) {
      return false
    }
  }
  const EVM_ADDRESS_REGEXP = /^0x[A-Fa-f0-9]{40}$/
  const isEvmAddress = (value) => typeof value === 'string' && EVM_ADDRESS_REGEXP.test(value)
  
  var getNumber = (id) => {
    var val = document.getElementById(id).value
    try {
      val = parseInt(val,10)
      return val
    } catch (e) {
      return false
    }
  }

  const ajaxSendData = (options) => {
    return new Promise((resolve, reject) => {
      const {
        action,
        data
      } = options

      const ajaxData = {
        action,
        nonce: mcw_refsystem.nonce,
        data
      }
      $.post( mcw_refsystem.ajaxurl, ajaxData, function(response) {
        if( response.success) {
          resolve(response)
        } else {
          reject(response)
        }
      })
    })
  }
  window.ajaxSendData = ajaxSendData
  var setTokenInfo = function (tokenInfo) {
    setHtml('refsystem_token_name_view', tokenInfo.name)
    setValue('refsystem_token_name', tokenInfo.name)
    setHtml('refsystem_token_decimals_view', tokenInfo.decimals)
    setValue('refsystem_token_decimals', tokenInfo.decimals)
    setHtml('refsystem_token_symbol_view', tokenInfo.symbol)
    setHtml('refsystem_token_symbol_view_2', tokenInfo.symbol)
    setValue('refsystem_token_symbol', tokenInfo.symbol)
    showBlock('refsystem_token_info')
    showBlock('refsystem_reward_amount_holder')
  }

  mcwRefSystem.init({
    onStartLoading: () => {
      // show loader
      
      deployButton.disabled = true;
      //fetchButton.disabled = true;
      fetchToken.disabled = true;
      
    },
    onFinishLoading: async () => {
      // hide loader
      deployButton.disabled = false;
      //fetchButton.disabled = false;
      fetchToken.disabled = false;
      const account = await mcwRefSystem.getActiveAccount()
      if (account) {
        hideBlock('refsystem_holder_connect_wallet')
        showBlock('refsystem_holder_connected_wallet')
      } else {
        showBlock('refsystem_holder_connect_wallet')
        hideBlock('refsystem_holder_connected_wallet')
      }
      try {
        if (isConfigured && account) {
          if (selectedChain.value != 0) {
            await mcwRefSystem
              .setSelectedChain(selectedChain.value)
          }
          console.log('>>> is finished fetch info')
          fetchInfo()
        }
      } catch (e) {
        hideLoader()
      }
    },
    onError: (err) => {
      console.error(err);
      deployButton.disabled = true;
      //fetchButton.disabled = true;
      fetchToken.disabled = true;
      hideLoader();
      alert(err);
    }
  });

  
  $('A[data-ref-action-hide]').on('click', (e) => {
    e.preventDefault();
    var $a = $(e.target);
    $($a.attr('data-ref-action-hide'))[0].style.display = 'none'
  })
  $('A[data-ref-action-show]').on('click', (e) => {
    e.preventDefault();
    var $a = $(e.target);
    $($a.attr('data-ref-action-show'))[0].style.display = ''
  })
  
  $( saveDeployed ).on('click', (e) => {
    e.preventDefault()
    const data = {
      blockchain: selectedChain.value,
      oracleMnemonic: oracleMnemonic.value,
      oraclePKey: oraclePKey.value,
      oracleAddress: oracleAddress.value,
      tokenAddress: tokenAddress.value,
      tokenDecimals: $('#refsystem_token_decimals').val(),
      tokenSymbol: $('#refsystem_token_symbol').val(),
      contractAddress: contractAddress.value,
    }
    
    console.log(data)
    showLoader()
    setLoaderStatus( langMsg( 'Saving...' ) )
    ajaxSendData({
      action: 'mcwallet_ref_system_save_new_contract',
      data,
    }).then((answer) => {
      console.log('Ok')
      window.location.reload()
      hideLoader()
    }).catch((err) => {
      console.log('fail save', err)
      hideLoader()
    })
  })
  
  $( generateOracleAddress ).on('click', (e) => {
    e.preventDefault()
    const wallet = mcwRefSystem.getWalletFromMnemonic(oracleMnemonic.value)
    oracleAddress.value = wallet.address
    oraclePKey.value = wallet.privateKey
  })
  $('#mcwallet_ref_system_enable_save').on('click', (e) => {
    e.preventDefault()
    showLoader()
    setLoaderStatus( langMsg( 'Saving...' ) )
    ajaxSendData({
      action: 'mcwallet_ref_system_enable_change',
      data: {
        enabled: ($('#mcwallet_enable_ref_system').is(':checked')) ? 'true' : 'false'
      },
    }).then((answer) => {
      console.log('Ok')
      hideLoader()
    }).catch((err) => {
      console.log('fail save', err)
      hideLoader()
    })
  })
/*
  // Change operator address
  $('#lottery_operator_change').on('click', function (e) {
    e.preventDefault()
    $('#lottery_operator_changebox>INPUT').val($('#lottery_operator').html())
    hideBlock('lottery_operator_viewbox')
    showBlock('lottery_operator_changebox')
  })
  $('#lottery_operator_cancel_change').on('click', function (e) {
    e.preventDefault()
    hideBlock('lottery_operator_changebox')
    showBlock('lottery_operator_viewbox')
  })
  $('#lottery_operator_do_change').on('click', function (e) {
    e.preventDefault()
    const unlockButton = () => {
      $('#lottery_operator_do_change')[0].disabled = false
      $('#lottery_operator_cancel_change')[0].disabled = false
      hideLoader()
    }
    const operatorAddress = $('#lottery_operator_changebox>INPUT').val()
    if (!mcwRefSystem.isCorrectAddress(operatorAddress)) {
      return errMessage(langMsg('Not correct address'))
    }
    if (confirm(
      langMsg(
        'Change operator address to {operatorAddress}?',
        { operatorAddress }
      ))
    ) {
      $('#lottery_operator_do_change')[0].disabled = true
      $('#lottery_operator_cancel_change')[0].disabled = true
      showLoader()
      setLoaderStatus(langMsg( 'Change lottery operator to {operatorAddress}. Confirm transaction' , {
        operatorAddress
      }))
      mcwRefSystem
        .setOperatorAddress(lotteryAddress.value, operatorAddress)
        .then( (result) => {
          $('#lottery_operator').html(operatorAddress)
          hideBlock('lottery_operator_changebox')
          showBlock('lottery_operator_viewbox')
          unlockButton()
          showNotice( langMsg( 'Operator address succesfull changed to {operatorAddress}', { operatorAddress } ) )
        })
        .catch( (err) => {
          console.log('>>>fail', err)
          unlockButton()
        })
    }
  })
  // << Change operator address
  // Inject funds to current lottery round
  $('#lottery_inject_funds').on('click', function (e) {
    e.preventDefault()
    const unlockButton = () => {
      hideLoader()
      $('#lottery_inject_funds')[0].disabled = false
    }
    const tokenDecimals = getNumber('lottery_token_decimals')
    const injectAmount = getFloat('lottery_inject_funds_tobank')
    if (tokenDecimals === false) {
      return errMessage( langMsg( 'Could not determine token dicimals. Inquire about token and try again') )
    }
    if (injectAmount > 0) {
      if (confirm(langMsg(
        'Inject {amount} {symbol} to current lottery round?',
        {
          amount: injectAmount,
          symbol: $('#lottery_token_symbol').val()
        }
      ))) {
        const injectAmountWei = new BigNumber(injectAmount).multipliedBy(10 ** tokenDecimals).toFixed()
        $('#lottery_inject_funds')[0].disabled = true
        showLoader()
        setLoaderStatus( langMsg( 'Injecting current lottery bank amount. Check approve' ) )

        const injectFundsDo = () => {
          setLoaderStatus( langMsg( 'Injecting current lottery bank amount. Confirm transaction' ) )
          mcwRefSystem
            .injectFunds(lotteryAddress.value, injectAmountWei)
            .then( (result) => {
              unlockButton()
              fetchStatusFunc( langMsg( 'Fund injected. Fetching actual lottery status' ) )
            })
            .catch( (err) => {
              console.log('>>> err', err)
              unlockButton()
            })
        }
        mcwRefSystem
          .checkNeedApprove(tokenAddress.value, lotteryAddress.value, new BigNumber(injectAmountWei))
          .then( (needApprove) => {
            if (needApprove) {
              console.log('>>> need approve')
              setLoaderStatus( langMsg( 'Approve injecting funds. Confirm transaction' ) )
              mcwRefSystem
                .approveInjectFunds(tokenAddress.value, lotteryAddress.value, injectAmountWei)
                .then(() => {
                  injectFundsDo()
                })
                .catch( (err) => {
                  console.log('>>> err', err)
                  unlockButton()
                })
            } else {
              injectFundsDo()
            }
          })
          .catch( (err) => {
            console.log('>>> err', err)
            unlockButton()
          })
      }
    } else {
      return errMessage( langMsg( 'Amount for inject must be greater than zero' ) )
    }
  })
  // << Inject funds

  const fetchStatusFunc = (ownMessage = false) => {
    if (fetchStatus.disabled) return
    if (!lotteryAddress.value) return errMessage('No lottery address!')

    fetchStatus.disabled = true
    showLoader()
    setLoaderStatus( (ownMessage !== false) ? ownMessage : langMsg( 'Fetch lottery status' ) )
    mcwRefSystem
      .fetchLotteryInfo(lotteryAddress.value)
      .then( (lotteryInfo) => {
        thisIsOperator = (
          (lotteryInfo.operator.toUpperCase() == lotteryInfo.activeAccount.toUpperCase())
          || (lotteryInfo.owner.toUpperCase() == lotteryInfo.activeAccount.toUpperCase())
        )
        thisIsOwner = (lotteryInfo.owner.toUpperCase() == lotteryInfo.activeAccount.toUpperCase())
        console.log('>>>> lotteryInfo', lotteryInfo)
        setHtml('lottery_owner', lotteryInfo.owner)
        setHtml('lottery_operator', lotteryInfo.operator)
        setHtml('lottery_treasury', lotteryInfo.treasury)
        setHtml('lottery_current', lotteryInfo.currentLotteryNumber)
        if (thisIsOwner) showBlock('lottery_info')
        hideLoader()
        hideBlock('lottery_start')
        hideBlock('lottery_round')
        hideBlock('lottery_draw')
        hideBlock('lottery_settings')
        hideBlock('lottery_inject_funds_holder')
        $('INPUT.lottery-winning-percent-input').attr('type', 'hidden')

        const current = lotteryInfo.currentLotteryInfo

        // Время
        const lotteryStart = new Date(parseInt( current.startTime, 10) * 1000)
        const lotteryEnd = new Date(parseInt( current.endTime, 10) * 1000)
        setHtml('lottery_current_starttime', lotteryStart)
        setHtml('lottery_current_endtime', lotteryEnd)
        setHtml('lottery_current_timeleft', getDateDiffText(getUnixTimeStamp(), parseInt( current.endTime, 10) + 1 * 60))
        // Лотерея открыта, время вышло, нужен расчет
        if (current.status === "1"
          && (parseInt(current.endTime, 10) - getUnixTimeStamp() < 0)
        ) {
          showBlock('lottery_current_close_goto_draw')
          hideBlock('lottery_current_timeleft')
        } else {
          showBlock('lottery_current_timeleft')
          hideBlock('lottery_current_close_goto_draw')
        }
        // Текущий банк
        const tokenDecimals = getNumber('lottery_token_decimals')
        const bankAmount = new BigNumber(current.amountCollectedInCake)
          .div(new BigNumber(10).pow(tokenDecimals))
          .toNumber()
        setHtml('lottery_current_bank', bankAmount)

        if ((lotteryInfo.currentLotteryNumber !== "1") && (lotteryInfo.currentLotteryInfo.status === "1")) {
          showBlock('lottery_round')
        }
        if (current.status === "2") {
          showBlock('lottery_draw')
        }
        if (current.status === "3") {
          showBlock('lottery_start')
        }
        if (thisIsOwner) showBlock('lottery_inject_funds_holder')

        if (thisIsOwner) showBlock('lottery_settings')
        reinit_winningPercents()
        fetchStatus.disabled = false
        showNotice( langMsg('Lottery status fetched') )
      })
      .catch((e) => {
        console.log(e)
        hideLoader()
        $('INPUT.lottery-winning-percent-input').attr('type', 'hidden')
        hideBlock('lottery_start')
        hideBlock('lottery_round')
        hideBlock('lottery_draw')
        hideBlock('lottery_settings')
        fetchStatus.disabled = false
        alert('Fail fetch contract info')
      })
  }

  const calcWinningPercentsIsCorrect = () => {
    const numbersCount = parseInt( $('#lottery_numbers_count').val(), 10)
    const inputs = $('INPUT[data-winning-number]')
    let totalPercents = 0
    inputs.each((i, input) => {
      const $input = $(input)
      const inputNumber = parseInt($input.data('winning-number'), 10)
      
      if (numbersCount >= inputNumber) {
        const inputPercent = parseFloat($input.val())
        totalPercents = totalPercents + inputPercent
      }
    })
    return totalPercents
  }

  const checkWinningPercentsState = () => {
    const totalPercents = calcWinningPercentsIsCorrect()
    $('#lotteryfactory-winning-percent-total').html(totalPercents.toFixed(2))
    if (totalPercents != 100) {
      $('#lotteryfactory-winning-percent-error').removeClass('-hidden')
    } else {
      $('#lotteryfactory-winning-percent-error').addClass('-hidden')
    }
    return (totalPercents == 100)
  }

  $( 'INPUT.lottery-winning-percent-input[data-winning-number]' ).on('keyup', function (e) {
    checkWinningPercentsState()
  })

  $( saveWinningPercents ).on('click', function (e) {
    e.preventDefault()
    if (saveWinningPercents.disabled) return
    if (checkWinningPercentsState()) {
      showLoader()
      setLoaderStatus( langMsg( 'Saving changes... Plase wait.' ) )
      saveWinningPercents.disabled = true
      const unlockButton = () => {
        saveWinningPercents.disabled = false
        hideLoader()
      }
      ajaxSendData({
        action: 'lotteryfactory_update_options',
        data: {
          postId,
          options: {
            'winning_1': parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="1"]').val()),
            'winning_2': parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="2"]').val()),
            'winning_3': parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="3"]').val()),
            'winning_4': parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="4"]').val()),
            'winning_5': parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="5"]').val()),
            'winning_6': parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="6"]').val()),
          }
        }
      }).then((ajaxAnswer) => {
        console.log('>> save result', ajaxAnswer)
        unlockButton()
        showNotice( langMsg( 'Changes saved' ) )
      }).catch((isFail) => { unlockButton() })
    } else {
      errMessage( langMsg( 'The sum must be equal to 100%' ) )
    }
  })

  $( 'A[data-lottery-action="fix-winning-percents"]').on('click', function (e) {
    const $button = $(e.target)
    const winningNumber = parseInt( $button.data('winning-number'), 10)
    const $percentInput = $('INPUT.lottery-winning-percent-input[data-winning-number="' + winningNumber + '"]')
    const totalPercents = calcWinningPercentsIsCorrect()
    const percentDelta = 100 - totalPercents
    const ballPercent = parseFloat( $percentInput.val() )
    $percentInput.val( parseFloat(ballPercent + percentDelta).toFixed(2) )
    checkWinningPercentsState()
  })
  $( 'INPUT.lottery-winning-percent-input[data-winning-number]' ).on('change', function (e) {
    checkWinningPercentsState()
  })

  const reinit_winningPercents = () => {
    const numbersCount = parseInt( $('#lottery_numbers_count').val(), 10)
    const winningPercentsHolders = $('.lotteryfactory-winning-percent')
    winningPercentsHolders.each((i, holder) => {
      const holderNumber = parseInt($(holder).data('winning-number'), 10)
      if (holderNumber > numbersCount ) {
        $(holder).addClass('-hidden')
        $($(holder).find('INPUT.lottery-winning-percent-input')).attr('type', 'hidden')
      } else {
        $(holder).removeClass('-hidden')
        $($(holder).find('INPUT.lottery-winning-percent-input')).attr('type', 'number')
      }
    })
    checkWinningPercentsState()
  }
  $( '#lottery_numbers_count' ).on('change', function (e) {
    const numbersCount = parseInt( $('#lottery_numbers_count').val(), 10)
    const winningPercentsHolders = $('.lotteryfactory-winning-percent')
    winningPercentsHolders.each((i, holder) => {
      const holderNumber = parseInt($(holder).data('winning-number'), 10)
      if (holderNumber > numbersCount ) {
        $(holder).addClass('-hidden')
        $($(holder).find('INPUT.lottery-winning-percent-input')).attr('type', 'hidden')
      } else {
        $(holder).removeClass('-hidden')
        $($(holder).find('INPUT.lottery-winning-percent-input')).attr('type', 'number')
      }
    })
    checkWinningPercentsState()
  })


  $( numbersCountChange ).on('click', function (e) {
    e.preventDefault();
    if (numbersCountChange.disabled) return

    showLoader()
    numbersCountChange.disabled = true
    const unlockButton = () => {
      numbersCountChange.disabled = false
      hideLoader()
    }

    setLoaderStatus( langMsg( 'Fetch lottery status' ) )
    mcwRefSystem
      .fetchLotteryInfo(lotteryAddress.value)
      .then( (lotteryInfo) => {
        const current = lotteryInfo.currentLotteryInfo
        const numbersCount = parseInt( $('#lottery_numbers_count').val(), 10)
        if ((current.status !== "3") && (lotteryInfo.currentLotteryNumber !== "1")) {
          errMessage( langMsg( 'You can change the number of balls only when the lottery is stopped') )
          unlockButton()
          return
        }
        setLoaderStatus( langMsg( 'Save information about numbers counts to contract' ) )
        mcwRefSystem.setNumbersCount(lotteryAddress.value, numbersCount)
          .then((isOk) => {
            // call ajax save
            setLoaderStatus( langMsg( 'Save local WP configuration' ) )
            ajaxSendData({
              action: 'lotteryfactory_update_options',
              data: {
                postId,
                options: {
                  'numbers_count': numbersCount,
                }
              }
            }).then((ajaxAnswer) => {
              unlockButton()
              showNotice( langMsg( 'Count of balls changed' ) )
            }).catch((isFail) => { unlockButton() })
          })
          .catch((errMsg) => {
            errMessage(errMsg)
            unlockButton()
          })
      })
      .catch((err) => {
        numbersCountChange.disabled = false
        hideLoader()
      })
  })

  $( 'A[data-lottery-action="save-ajax-param"]' ).on('click', function (e) {
    e.preventDefault()
    const $button = $(e.target);
    if ($button[0].disabled) return
    const postTarget = $button.data('lottery-target')
    const postValue = $($button.data('lottery-source')).val()
    const unlockButton = () => {
      $button[0].disabled = false
      hideLoader()
    }
    $button[0].disabled = true
    showLoader()
    setLoaderStatus( langMsg( 'Saving chainges' ) )
    const options = {}
    options[postTarget] = postValue
    
    ajaxSendData({
      action: 'lotteryfactory_update_options',
      data: {
        postId,
        options
      }
    }).then((ajaxAnswer) => {
      console.log('>> save result', ajaxAnswer)
      unlockButton()
      showNotice( langMsg( 'Changes changed' ) )
    }).catch((isFail) => { unlockButton() })
  })

  $( startLottery ).on( 'click', function(e) {
    e.preventDefault()
    if (startLottery.disabled) return
    if (!checkWinningPercentsState()) {
      return errMessage( langMsg( 'Adjust the win percentage to be 100%') )
    }
    const endDate = getValue('lottery_enddate')
    const endTime = getValue('lottery_endtime')
    let ticketPrice = getFloat('lottery_ticket_price')
    let treasuryFee = getNumber('lottery_treasury_fee')
    const tokenDecimals = getNumber('lottery_token_decimals')
    const lotteryContract = getValue('lottery_address')

    if (tokenDecimals === false)
      return errMessage( langMsg( 'Could not determine token dicimals. Inquire about token and try again') )
    if (ticketPrice === false)
      return errMessage( langMsg( 'Enter the ticket price') )
    if (treasuryFee === false)
      return errMessage( langMsg( 'Specify treasury fee') )
    if (ticketPrice <= 0)
      return errMessage( langMsg( 'Ticket price must be greater than zero') )
    if (!(treasuryFee >= 0 && treasuryFee <= 30))
      return errMessage( langMsg( 'The treasury tax must be between 0% and 30%') )
    if (!endDate || endDate === '')
      return errMessage( langMsg( 'Enter date of lottery end') )
    if (!endTime || endTime === '')
      return errMessage( langMsg( 'Enter time of lottery end') )
    if (!lotteryContract)
      return errMessage( langMsg( 'Lottery contract not specified') )


    const lotteryEnd = new Date(endDate + ' ' + endTime).getTime() / 1000

    // winningPercents
    const numbersCount = parseInt( $('#lottery_numbers_count').val(), 10)
    const winningPercents = [
      (numbersCount >= 1) ? parseInt(parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="1"]').val()) * 100, 10) : 0,
      (numbersCount >= 2) ? parseInt(parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="2"]').val()) * 100, 10) : 0,
      (numbersCount >= 3) ? parseInt(parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="3"]').val()) * 100, 10) : 0,
      (numbersCount >= 4) ? parseInt(parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="4"]').val()) * 100, 10) : 0,
      (numbersCount >= 5) ? parseInt(parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="5"]').val()) * 100, 10) : 0,
      (numbersCount >= 6) ? parseInt(parseFloat($('INPUT.lottery-winning-percent-input[data-winning-number="6"]').val()) * 100, 10) : 0,
    ]

    ticketPrice = new BigNumber(ticketPrice).multipliedBy(10 ** tokenDecimals).toFixed()
    treasuryFee = parseInt(treasuryFee*100, 10)
    startLottery.disabled = true
    showLoader()
    setLoaderStatus( langMsg( 'Saving lottery configuration' ) )
    // save last ticket price, treasury fee and winning breaks
    ajaxSendData({
      action: 'lotteryfactory_update_options',
      data: {
        postId,
        options: {
          winning_1: $('INPUT.lottery-winning-percent-input[data-winning-number="1"]').val(),
          winning_2: $('INPUT.lottery-winning-percent-input[data-winning-number="2"]').val(),
          winning_3: $('INPUT.lottery-winning-percent-input[data-winning-number="3"]').val(),
          winning_4: $('INPUT.lottery-winning-percent-input[data-winning-number="4"]').val(),
          winning_5: $('INPUT.lottery-winning-percent-input[data-winning-number="5"]').val(),
          last_ticket_price: $('INPUT#lottery_ticket_price').val(),
          last_treasury_fee: $('INPUT#lottery_treasury_fee').val()
        }
      }
    }).then((ajaxAnswer) => {
      setLoaderStatus( langMsg( 'Starting lottery. Confirm trasaction...' ) )
      mcwRefSystem.startLottery({
        lotteryContract,
        lotteryEnd,
        ticketPrice,
        treasuryFee,
        winningPercents,
      })
        .then((res) => {
          fetchStatusFunc()
          showNotice( langMsg( 'New lottery round started' ) )
        })
        .catch((err) => {
          console.log('>> fail', err)
          startLottery.disabled = false
          hideLoader()
        })
    })
  })

  $( drawNumbers ).on( 'click', function(e) {
    e.preventDefault()
    if (drawNumbers.disabled) return

    const lotteryAddress = getValue('lottery_address')
    const lotterySalt = getValue('lottery_draw_salt')
    if (!lotteryAddress)
      return errMessage('Lottery contract not specified')
    if (!lotterySalt || lotterySalt.length < 128) {
      // Не корректная соль. Соль должна быть 128 или больше символов. Нажмите Сгенерировать чтобы получить новую
      return errMessage('Incorrect draw salt. Salt must be 128 chars or bigger length. Press Generate new salt')
    }

    drawNumbers.disabled = true
    showLoader()
    setLoaderStatus( langMsg( 'Drawing final numbers... confirm trasaction' ) )
    mcwRefSystem
      .drawNumbers(lotteryAddress, lotterySalt)
      .then((res) => {
        console.log('>>> ok', res)
        hideBlock('lottery_draw')
        showBlock('lottery_start')
        hideLoader()
        showNotice( langMsg( 'Final numbers drawed' ) )
      })
      .catch((err) => {
        console.log('>> fail', err)
        drawNumbers.disabled = false
        hideLoader()
      })
  })

  $( '#lotteryfactory_gen_drawsalt' ).on( 'click', function(e) {
    e.preventDefault()
    setValue('lottery_draw_salt', genSalt())
  })

  $( closeAndGoDraw ).on( 'click', function(e) {
    e.preventDefault()
    if (closeAndGoDraw.disabled) return

    const lotteryContract = getValue('lottery_address')
    if (!lotteryContract)
      return errMessage('Lottery contract not specified')

    const unlockButton = () => {
      closeAndGoDraw.disabled = false
      hideLoader()
    }
    closeAndGoDraw.disabled = true
    showLoader()
    setLoaderStatus( langMsg( 'Closing lottery round. Confirm transaction') )
    mcwRefSystem
      .closeLottery(lotteryContract)
      .then((res) => {
        unlockButton()
        hideBlock('lottery_round')
        showBlock('lottery_draw')
        showNotice( langMsg( 'Lottery round closed. Draw round numbers' ) )
      })
      .catch((err) => {
        console.log('>> fail', err)
        unlockButton()
      })
  })

  $( fetchStatus ).on( 'click', function(e) {
    e.preventDefault()
    fetchStatusFunc()
  })

  const fillLotteryData = (lotteryInfo) => {
    const breakDowns = lotteryInfo.currentLotteryInfo.rewardsBreakdown
    $('INPUT[data-winning-number="1"]').val(breakDowns[0]/ 100)
    $('INPUT[data-winning-number="2"]').val(breakDowns[1]/ 100)
    $('INPUT[data-winning-number="3"]').val(breakDowns[2]/ 100)
    $('INPUT[data-winning-number="4"]').val(breakDowns[3]/ 100)
    $('INPUT[data-winning-number="5"]').val(breakDowns[4]/ 100)
    $('#lottery_numbers_count').val(lotteryInfo.numbersCount)
  }

  $( fetchButton ).on( 'click', function(e) {
    e.preventDefault();
    if (fetchButton.disabled) return
    if (!lotteryAddress.value) return errMessage('Enter contract address')

    hideBlock('lottery_token_info')
    hideBlock('lottery_info')
    showLoader()
    fetchButton.disabled = true
    setLoaderStatus( langMsg( 'Fetching current lottery status from contract' ) )
    mcwRefSystem
      .fetchLotteryInfo(lotteryAddress.value)
      .then( (lotteryInfo) => {
        console.log('>>>> lotteryInfo', lotteryInfo)
        setTokenInfo(lotteryInfo.token)
        tokenAddress.value = lotteryInfo.token.tokenAddress
        setHtml('lottery_owner', lotteryInfo.owner)
        setHtml('lottery_operator', lotteryInfo.operator)
        setHtml('lottery_treasury', lotteryInfo.treasury)
        setHtml('lottery_current', lotteryInfo.currentLotteryNumber)
        // fill break downs
        if (lotteryInfo.currentLotteryInfo
          && lotteryInfo.currentLotteryNumber !== "1"
        ) {
          fillLotteryData(lotteryInfo)
        }
        showBlock('lottery_info')
        hideLoader()
        showNotice( langMsg( 'Lottery info fetched from contract {address}', { address: lotteryAddress.value } ) )
        fetchButton.disabled = false
      })
      .catch((e) => {
        console.log(e)
        hideLoader()
        fetchButton.disabled = false
        alert('Fail fetch contract info')
      })
  })
*/
  $( fetchToken ).on( 'click', function(e) {
		e.preventDefault();
    if (fetchToken.disabled) return
    hideBlock('refsystem_token_info')

    if (!tokenAddress.value) return errMessage('Enter token address')

    showLoader()
    setLoaderStatus( langMsg( 'Fetching information about token' ) )
    fetchToken.disabled = true
    mcwRefSystem
      .fetchTokenInfo(tokenAddress.value)
      .then((tokenInfo) => {
        setTokenInfo(tokenInfo)
        hideLoader()
        showBlock('refsystem_deploy_holder')
        showNotice( langMsg( 'Token info fetched from address {tokenAddress}', { tokenAddress: tokenAddress.value } ) )
        fetchToken.disabled = false
      })
      .catch((e) => {
        console.log(e)
        hideLoader()
        hideBlock('refsystem_deploy_holder')
        fetchToken.disabled = false
        alert('Fail fetch token info')
      })
  })

	$( deployButton ).on( 'click', function(e) {
		e.preventDefault();

    console.log('click')
    if (deployButton.disabled) {
      console.log('>>')
      return
    }
    if (!tokenAddress.value) return errMessage('Enter token address')
    if (!isEvmAddress(tokenAddress.value)) return errMessage('Token address not valid')
    if (!isEvmAddress(oracleAddress.value)) return errMessage('Oracle address not valid')
    if (!tokenSymbol.value) return errMessage('Invalid token')
    if (parseFloat(rewardAmount.value) <=0) return errMessage('Invalid reward amount')
    const decimals = parseInt(tokenDecimals.value)
    const rewardAmountWei = new BigNumber(rewardAmount.value).multipliedBy(10 ** decimals).toFixed()
    console.log('> reward amount wei', rewardAmountWei)
    deployButton.disabled = true;
    showLoader()
    setLoaderStatus( langMsg( 'Deploying referrals programs contract to blockchain. Confirm transaction' ) )

    mcwRefSystem.deploy({
      tokenAddress: tokenAddress.value,
      rewardAmount: rewardAmountWei,
      oracleAddress: oracleAddress.value,
      onTrx: (trxHash) => {
        setLoaderStatus( langMsg(`Transaction hash: ${trxHash}. Waiting ... `) )
        showNotice(`Transaction hash: ${trxHash}. Send this hash to the support if you have a problem with deploy.`)
      },
      onSuccess: (address) => {
        console.log('Contract address:', address);
        contractAddress.value = address
        deployButton.disabled = false;
        hideLoader()
        //lotteryAddress.value = address;
        showNotice( langMsg( 'Referral program contract deployed' ) )
        showBlock( 'refsystem_deployed_holder' )
        hideBlock( 'refsystem_deploy_new_holder' )
      },
      onError: (err) => {
        console.error(err);
        deployButton.disabled = false;
        hideLoader()
        alert(err);
      }
    })
	});
/*
  $( selectedChain ).on( 'change', function(e) {
		e.preventDefault();

    mcwRefSystem
      .setSelectedChain(e.target.value)
  });
*/
  const fetchInfo = () => {
    showLoader()
    try {
      mcwRefSystem.fetchContractInfo(document.getElementById('mcw_configured_referral_contract').value).then((answer) => {
        console.log('>>> contract info', answer)
        /*
        tokenBalance,
        tokenDecimals,
        tokenSymbol,
        oracleAddress,
        oracleBalance,
        owner,
        rewardToken,
        rewardAmount,
        tokensEarned,
        tokensPending,
        usersCount
        */
        setHtml('refsystem_oracle_address', answer.oracleAddress)
        setHtml('refsystem_contract_owner', answer.owner)
        setHtml('refsystem_reward_token', answer.rewardToken)
        const oracleBalance = new BigNumber(answer.oracleBalance)
          .div(new BigNumber(10).pow(18))
          .toNumber()
        setHtml('refsystem_oracle_balance_holder', oracleBalance)
        const tokenBalance = new BigNumber(answer.tokenBalance)
          .div(new BigNumber(10).pow(answer.tokenDecimals))
          .toNumber()
        setHtml('refsystem_contract_balance', tokenBalance)
        const rewardAmount = new BigNumber(answer.rewardAmount)
          .div(new BigNumber(10).pow(answer.tokenDecimals))
          .toNumber()
        setHtml('refsystem_contract_reward_amount', rewardAmount)
        setValue('refsystem_reward_amount_new', rewardAmount)
        const tokensEarned = new BigNumber(answer.tokensEarned)
          .div(new BigNumber(10).pow(answer.tokenDecimals))
          .toNumber()
        setHtml('refsystem_reward_allocated', tokensEarned)
        const tokensPending = new BigNumber(answer.tokensPending)
          .div(new BigNumber(10).pow(answer.tokensPending))
          .toNumber()
        setHtml('refsystem_reward_pending', tokensPending)
        setValue('refsystem_token_decimals_at_contract', answer.tokenDecimals)
        
        hideLoader()
      }).catch((err) => {
        hideLoader()
        console.log('Fail fetch referral system contract info', err)
      })
    } catch (err) {
      console.log(err)
      hideLoader()
    }
  }
  $('A[data-mcw-action="update"]').on('click', (e) => {
    console.log('CLICK')
    e.preventDefault()
    showLoader()
    fetchInfo()
  })
  $('#refsystem_contract_balance_withdraw').on('click', (e) => {
    e.preventDefault()
    showLoader()
    setLoaderStatus( langMsg('Withdraw tokens. Confirm transaction') )
    mcwRefSystem.callContractMethod(
      document.getElementById('mcw_configured_referral_contract').value,
      'withdrawTokens',
      [], {
        transactionHash: (txHash) => {
          setLoaderStatus( langMsg('TxId: ' + txHash + '. Waiting...') )
          console.log('>>> txHash', txHash)
        },
        onError: (err) => {
          hideLoader()
          console.log('>>> onError', err)
        },
        onReceipt: () => {
          hideLoader()
          fetchInfo()
          console.log('>>> onReceipt')
        }
      }
    )
  })
  $('#refsystem_allocate_pending').on('click', (e) => {
    e.preventDefault()
    showLoader()
    setLoaderStatus( langMsg('Allocate pending reward. Confirm transaction') )
    mcwRefSystem.callContractMethod(
      document.getElementById('mcw_configured_referral_contract').value,
      'sendPendingReward',
      [], {
        transactionHash: (txHash) => {
          setLoaderStatus( langMsg('TxId: ' + txHash + '. Waiting...') )
          console.log('>>> txHash', txHash)
        },
        onError: (err) => {
          hideLoader()
          console.log('>>> onError', err)
        },
        onReceipt: () => {
          hideLoader()
          fetchInfo()
          console.log('>>> onReceipt')
        }
      }
    )
  })
  $('#refsystem_reward_amount_change').on('click', (e) => {
    e.preventDefault()
    const decimals = parseInt(document.getElementById('refsystem_token_decimals_at_contract').value)
    const rewardAmountWei = new BigNumber(document.getElementById('refsystem_reward_amount_new').value).multipliedBy(10 ** decimals).toFixed()
    showLoader()
    setLoaderStatus( langMsg('Change reward amount. Confirm transaction') )
    mcwRefSystem.callContractMethod(
      document.getElementById('mcw_configured_referral_contract').value,
      'setRewardAmount',
      [
        rewardAmountWei
      ], {
        transactionHash: (txHash) => {
          setLoaderStatus( langMsg('TxId: ' + txHash + '. Waiting...') )
          console.log('>>> txHash', txHash)
        },
        onError: (err) => {
          hideLoader()
          console.log('>>> onError', err)
        },
        onReceipt: () => {
          hideLoader()
          fetchInfo()
          hideBlock('refsystem_reward_amount_edit')
          showBlock('refsystem_reward_amount_view')
          console.log('>>> onReceipt')
        }
      }
    )
  })
  $('#mcwallet_ref_initmetamask').on('click', async (e) => {
    e.preventDefault()
    try {
      await mcwRefSystem.connectMetamask()
      const account = await mcwRefSystem.getActiveAccount()
      if (account) {
        hideBlock('refsystem_holder_connect_wallet')
        showBlock('refsystem_holder_connected_wallet')
      } else {
        showBlock('refsystem_holder_connect_wallet')
        hideBlock('refsystem_holder_connected_wallet')
      }
    } catch (e) {}
  })
  $('#mcwallet_ref_blockchain').on( 'change', function(e) {
    console.log('change chain')
		//e.preventDefault();
    if (e.target.value && e.target.value != "") {
      mcwRefSystem
        .setSelectedChain(e.target.value)
      $('.showOnChainSelected').each((i,el) => {
        el.style.display = ''
      })
    } else {
      $('.showOnChainSelected').each((i,el) => {
        el.style.display = 'none'
      })
    }
  });
})( jQuery );
console.log('>>> REF ADMIN')