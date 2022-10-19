import PromoCodeParams from "./PromoCodeParams.ts";
import App from "../App.ts";

export default class PromoCodeView {

  public readonly el: HTMLElement
  public amount: string

  private readonly eventId
  public readonly inputEl: HTMLInputElement
  private readonly activePromoCodeEl: HTMLInputElement

  constructor(selector, eventId) {
    this.el = document.querySelector(selector)
    this.eventId = eventId
    this.inputEl = this.el.querySelector("#promo_code")
    this.activePromoCodeEl = this.el.querySelector("[name=active-promo-code]")

    this.el.querySelector("#promo-code-button")
      .addEventListener("click", () => this.applyButtonClicked())
  }

  async applyButtonClicked() {
    if(this.inputEl.value == "") {
      this.el.classList.remove('invalid')
      return
    }

    App.getInstance().showProcessing()

    let promoCodeParams = new PromoCodeParams()
    promoCodeParams.eventId = this.eventId
    promoCodeParams.promoCode = this.inputEl.value

    let response = await promoCodeParams.validate()
    if(response.data.error) {
      this.el.classList.add('invalid')
      this.activePromoCodeEl.value = null
      this.amount = null
    }
    else {
      this.el.classList.remove('invalid')
      this.amount = response.data.data.amount
      this.activePromoCodeEl.value = promoCodeParams.promoCode
    }

    this.el.dispatchEvent(new Event('promoUpdate'))

    App.getInstance().hideProcessing()
  }

}
