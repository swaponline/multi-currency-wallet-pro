const ContractMeta = require("./abi/ReferralSystem.json")
const { BigNumber } = require('bignumber.js')

const calcSendArgWithFee = async (account, contract, method, args, weiAmount) => {
  const txArguments = {
    from: account,
    gas: '0'
  }

  if (weiAmount) txArguments.value = new BigNumber(weiAmount)

  const gasAmountCalculated = await contract.methods
    [method](...args)
    .estimateGas(txArguments)

  const gasAmounWithPercentForSuccess = new BigNumber(
    new BigNumber(gasAmountCalculated)
      .multipliedBy(1.15) // + 15% -  множитель добавочного газа, если будет фейл транзакции - увеличит (1.05 +5%, 1.1 +10%)
      .toFixed(0)
  ).toString(16)

  txArguments.gas = '0x' + gasAmounWithPercentForSuccess
  return txArguments
}

const doRegister = (options) => {
  const {
    activeWeb3,
    fromWallet,
    contractAddress,
    UserId,
    UserNickName,
    UserEmail,
    ReferrerId,
    ReferrerNickName,
    ReferrerEmail,
    ReferrerAddress
  } = options
  

  return new Promise((resolve, reject) => {
    activeWeb3.eth.getAccounts().then(async (accounts) => {
      console.log('>>> accounts', accounts)

      if (accounts.length>0 || true) {
        const activeWallet = (accounts.length > 0) ? accounts[0] : fromWallet

        const contract = new activeWeb3.eth.Contract(ContractMeta.abi, contractAddress)
/*
        const sendArgs = await calcSendArgWithFee(
          activeWallet,
          contract,
          'registerUser',
          [
            UserId,
            UserNickName,
            UserEmail,
            ReferrerId,
            ReferrerNickName,
            ReferrerEmail,
            ReferrerAddress
          ],
          0
        )
  */

        const sendArgs = {
          from: activeWallet,
          gas: 300000
        }

        const gasPrice = await activeWeb3.eth.getGasPrice()
        sendArgs.gasPrice = gasPrice

        let txHash
        contract.methods.registerUser(
          UserId,
          UserNickName,
          UserEmail,
          ReferrerId,
          ReferrerNickName,
          ReferrerEmail,
          ReferrerAddress
        )
          .send(sendArgs)
          .on('transactionHash', (hash) => {
            console.log(`>>>> txHash`, hash)
            txHash = hash
          })
          .on('error', (error) => {
            console.log('transaction error:', error)
            reject(error)
          })
          .on('receipt', (receipt) => {
            
          })
          .then((res) => {
            resolve(txHash)
          }).catch((err) => {
            reject(err)
          })
      } else {
        reject('NO_ACTIVE_ACCOUNT')
      }
    }).catch((err) => {
      reject(err)
    })
  })
}


module.exports = { doRegister }