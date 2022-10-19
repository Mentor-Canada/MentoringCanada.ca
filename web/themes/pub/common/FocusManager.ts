import {FocusElement} from "./FocusElement.ts";

export class FocusManager {

  private $root: HTMLElement
  private $els: NodeListOf<HTMLElement>
  private focusElements: FocusElement[] = []

  constructor(el?: HTMLElement) {
    if(el) {
      this.$root = el
    } else {
      this.$root = document.body
    }
    this.$els = this.$root.querySelectorAll('[data-focus-start]')
    this.$els.forEach((el) => {
      this.focusElements.push(new FocusElement(el))
    })

    requestAnimationFrame(this.update.bind(this))

    window.addEventListener('scroll', () => {
      this.scroll()
    })
    window.addEventListener('resize', () => {
      this.resize()
    })
  }

  scroll() {
    this.focusElements.forEach((focusElement) => {
      focusElement.checkState()
    })
  }

  resize() {
    this.focusElements.forEach((focusElement) => {
      focusElement.onResize()
    })
  }

  update() {
    requestAnimationFrame(this.update.bind(this))
    this.focusElements.forEach((focusElement) => {
      if(focusElement.needsVarsUpdate) {
        focusElement.$el.style.setProperty('--focus-percent', `${focusElement.percentInRange}`)
        focusElement.$el.style.setProperty('--focus-percent-excluding-height', `${focusElement.percentInRangeExcludingHeight}`)
        focusElement.needsVarsUpdate = false
      }
      if(focusElement.needsFocusUpdate) {
        if(focusElement.focused) {
          focusElement.$el.classList.add('focused')
        } else {
          focusElement.$el.classList.remove('focused')
        }
        focusElement.needsFocusUpdate = false
      }
    })
  }



}
