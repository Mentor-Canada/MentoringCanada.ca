export class Counter {

  private readonly el
  private from: string
  private to: string

  private characterArray = []

  private rotations
  private fillMode = null

  private readonly allowedFillModes = ['PROGRESSIVE', 'PERSISTENT']

  constructor(args) {
    this.el = args.el as HTMLElement

    if(args.hasOwnProperty('from')) {
      this.from = args.from
    } else {
      this.from = '0'
    }

    if(args.hasOwnProperty('to')) {
      this.to = args.to
    } else {
      this.to = this.el.textContent
    }

    if(args.hasOwnProperty('fillMode')) {
      if(this.allowedFillModes.indexOf(args.fillMode) === -1) {
        this.fillMode = this.allowedFillModes[0]
      } else {
        this.fillMode = args.fillMode
      }
    } else {
      this.fillMode = this.allowedFillModes[0]
    }

    for(let i = 0; i < 10; i++) {
      this.characterArray.push(`${i}`)
    }

    let maxLength = Math.max(this.from.length, this.to.length)
    if(this.fillMode == 'PROGRESSIVE') {
      this.from = this.from.padStart(maxLength, ' ')
      this.to = this.to.padStart(maxLength, ' ')
    }
    if(this.fillMode == 'PERSISTENT') {
      this.from = this.from.padStart(maxLength, '0')
      this.to = this.to.padStart(maxLength, '0')
    }

    this.el.innerHTML = ''
    for(let i = 0; i < maxLength; i++) {
      let loop = this.getLoopAtPosition(i)
      let loopEl = document.createElement('div')
      loopEl.setAttribute('class', 'counter-loop-el')
      for(let j = 0; j < loop.length; j++) {
        let charEl = document.createElement('div')
        charEl.setAttribute('class', 'counter-char-el')
        charEl.style.transform = `translateY(${(loop.length - j - 1) * 100}%)`
        charEl.textContent = loop[j]
        loopEl.appendChild(charEl)
      }
    this.el.appendChild(loopEl)
      // console.log(loopEl)
      // console.log(loop)
    }



    // console.log(this)


  }

  getLoopAtPosition(pos) {
    let from = this.from[pos]
    let to = this.to[pos]

    let indexFrom = this.characterArray.indexOf(from)
    let indexTo = this.characterArray.indexOf(to)

    let adjustedIndexFrom = indexFrom
    let adjustedIndexTo = indexTo

    if(indexFrom === -1) adjustedIndexFrom = 0
    if(indexTo === -1) adjustedIndexTo = this.characterArray.length - 1

    // console.log(adjustedIndexFrom, adjustedIndexTo)

    let loop = []
    if(adjustedIndexTo >= adjustedIndexFrom) {
      loop = this.characterArray.slice(adjustedIndexFrom, adjustedIndexTo + 1)
    } else {
      loop = this.characterArray.slice(adjustedIndexFrom, this.characterArray.length)
      loop = loop.concat(this.characterArray.slice(0, adjustedIndexTo + 1))
    }


    // console.log(loop)

    return loop
  }

}
