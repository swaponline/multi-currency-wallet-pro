const WEB3 = require('web3')
const { getEthLikeWallet } = require('./getEthLikeWallet')
const { CHAINS } = require('./chains')

const initWeb3 = (chain, mnemonic) => {
  const rpc = CHAINS[chain]
  console.log('>>> Initing Web3 on ', rpc)
  
  const web3 = new WEB3(new WEB3.providers.HttpProvider(rpc))
  const wallet = getEthLikeWallet({ mnemonic })
  
  console.log('>>> Account: ', wallet.address)

  const account = web3.eth.accounts.privateKeyToAccount( wallet.privateKey )
  console.log('>>> account', account)
  const t = web3.eth.accounts.wallet.add( account.privateKey )
  console.log(" after add", t)
  console.log('>>> Web3 inited')
  return web3
}

module.exports = { initWeb3 }