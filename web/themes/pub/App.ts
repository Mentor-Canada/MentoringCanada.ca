export default class App {

  private static instance: App

  private readonly bodyEl: HTMLBodyElement

  private constructor() {
    this.bodyEl = document.querySelector("body")
  }

  public static getInstance() {
    if(!this.instance) {
      this.instance = new App
    }
    return this.instance
  }

  public showProcessing() {
    this.bodyEl.classList.add("processing")
  }

  public hideProcessing() {
    this.bodyEl.classList.remove("processing")
  }

}
