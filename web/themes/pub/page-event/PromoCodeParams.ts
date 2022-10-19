import axios from "axios";

export default class PromoCodeParams {

  eventId
  promoCode

  validate() {
    return axios({
      method: 'POST',
      url: '/a/payment/promo',
      data: this
    })
  }

}
