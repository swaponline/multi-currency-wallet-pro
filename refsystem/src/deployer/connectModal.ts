import { Modal } from './modals'
import { accountUnlockedStorageKey } from './constants'
import { getState } from './state'
import setupWeb3 from './setupWeb3'


const depositModal = new Modal({
  title: 'Connect',
  content: `
    <strong>Metamask</strong>
    <div class="ff-modal-buttons">
      <button class="ff-button" type="button">Connect</button>
    </div>
  `,
  onOpen() {
    const submitButton = this.elems.root.querySelector('.ff-button')

    submitButton.addEventListener('click', async () => {
      localStorage.setItem(accountUnlockedStorageKey, 'true')

      await window.ethereum.enable()
      await setupWeb3()

      const { opts } = getState()

      if (typeof opts.onFinishLoading === 'function') {
        opts.onFinishLoading()
      }

      this.close()
    })
  },
})


export default depositModal
