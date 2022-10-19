export class SplitText {

  private readonly el
  private string: string
  private splits = []

  public initialDelay = 0
  public delay = 100
  public direction = 'rtl'
  public delimiter = ''
  public includeDelimiter: boolean = true

  constructor(args) {
    this.el = args.el as HTMLElement
    if(args.hasOwnProperty('initialDelay')) {
      this.initialDelay = args.initialDelay
    }
    if(args.hasOwnProperty('delay')) {
      this.delay = args.delay
    }
    if(args.hasOwnProperty('direction')) {
      if(args.direction === 'rtl' || args.direction === 'ltr') {
        this.direction = args.direction
      }
    }
    if(args.hasOwnProperty('delimiter')) {
      this.delimiter = args.delimiter
    }
    if(args.hasOwnProperty('includeDelimiter')) {
      this.includeDelimiter = args.includeDelimiter
    }

    this.string = this.el.innerText
    this.splits = this.string.split(this.delimiter)


    this.el.innerHTML = ''
    for(let i = 0; i < this.splits.length; i++) {
      let splitEl = document.createElement('span')
      splitEl.setAttribute('class', 'split-text-el')
      splitEl.style.display = 'inline-block'
      let delimiter = ''
      if(this.includeDelimiter) {
        delimiter = (i !== this.splits.length - 1) ? this.delimiter : ''
      }
      splitEl.textContent = `${this.splits[i]}${delimiter}`

      let delay = i * this.delay
      if(this.direction === 'ltr') {
        delay = (this.splits.length - i - 1) * this.delay
      }
      delay += this.initialDelay
      splitEl.style.transitionDelay = `${delay}ms`
      this.el.appendChild(splitEl)
    }

  }



}
