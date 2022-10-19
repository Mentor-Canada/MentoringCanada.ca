export class YearEnd2021Page {

  private cards: NodeListOf<HTMLElement>
  private frontFaces: NodeListOf<HTMLElement>
  private closeButtons: NodeListOf<HTMLElement>
  private cardsOverlay: HTMLElement
  private activeCard: HTMLElement|null = null
  private windowHeight: number
  private animatedCardKeys: any = []

  constructor() {

    this.cardsOverlay = document.querySelector(".cards-overlay")
    this.cards = document.querySelectorAll(".card")
    this.frontFaces = document.querySelectorAll(".card .card-front")
    this.closeButtons = document.querySelectorAll(".card .card-back button")
    this.frontFaces.forEach((el) => {
      el.addEventListener("click", (ev) => this.onFrontFaceClick(ev))
    })
    this.closeButtons.forEach((el) => {
      el.addEventListener("click", (ev) => this.onCloseClick(ev))
    })
    this.cardsOverlay.addEventListener("click", (ev) => this.onCloseClick(ev))

    this.resize()
    window.addEventListener('scroll', () => this.scroll())
    window.addEventListener('resize', () => this.resize())

  }

  onFrontFaceClick(ev) {
    let card = ev.currentTarget.closest('.card')
    this.activeCard = card
    this.cards.forEach((el) => {
      el.classList.remove('active')
    })
    card.querySelector(".card-content-scroll-wrapper").scrollTop = 0
    card.classList.add('active')
    this.cardsOverlay.classList.add('active')
  }

  onCloseClick(ev) {
    this.activeCard = null
    this.cards.forEach((el) => {
      el.classList.remove('active')
    })
    this.cardsOverlay.classList.remove('active')
  }

  scroll() {
    let rect: DOMRect|undefined
    this.cards.forEach((el, key) => {
      if(this.animatedCardKeys.includes(key)) return
      rect = el.getBoundingClientRect()
      if(rect.top < this.windowHeight * 0.9) {
        this.animatedCardKeys.push(key)
        el.classList.add('animate')
      }
    })
    if(!this.activeCard) return
    rect = this.activeCard.getBoundingClientRect()
    if(rect.bottom < 0 || rect.top > this.windowHeight) {
      this.onCloseClick(null)
    }
  }

  resize() {
    this.windowHeight = window.innerHeight
    this.scroll()
  }

}
