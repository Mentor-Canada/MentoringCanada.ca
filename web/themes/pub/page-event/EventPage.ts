import {Page} from "../common/Page.ts"
import PaymentFormView from "./PaymentFormView.ts";

export default class EventPage extends Page {

  private readonly el: HTMLElement

  constructor() {
    super()

    this.el = document.querySelector("body")

    const hasPaymentEl: HTMLInputElement = this.el.querySelector("#hasPayment")
    if(hasPaymentEl?.value == 1) {
      new PaymentFormView("#event-form")
    }
  }

}
