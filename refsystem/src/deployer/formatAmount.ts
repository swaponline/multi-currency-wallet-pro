const formatAmount: any = (amount: any, decimals: number) =>
  new window.BigNumber(amount).times(new window.BigNumber(10).pow(decimals)).toString(10)


export default formatAmount
