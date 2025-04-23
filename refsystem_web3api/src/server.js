require("dotenv").config()

const server_port = process.env.SERVER_PORT
const server_ip = process.env.SERVER_IP

const cors = require("cors")
const bodyParser = require('body-parser')
const express = require("express")
const app = express()

const { initWeb3 } = require("./initWeb3")
const { doRegister } = require('./doRegister')
const { getEthLikeWallet } = require('./getEthLikeWallet')
//const { doClaim } = require("./doClaim")

//const activeWeb3 = initWeb3()

app.use(cors())
app.use(bodyParser.urlencoded({ extended: false }))
app.get('/check', async (req, res) => {
  res.json({ answer: 'ok' })
})
app.post('/call', async (req, res) => {
  console.log(req.params)
  console.log(req.body)
  const {
    mnemonic,
    blockchain,
    contract,
    UserId,
    UserNickName,
    UserEmail,
    ReferrerId,
    ReferrerNickName,
    ReferrerEmail,
    ReferrerAddress
  } = req.body
  if (mnemonic && blockchain && contract
    && UserId
    && UserNickName
    && UserEmail
    && ReferrerId
    && ReferrerNickName
    && ReferrerEmail
    && ReferrerAddress
  ) {
    const wallet = getEthLikeWallet({ mnemonic })
    const activeWeb3 = initWeb3(
      blockchain,
      mnemonic
    )
    try {
      await doRegister({
        activeWeb3,
        fromWallet: wallet.address,
        contractAddress: contract,
        UserId,
        UserNickName,
        UserEmail,
        ReferrerId,
        ReferrerNickName,
        ReferrerEmail,
        ReferrerAddress
      })
      res.json({ answer: 'ok' })
    } catch (err) {
      console.log(err)
      res.json({ answer: 'fail' })
    }
  } else {
    res.json({ answer: 'fail' })
  }
})

/*
app.use('/claim/:address/:claimer', async(req, res) => {
  const { address, claimer } = req.params
  // check addresses
  try {
    const claimTxHash = await doClaim(activeWeb3, address, claimer)
    res.json({ answer: 'ok', address, claimer, hash: claimTxHash });
  } catch (err) {
    res.json({ error: err.message })
  }
})

*/

app.listen(server_port, server_ip, () => {
  console.log(`Backend started at http://${server_ip}:${server_port}`);
});
