const id = 'ff-modals-root'

const createHtml = ({ title = '', content = '' }) => `
  <div class="ff-overlay">
    <div class="ff-modal">
      <div class="ff-modal-headline">
        <div class="ff-modal-title">${title}</div>
        <button class="ff-modal-close" type="button" aria-label="Close the dialog">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path fill="currentColor" d="M18.3 5.70997C17.91 5.31997 17.28 5.31997 16.89 5.70997L12 10.59L7.10997 5.69997C6.71997 5.30997 6.08997 5.30997 5.69997 5.69997C5.30997 6.08997 5.30997 6.71997 5.69997 7.10997L10.59 12L5.69997 16.89C5.30997 17.28 5.30997 17.91 5.69997 18.3C6.08997 18.69 6.71997 18.69 7.10997 18.3L12 13.41L16.89 18.3C17.28 18.69 17.91 18.69 18.3 18.3C18.69 17.91 18.69 17.28 18.3 16.89L13.41 12L18.3 7.10997C18.68 6.72997 18.68 6.08997 18.3 5.70997Z"></path>
          </svg>
        </button>
      </div>
      <div class="ff-modal-content">
        ${content}
      </div>
    </div>
  </div>
`

type Elements = {
  root: HTMLDivElement
  overlay: HTMLDivElement
  modal: HTMLDivElement
  closeButton: HTMLButtonElement
  title: HTMLButtonElement
  content: HTMLButtonElement
}

const queryElements = (root): Elements => ({
  root,
  overlay: root.querySelector('.ff-overlay'),
  modal: root.querySelector('.ff-modal'),
  closeButton: root.querySelector('.ff-modal-close'),
  title: root.querySelector('.ff-modal-title'),
  content: root.querySelector('.ff-modal-content'),
})

type Opts = {
  title?: string
  content?: string
  onOpen(...args: any[]): void
}

type OpenProps = Record<string, any> & {
  title?: string
}

export class Modal {

  opts: Opts
  elems: Elements

  constructor(opts: Opts) {
    this.opts = opts
  }

  open = (props: OpenProps = {}) => {
    const html = createHtml({
      title: this.opts.title,
      content: this.opts.content,
    })

    const root = document.getElementById(id) as HTMLDivElement

    root.innerHTML = html

    this.elems = queryElements(root)

    if (props?.title) {
      this.elems.title.innerText = props.title
    }

    this.elems.overlay.addEventListener('click', this.close)

    this.elems.modal.addEventListener('click', (event) => {
      event.stopPropagation()
    })

    this.elems.closeButton.addEventListener('click', this.close)

    this.opts.onOpen.bind(this)(props)
  }

  close = () => {
    this.elems.root.innerHTML = ''
  }
}

export const injectModalsRoot = () => {
  const modalsRoot = document.createElement('div')

  modalsRoot.setAttribute('id', id)

  document.body.appendChild(modalsRoot)
}
