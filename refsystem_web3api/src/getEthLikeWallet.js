
const { hdkey } = require('ethereumjs-wallet')
const bip39 = require('bip39')

const convertMnemonicToValid = (mnemonic) => {
  return mnemonic
    .trim()
    .toLowerCase()
    .split(` `)
    .filter((word) => word)
    .join(` `)
}

const getEthLikeWallet = (params) => {
  const { mnemonic, walletNumber = 0, path } = params
  const validMnemonic = convertMnemonicToValid(mnemonic)
  const seed = bip39.mnemonicToSeedSync(validMnemonic)
  const hdwallet = hdkey.fromMasterSeed(seed)
  const wallet = hdwallet.derivePath((path) || `m/44'/60'/0'/0/${walletNumber}`).getWallet()
  const publicKey = wallet.getPublicKey()
  const privateKey = wallet.getPrivateKey()

  return {
    mnemonic: validMnemonic,
    address: `0x${wallet.getAddress().toString('hex')}`,
    publicKey: `0x${publicKey.toString('hex')}`,
    privateKey: `0x${privateKey.toString('hex')}`,
    wallet,
  }
}

module.exports = { getEthLikeWallet }