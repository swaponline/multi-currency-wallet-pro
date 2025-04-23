import * as bip32 from 'bip32'
import { hdkey } from 'ethereumjs-wallet'
import * as bip39 from 'bip39'

const getRandomMnemonicWords = () => {
  return bip39.generateMnemonic()
}

const validateMnemonicWords = (mnemonic) => {
  return bip39.validateMnemonic(convertMnemonicToValid(mnemonic))
}

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

const mnemonicIsValid = (mnemonic:string):boolean => bip39.validateMnemonic(convertMnemonicToValid(mnemonic))

export {
  getRandomMnemonicWords,
  validateMnemonicWords,
  mnemonicIsValid,
  convertMnemonicToValid,
  getEthLikeWallet,
}