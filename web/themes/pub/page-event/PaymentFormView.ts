import PromoCodeView from "./PromoCodeView.ts";
import {Spinner} from "spin.js";
import App from "../App.ts";

export default class PaymentFormView {

  private el: HTMLFormElement

  private readonly promoCodeView: PromoCodeView

  private readonly totalEl
  private readonly totalWithoutPromo
  private readonly chequeCheckbox: HTMLInputElement
  private charge

  constructor(selector) {
    this.el = document.querySelector(selector)
    this.totalEl = this.el.querySelector(".payment-total")
    this.totalWithoutPromo = (this.el.querySelector("#totalWithoutPromo") as HTMLInputElement).value
    this.charge = this.totalWithoutPromo

    let eventId = (document.querySelector("[name=field_submission_event_id]") as HTMLInputElement)
      .value

    this.promoCodeView = new PromoCodeView(".promo-code", eventId)
    this.promoCodeView.el.addEventListener('promoUpdate', () => {
      const value = this.promoCodeView.amount || this.totalWithoutPromo
      this.totalEl.innerText = value
      this.charge = value
    })

    const wrapper: HTMLElement = this.el.closest(".form-section")

    this.chequeCheckbox = this.el.querySelector("[name=cheque]")
    this.chequeCheckbox.addEventListener('click', () => {
      if(this.chequeCheckbox.checked) {
        this.el.classList.add('pay-by-cheque')
      }
      else {
        this.el.classList.remove('pay-by-cheque')
      }
    })

    this.el.addEventListener("submit", (e) => {
      if(!this.el.classList.contains("show-payment")) {
        this.el.classList.add("show-payment")
        this.chequeCheckbox.checked = false
        this.promoCodeView.inputEl.value = ""
        document.querySelector("body").classList.remove("standard-form-submitted")
        wrapper.scrollIntoView()
        e.preventDefault()
      }
    })

    const spin: HTMLElement = document.querySelector(".spin");
    new Spinner({color:'#f5b049'}).spin(spin);

    this.renderPayPalButton()
  }

  renderPayPalButton() {
    if(!window.paypal) {
      return
    }

    const that: PaymentFormView = this

    paypal.Buttons({
      style: {
        shape: 'pill',
      },
      createOrder: function(data, actions) {
        that.onPayPalSelected();
        const units = [
          {
            amount: {
              value: that.charge
            },
            description: "Purchase Description"
          }
        ];
        return actions.order.create({
          purchase_units: units,
          application_context: {
            shipping_preference: 'NO_SHIPPING'
          }
        });
      },
      onApprove: function(data, actions) {
        App.getInstance().showProcessing()
        return actions.order.capture().then(function(details) {
          if(details.status == "COMPLETED") {
            const orderIdInput: HTMLInputElement = document.querySelector("[name=order_id]");
            orderIdInput.value = details.id;
            that.el.submit()
          }
        });
      },
      onCancel: function(data, actions) {
        that.onPayPalCancelled();
      }
    }).render('#paypal-button');
  }

  public onPayPalSelected() {
    this.el.classList.add('pay-by-paypal')
  }

  public onPayPalCancelled() {
    this.el.classList.remove('pay-by-paypal')
  }

}
