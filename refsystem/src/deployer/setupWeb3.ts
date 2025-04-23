import { setState, getState } from './state'

const networks = {
  mainnet: 'https://mainnet.infura.io/v3/5ffc47f65c4042ce847ef66a3fa70d4c',
  ropsten: 'https://ropsten.infura.io/v3/5ffc47f65c4042ce847ef66a3fa70d4c',
  kovan: 'https://kovan.infura.io/v3/5ffc47f65c4042ce847ef66a3fa70d4c',
  matic_testnet: 'https://rpc-mumbai.maticvigil.com',
  flare_mainnet: 'https://flare-api.flare.network/ext/C/rpc',
  base_mainnet: 'https://mainnet.base.org',
  eth_sepolia: 'https://eth-sepolia.g.alchemy.com/v2/eV40AoRwFdzusyW_9htirAoRXSMssQ0E',
  bsc_mainnet: 'https://bsc-dataseed.binance.org/',
  bsc_testnet: 'https://data-seed-prebsc-1-s1.binance.org:8545/'
}

const setupWeb3 = () => new Promise((resolve, reject) => {
  const activeNetworkName = ({
    1: 'mainnet',
    3: 'ropsten',
    42: 'kovan',
    14: 'flare_mainnet',
    80001: 'matic_testnet',
    8453: 'base_mainnet',
    11155111: 'eth_sepolia',
    56: 'bsc_mainnet',
    97: 'bsc_testnet'
    
  })[window.ethereum && window.ethereum.networkVersion]

  const network = networks[activeNetworkName]
  const web3 = new window.Web3(window.ethereum || window.Web3.givenProvider || new window.Web3.providers.HttpProvider(network))

  if (web3) {
    setState({ web3 })
    resolve()
  }
  else {
    reject()
  }
})


export default setupWeb3
