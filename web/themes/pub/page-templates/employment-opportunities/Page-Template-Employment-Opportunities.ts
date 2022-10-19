import {Page} from "../../common/Page.ts"
import {SplitText} from "../../common/SplitText.ts"
import {FocusManager} from "../../common/FocusManager.ts";
import {ClassWatcher} from "../../common/ClassWatcher.ts"

export default class PageTemplateEmploymentOpportunities extends Page {

  private readonly body: HTMLBodyElement

  constructor() {
    super()

    this.body = document.querySelector('body')
    const sections = document.querySelectorAll("[data-theme]")
    sections.forEach((el) => {
      new ClassWatcher(el, 'focused', this.onDarkThemeIn.bind(this, el), this.onDarkThemeOut.bind(this, el))
    })

    new FocusManager()

    let heroHeading = document.querySelectorAll('section#hero .title-line')

    heroHeading.forEach((el) => {
      new SplitText({
        el: el,
        delay: 0,
        delimiter: ''
      })
    })

    let statPercentages = document.querySelectorAll('.stat-percentage')
    statPercentages.forEach((el) => {
      new SplitText({
        el: el,
        delay: 100,
        delimiter: ''
      })
    })

    let statFractions = document.querySelectorAll('.stat-fraction')
    statFractions.forEach((el) => {
      new SplitText({
        el: el,
        delay: 100,
        delimiter: ' ',
        includeDelimiter: false
      })
    })

    let statWords = document.querySelectorAll('.stat-words')
    statWords.forEach((el) => {
      new SplitText({
        el: el,
        delay: 100,
        delimiter: ' '
      })
    })

    window.addEventListener('resize', () => {
      this.setAspectRatio()
    })
    this.setAspectRatio()

  }

  setAspectRatio() {
    const width = window.innerWidth
    const height = window.innerHeight
    const aspect = width / height
    this.body.style.setProperty('--vw-aspect-ratio', `${aspect}`);
  }

  setDarkThemeTransition(el) {
    const rect = el.getBoundingClientRect()
    let attr = "data-theme-transition-in"
    if (rect.top <= 0) attr = "data-theme-transition-out"
    const transition = el.getAttribute(attr)
    this.body.style.setProperty('--section-theme-transition', transition);
  }

  onDarkThemeIn(el) {
    this.setDarkThemeTransition(el)
    this.body.style.setProperty('--typography-body-color', '#fff');
    this.body.style.setProperty('--typography-heading-color', '#fff');
    this.body.style.setProperty('--section-theme-dark-opacity', '1');
  }

  onDarkThemeOut(el) {
    this.setDarkThemeTransition(el)
    this.body.style.setProperty('--typography-body-color', '');
    this.body.style.setProperty('--typography-heading-color', '');
    this.body.style.setProperty('--section-theme-dark-opacity', '');
  }

}
