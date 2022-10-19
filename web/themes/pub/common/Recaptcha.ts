let recaptchaUtils = {
  onRecaptchaSuccess: function() {
    document.querySelector("#sign-up-button")?.classList.remove('disabled')
  },
  onRecaptchaError: function() {
    document.querySelector("#sign-up-button")?.classList.add('disabled')
  }
}

export default recaptchaUtils
