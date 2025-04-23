declare class Web3 {
  constructor(givenProvider: string)
  givenProvider: string
}

interface Window {
  Web3: any
  Big: any
  BigNumber: any
  ethereum: any
  networkId: any
  farmAddress: any
  rewardsAddress: any
  stakingAddress: any
}
