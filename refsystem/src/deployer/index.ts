import json from '../../contracts/artifacts/ReferralSystem.json'
import formatAmount from './formatAmount'
import { injectModalsRoot } from './modals'
import infoModal from './infoModal'
import connectModal from './connectModal'
import { accountUnlockedStorageKey, AVAILABLE_NETWORKS_INFO } from './constants'
import { getState, setState } from './state'
import setupWeb3 from './setupWeb3'
import TokenAbi from 'human-standard-token-abi'
import { BigNumber } from 'bignumber.js'
import sha256 from 'js-sha256'

const loadScript = (src) => new Promise((resolve, reject) => {
  const script = document.createElement('script')

  script.onload = resolve
  script.onerror = reject
  script.src = src

  document.head.appendChild(script)
})

type Params = {
  tokenAddress: string
  rewardAmount: string
  oracleAddress: string
  onTrx: (trxHash: string) => void
  onSuccess: (address: string) => void
  onError: (error: Error | string) => void
  onFinally: () => void
}


const genSalt = () => {
  let result = ''
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
  const charactersLength = characters.length
  for ( var i = 0; i < 128; i++ ) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength))
  }
  return result
}

const deploy = async (params: Params) => {
  const { abi, data: { bytecode: { object: bytecode } } } = json
  const {
    tokenAddress,
    rewardAmount,
    oracleAddress
  } = params
  const { web3 } = getState()

  const onTrx = params.onTrx || (() => {})
  const onSuccess = params.onSuccess || (() => {})
  const onError = params.onError || (() => {})
  const onFinally = params.onFinally || (() => {})

  if (!tokenAddress) {
    onError('All fields should be filled: tokenAddress.')
    return
  }

  let contract
  let accounts

  try {
    contract = new web3.eth.Contract(abi)
    accounts = await window.ethereum.request({ method: 'eth_accounts' })
  }
  catch (err) {
    onError(err)
    return
  }

  if (!accounts || !accounts[0]) {
    onError('Wallet account is undefined.')
    return
  }

  contract.deploy({
    data: '0x' + bytecode,
    arguments: [ tokenAddress, rewardAmount, oracleAddress ],
  })
    .send({
      from: accounts[0],
      //gasPrice: '0x' + new BigNumber('600000000000').toString(16),
      gas: 6000000,
    })
    .on('transactionHash', (hash) => {
      console.log('transaction hash:', hash)
      onTrx(hash)
    })
    .on('error', (error) => {
      console.log('transaction error:', error)
      onError(error)
    })
    .on('receipt', (receipt) => {
      console.log('transaction receipt:', receipt)
      onSuccess(receipt.contractAddress)
    })
    .then(() => {
      onFinally()
    })
}

const handleError = (err) => {
  const { opts } = getState()

  if (typeof opts.onError === 'function') {
    opts.onError(err)
  }
}

const getActiveAccount = async () => {
  try {
    const accounts = await window.ethereum.request({ method: 'eth_accounts' })
    return accounts[0]
  } catch (err) {
    return false
  }
}

const initMetamask = async () => {
  const isAccountUnlocked = localStorage.getItem(accountUnlockedStorageKey) === 'true'

  if (isAccountUnlocked) {
    try {
      const accounts = await window.ethereum.request({ method: 'eth_accounts' })

      if (!accounts[0]) {
        localStorage.removeItem(accountUnlockedStorageKey)
        connectModal.open()
      }
      else {
        setState({ account: accounts[0] })

        await setupWeb3()

        const { opts } = getState()

        if (typeof opts.onFinishLoading === 'function') {
          opts.onFinishLoading()
        }
      }
    }
    catch (err) {
      console.error(err)
      localStorage.removeItem(accountUnlockedStorageKey)
      connectModal.open()
    }
  }
  else {
    connectModal.open()
  }
}

const connectMetamask = async () => {
  if (!window.ethereum) {
    infoModal.open({
      message: `
        <div class="install-metamask">
          <img src="https://swaponline.github.io/images/metamask_45038d.svg" /><br />
          Please install MetaMask
        </div>
      `,
    })

    return Promise.reject()
  }

  return new Promise<void>((resolve) => {
    const interval = setInterval(async () => {
      if (window.ethereum.networkVersion) {
        console.log('>>> wait metamask')
        clearInterval(interval)
        await initMetamask()
        window.ethereum.on('networkChanged', initMetamask)
        resolve()
      }
    }, 500)
  })
}
const getContract = (contractAddress: string): Promise<any> => {
  return new Promise(async (resolve, reject) => {
    const { abi } = json

    const { web3 } = getState()

    let contract
    let accounts

    try {
      contract = new web3.eth.Contract(abi, contractAddress)
      accounts = await window.ethereum.request({ method: 'eth_accounts' })
    } catch (err) {
      reject(err)
      return
    }

    if (!accounts || !accounts[0]) {
      reject('Wallet account is undefined.')
      return
    }
    resolve({
      contract,
      account: accounts[0],
    })
  })
}

const getTokenContract = (tokenAddress: string): Promise<any> => {
  return new Promise(async (resolve, reject) => {
    const { abi } = json

    const { web3 } = getState()

    let contract
    let accounts

    try {
      contract = new web3.eth.Contract(TokenAbi, tokenAddress)
      accounts = await window.ethereum.request({ method: 'eth_accounts' })
    } catch (err) {
      reject(err)
      return
    }

    if (!accounts || !accounts[0]) {
      reject('Wallet account is undefined.')
      return
    }
    resolve({
      contract,
      account: accounts[0],
    })
  })
}
/*
const drawNumbers = (lotteryContract, userSalt) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      const { abi } = json

      const { web3 } = getState()

      let contract
      let accounts

      try {
        contract = new web3.eth.Contract(abi, lotteryContract)
        accounts = await window.ethereum.request({ method: 'eth_accounts' })
      } catch (err) {
        reject(err)
        return
      }

      if (!accounts || !accounts[0]) {
        reject('Wallet account is undefined.')
        return
      }

      const txArguments = {
        from: accounts[0],
        gas: '0',
        type:"0x0",
        //gasPrice: '0x' + new BigNumber('600000000000').toString(16),
      }

      const lotteryRandSalt = genSalt()
      // @ts-ignore
      const calcedHash = sha256(lotteryRandSalt + userSalt)
      // get current lottery ids
      const lotteryId = await contract.methods.viewCurrentLotteryId().call()


      const gasAmountCalculated = await contract.methods
        .drawFinalNumberAndMakeLotteryClaimable(lotteryId, '0x' + calcedHash, true)
        .estimateGas(txArguments)

      const gasAmounWithPercentForSuccess = new BigNumber(
        new BigNumber(gasAmountCalculated)
          .multipliedBy(1.05) // + 5% -  множитель добавочного газа, если будет фейл транзакции - увеличит (1.05 +5%, 1.1 +10%)
          .toFixed(0)
      ).toString(16)

      txArguments.gas = '0x' + gasAmounWithPercentForSuccess

      contract.methods
        .drawFinalNumberAndMakeLotteryClaimable(lotteryId, '0x' + calcedHash, true)
        .send(txArguments)
        .on('transactionHash', (hash) => {
          console.log('transaction hash:', hash)

        })
        .on('error', (error) => {
          console.log('transaction error:', error)

        })
        .on('receipt', (receipt) => {
          console.log('transaction receipt:', receipt)

        })
        .then(() => {
          console.log('alll ready')
          resolve(true)
        })
    })
  })
}
*/
const callContractMethod = (contractAddress, method, args: any[], callbacks: any = {}) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      try {
        const { contract, account } = await getContract(contractAddress)
        console.log('>>> account', account)
        const {
          transactionHash,
          onError,
          onReceipt,
        } = callbacks

        const txArguments = {
          from: account,
          type:"0x0",
          //gas: '0',
          //gasPrice: '0x' + new BigNumber('600000000000').toString(16),
        }
        /*
        const gasAmountCalculated = await contract.methods
          [method](...args)
          .estimateGas(txArguments)

        const gasAmounWithPercentForSuccess = new BigNumber(
          new BigNumber(gasAmountCalculated)
            .multipliedBy(1.05) // + 5% -  множитель добавочного газа, если будет фейл транзакции - увеличит (1.05 +5%, 1.1 +10%)
            .toFixed(0)
        ).toString(16)

        txArguments.gas = '0x' + gasAmounWithPercentForSuccess
        */
        console.log('>>>> call tx arguments', txArguments)
        contract.methods
          [method](...args)
          .send(txArguments)
          .on('transactionHash', (hash) => {
            if (typeof transactionHash === 'function') {
              transactionHash(hash)
            }
          })
          .on('error', (error) => {
            if (typeof onError === 'function') {
              console.log('transaction error:', error)
              onError(error)
            }
          })
          .on('receipt', (receipt) => {
            if (typeof onReceipt === 'function') {
              console.log('transaction receipt:', receipt)
              onReceipt(receipt)
            }
          })
          .then(() => {
            console.log('all ready')
            resolve(true)
          })
      } catch (err) {
        reject(err)
      }
    })
  })
}
/*
const callTokenMethod = (tokenAddress, method, args: any[], callbacks: any = {}) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async (web3) => {
      try {
        const { contract, account } = await getTokenContract(tokenAddress)
        const {
          transactionHash,
          onError,
          onReceipt,
        } = callbacks

        const txArguments = {
          from: account,
          type:"0x0",
          gas: '0',
          //gasPrice: '0x' + new BigNumber('600000000000').toString(16),
          //gasPrice: '0x' + new BigNumber('50000000000').toString(16),
          //maxPriorityFeePerGas: '0x' + new BigNumber('10000000000').toString(16),
          //maxFeePerGas: '0x' + new BigNumber('50000000000').toString(16),
        }
        console.log('>>> web3', web3)
        //const latest_block = await web3.eth.get_block("latest")
        //console.log('>>> latest_block', latest_block)
        console.log('>>> tx args 1', txArguments)
        const gasAmountCalculated = await contract.methods
          [method](...args)
          .estimateGas(txArguments)

        const gasAmounWithPercentForSuccess = new BigNumber(
          new BigNumber(gasAmountCalculated)
            .multipliedBy(1.05) // + 5% -  множитель добавочного газа, если будет фейл транзакции - увеличит (1.05 +5%, 1.1 +10%)
            .toFixed(0)
        ).toString(16)

        txArguments.gas = '0x' + gasAmounWithPercentForSuccess
console.log('>>> call token arguments', txArguments)
        const txArgs = {
          from: account,
          //gas: txArguments.gas,
          maxPriorityFeePerGas: '0x' + new BigNumber('10000000000').toString(16)
        }
        console.log('>>> txArgs', txArguments)
        contract.methods
          [method](...args)
          .send(txArguments)
          .on('transactionHash', (hash) => {
            if (typeof transactionHash === 'function') {
              transactionHash(hash)
            }
          })
          .on('error', (error) => {
            if (typeof onError === 'function') {
              console.log('transaction error:', error)
              onError(error)
            }
          })
          .on('receipt', (receipt) => {
            if (typeof onReceipt === 'function') {
              console.log('transaction receipt:', receipt)
              onReceipt(receipt)
            }
          })
          .then(() => {
            console.log('all ready')
            resolve(true)
          })
      } catch (err) {
        reject(err)
      }
    })
  })
}

const updateRewardAmount = (contractAddress: string, newAmountWei: string) => {
  return new Promise(async (resolve, reject) => {
    const {
      contract,
      account
    } = new await getContract(contractAddress)
    
  })
}
*/
const fetchContractInfo = (contractAddress: string) => {
  return new Promise(async (resolve,reject) => {
    const { web3 } = getState()
    const { abi } = json
    try {
      const contract = new web3.eth.Contract(abi, contractAddress)
      
      const tokenBalance = await contract.methods.getBalance().call()
      const tokenDecimals = await contract.methods.getDecimals().call()
      const tokenSymbol = await contract.methods.getSymbol().call()
      const oracleAddress = await contract.methods.oracle().call()
      const owner = await contract.methods.owner().call()
      const oracleBalance = await contract.methods.getOracleBalance().call()
      const rewardToken = await contract.methods.rewardToken().call()
      const rewardAmount = await contract.methods.rewardAmount().call()
      const tokensEarned = await contract.methods.tokensEarned().call()
      const tokensPending = await contract.methods.tokensPending().call()
      const usersCount = await contract.methods.usersCount().call()
      
      resolve( {
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
      } )
    } catch (err) {
      reject(err)
    }
  })
}

const isCorrectAddress = (address: string): boolean => {
  return window.Web3.utils.isAddress(address)
}
// setMinAndMaxTicketPriceInCake
// setMaxNumberTicketsPerBuy
/*
// injectFunds
const injectFunds = (lotteryAddress: string, weiAmount: BigNumber, callbacks: any = {}) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      try {
        // get current lottery
        const { contract, account } = await getContract(lotteryAddress)
        const currentLotteryId = await contract.methods.viewCurrentLotteryId().call()
        const result = await callLotteryMethod(lotteryAddress, 'injectFunds', [ currentLotteryId, weiAmount ], callbacks)
        resolve(result)
      } catch (err) {
        reject(err)
      }
    })
  })
}

const setOperatorAddress = (lotteryAddress: string, operatorAddress: string, callbacks: any = {}) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      try {
        const result = await callLotteryMethod(lotteryAddress, 'setOperatorAddresses', [ operatorAddress ], callbacks)
        resolve(result)
      } catch (err) {
        reject(err)
      }
    })
  })
}

const setNumbersCount = (lotteryAddress: string, numbersCount: number, callbacks: any = {}) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      try {
        const result = await callLotteryMethod(lotteryAddress, 'setNumbersCount', [ numbersCount ], callbacks)
        resolve(result)
      } catch (err) {
        reject(err)
      }
    })
  })
}

const getNumbersCount = (lotteryAddress: string) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      try {
        const { contract, address } = await getContract(lotteryAddress)
        const numbersCount = await contract.methods.numbersCount().call()
        resolve(parseInt(numbersCount, 10))
      } catch (err) {
        reject(err)
      }
    })
  })
}

const fetchLotteryInfo = (lotteryAddress: string) => {
  return new Promise((resolve, reject) => {
    const {
      web3: checkWeb3,
      account: checkAccount
    } = getState()

    const fetchCallback = async () => {
      const {
        web3,
        account
      } = getState()

      try {
        const lottery = new web3.eth.Contract(json.abi, lotteryAddress, {
          from: account,
        })
// @ts-ignore
window.lotteryContract = lottery
        const owner = await lottery.methods.owner().call()
        const operator = await lottery.methods.operatorAddress().call()
        const treasury = await lottery.methods.treasuryAddress().call()
        const numbersCount = await lottery.methods.numbersCount().call()
        const currentLotteryNumber = await lottery.methods.viewCurrentLotteryId().call()
        const currentLotteryInfo = await lottery.methods.viewLottery(currentLotteryNumber).call()
        const tokenAddress = await lottery.methods.cakeToken().call()
        if (tokenAddress) {
          const tokenInfo = await fetchTokenInfo(tokenAddress)
          resolve({
            activeAccount: account,
            contract: lotteryAddress,
            owner: owner,
            operator: operator,
            treasury: treasury,
            currentLotteryNumber: currentLotteryNumber,
            currentLotteryInfo: currentLotteryInfo,
            token: tokenInfo,
            numbersCount: parseInt( numbersCount, 10)
          })
        } else {
          reject()
        }
      } catch (e) { reject() }
    }
    if (!checkWeb3) {
      connectMetamask().then(() => {
        fetchCallback()
      })
    } else {
      fetchCallback()
    }
  })
}
*/
const waitMetamask = (callback) => {
  const {
    web3: checkWeb3,
  } = getState()

  if (!checkWeb3) {
    console.log(' not connected')
    connectMetamask().then(() => {
      console.log('connected')

      checkSelectedChain().then(() => {
        callback(checkWeb3)
      })
    })
  } else {
    console.log('connected - call')

    checkSelectedChain().then(() => {
      callback(checkWeb3)
    })
  }
}

const checkSelectedChain = async () => {
  const {
    web3,
    selectedChain,
  } = getState()

  const { networkVersion } = getChainInfoBySlug(selectedChain)

  if (web3?.currentProvider?.networkVersion !== networkVersion.toString()) {
    await switchOrAddChain()
  } else {
    console.log('Selected Chain checked successfully')
  }
}

const switchOrAddChain = async () => {
  const {
    selectedChain,
  } = getState()

  const {
    chainId,
    chainName,
    rpcUrls,
    blockExplorerUrls,
    nativeCurrency,
  } = getChainInfoBySlug(selectedChain)

  const params = [
    {
      chainId,
      chainName,
      rpcUrls,
      blockExplorerUrls,
      nativeCurrency,
    }
  ]

  try {
    await window.ethereum.request({
      method: 'wallet_switchEthereumChain',
      params: [{ chainId: `0x${Number(chainId).toString(16)}` }],
    });
  } catch (switchError) {
    // This error code indicates that the chain has not been added to MetaMask.
    if (switchError.code === 4902) {
      try {
        await window.ethereum.request({
          method: 'wallet_addEthereumChain',
          params,
        });
      } catch (addError) {
        // handle "add" error
      }
    } else {
      console.error('Switch chain error: ', switchError.message)
    }

  }
}

const getChainInfoBySlug = (chainSlug: string) => AVAILABLE_NETWORKS_INFO.find(networkInfo => networkInfo.slug === chainSlug)

const fetchTokenAllowance = (tokenAddress: string, lotteryAddress: string) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      const {
        web3,
        account
      } = getState()

      try {
        const tokenContract = new web3.eth.Contract(TokenAbi, tokenAddress, {
          from: account,
        })
        const allowance = await tokenContract.methods.allowance(account, lotteryAddress).call()
        resolve(allowance)
      } catch (e) { reject() }
    })
  })
}
/*
const approveInjectFunds = (tokenAddress: string, lotteryAddress: string, weiAmount: BigNumber, callbacks: any) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      const {
        web3,
        account
      } = getState()
      try {
        try {
          const result = await callTokenMethod(tokenAddress, 'approve', [ lotteryAddress, weiAmount ], callbacks)
          resolve(result)
        } catch (err) {
          reject(err)
        }
        callTokenMethod
        const tokenContract = new web3.eth.Contract(TokenAbi, tokenAddress, {
          from: account,
        })
        const allowance = await tokenContract.methods.allowance(account, lotteryAddress).call()
        resolve(allowance)
      } catch (e) { reject() }
    })
  })
}

const checkNeedApprove = (tokenAddress: string, lotteryAddress: string, weiAmount: BigNumber) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      const {
        web3,
        account
      } = getState()
      try {
        const allowanceWei = await fetchTokenAllowance(tokenAddress, lotteryAddress)
        // @ts-ignore
        const needApprove = !weiAmount.isLessThan(allowanceWei)
        resolve(needApprove)
      } catch (e) { reject() }
    })
  })
}
*/
const fetchTokenInfo = (tokenAddress: string) => {
  return new Promise((resolve, reject) => {
    waitMetamask(async () => {
      const {
        web3,
        account
      } = getState()

      try {
        const tokenContract = new web3.eth.Contract(TokenAbi, tokenAddress, {
          from: account,
        })
        const name = await tokenContract.methods.name().call()
        const symbol = await tokenContract.methods.symbol().call()
        const decimals = await tokenContract.methods.decimals().call()
        resolve({
          tokenAddress,
          name,
          symbol,
          decimals
        })
      } catch (e) { reject() }
    })
  })
}

const init = async (opts) => {
  setState({ opts })

  if (typeof opts.onStartLoading === 'function') {
    opts.onStartLoading()
  }

  try {
    await Promise.all([
      loadScript('https://cdnjs.cloudflare.com/ajax/libs/web3/1.3.1/web3.min.js'),
      loadScript('https://cdnjs.cloudflare.com/ajax/libs/bignumber.js/8.0.2/bignumber.min.js'),
      loadScript('https://cdn.jsdelivr.net/npm/ethereum-hdwallet@latest/ethereum-hdwallet.js'),
    ])
    console.log('inject modals root')
    injectModalsRoot()
    console.log('>>>')

    //await connectMetamask()
    if (typeof opts.onFinishLoading === 'function') {
      opts.onFinishLoading()
    }
  }
  catch (err) {
    handleError(err)
  }
}

const setSelectedChain = (selectedChain: string) => {
  setState({ selectedChain })

  waitMetamask(()=> console.log('Successfully set', selectedChain, 'chain'))
}

const getWalletFromMnemonic = (mnemonic: string): any => {
  // @ts-ignore
  const hdwallet = HDWallet.fromMnemonic(mnemonic)
  // @ts-ignore
  const wallet = hdwallet.derive(`m/44'/60'/0'/0/0`)
  return {
    address: `0x${wallet.getAddress().toString('hex')}`,
    privateKey: `0x${wallet.getPrivateKey().toString('hex')}`,
    mnemonic,
  }
}

export default {
  init,
  deploy,
  fetchTokenInfo,
  setSelectedChain,
  isCorrectAddress,
  getActiveAccount,
  getWalletFromMnemonic,
  fetchContractInfo,
  callContractMethod,
  connectMetamask
}
