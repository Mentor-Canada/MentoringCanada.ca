import $ from "jquery";

export class Submitted {

  private countdown: number = 5;
  private countdownStart: number;
  private countdownTimer: number | null = null;

  private rafId: any;
  private progressBar: JQuery<HTMLElement>;
  private progressTimer: JQuery<HTMLElement>;

  private redirect: string;

  constructor() {
    this.redirect = window.pageData.redirect;
    this.countdownStart = performance.now();
    this.progressBar = $("#countdown-progress-bar");
    this.progressTimer = $("#countdown-timer-seconds");
    if(this.progressBar.length) {
      this.raf();
    }
  }

  raf() {
    this.rafId = window.requestAnimationFrame(this.raf.bind(this));
    const rafCurrentTimestamp = performance.now();
    const elapsed = rafCurrentTimestamp - this.countdownStart;

    const percent = elapsed / (this.countdown * 1000);
    this.prefix(this.progressBar, "transform", `scaleX(${percent})`);

    let timer = Math.ceil(this.countdown - (elapsed / 1000));
    if(timer != this.countdownTimer) {
      if(timer <= 0) timer = 0;
      this.countdownTimer = timer;
      const dislpayTimer = this.countdownTimer + "";
      if(dislpayTimer == "1") {
        this.progressTimer.addClass("second");
      } else {
        this.progressTimer.removeClass("second");
      }
      this.progressTimer.text(dislpayTimer);
    }

    if(elapsed > this.countdown * 1000) {
      window.cancelAnimationFrame(this.rafId);
      window.location.href = this.redirect;
    }
  }

  prefix(el: JQuery<HTMLElement>, prop: string, val: any) {
    el.css(`-webkit-${prop}`, val);
    el.css(`-moz-${prop}`, val);
    el.css(`-ms-${prop}`, val);
    el.css(`-o-${prop}`, val);
    el.css(prop, val);
  }

}
