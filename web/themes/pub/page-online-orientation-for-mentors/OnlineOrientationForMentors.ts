import $ from "jquery";
import {Page} from "../common/Page.ts"

export class OnlineOrientationForMentors extends Page {

  constructor() {
    super()
    const form = document.querySelector("form#oofm") as HTMLFormElement
    const select = document.querySelector(".button-style-select-wrapper select") as HTMLSelectElement
    const selectOptions = select.options as HTMLOptionsCollection
    const button = document.querySelector("button.submit") as HTMLButtonElement

    const moodleURL = "https://moodle.mentoringcanada.ca/login/signup.php?"
    const moodleLang = $("html").attr("lang") == "fr-CA" ? "fr_ca" : "en";

    select.addEventListener("change", () => {
      if(selectOptions.selectedIndex !== 0) {
        button.classList.add("show")
      } else {
        button.classList.remove("show")
      }
    })
    button.addEventListener("click", (e) => {
      if(form.checkValidity()) {
        e.preventDefault()
        const option = selectOptions[selectOptions.selectedIndex] as HTMLOptionElement
        const value = option.value
        const url = `${moodleURL}province=${value}&lang=${moodleLang}`
        window.location.href = url
      }
    })
  }

}
