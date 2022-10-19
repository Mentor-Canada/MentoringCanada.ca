import $ from "jquery";
import {Banner} from "./Banner.ts";
import {ParallaxElement} from "./ParallaxElement.ts";
import {InputsWithOptionals} from "./InputsWithOptionals.ts";
import {Carousel} from "./Carousel.ts";
import "slick-carousel";

export class Page {

  private animViews: any = [];
  private scrollTop: any;
  private windowHeight: any;

  constructor() {
    window.addEventListener("scroll", () => { this.scroll() });
    window.addEventListener("resize", () => { this.resize() });

    new Banner("#page-banner");

    let animViews = document.querySelectorAll(".anim-view");
    animViews.forEach((el: any) => {
      let view: any;
      let delay: number;
      let chainDelay: number;
      delay = el.getAttribute('data-anim-view-delay');
      if(delay) {
        delay = Number(delay);
      } else {
        delay = 0;
      }
      chainDelay = el.getAttribute('data-anim-view-chain-delay');
      if(chainDelay) {
        chainDelay = Number(chainDelay);
      } else {
        chainDelay = 0;
      }
      view = {
        el: $(el),
        animDidTrigger: false,
        trigger: null,
        delay: delay,
        chainDelay: chainDelay
      };
      this.animViews.push(view);
    });

    $(".section-link").on("click", (e) => {
      e.preventDefault();
      const target = $(e.target);
      let id = target.attr("data-section-id");
      const selector = `section[data-section-id="${id}"]`;
      const section = $(selector);
      $([document.documentElement, document.body]).animate({
        scrollTop: section.offset().top
      }, 600);
    });

    $(".parallax-element").each((k: any, el: any) => {
      new ParallaxElement({
        el: $(el),
        parallax: -0.2
      });
    });

    const el: HTMLElement = document.querySelector(".standard-form-style");
    if(el) {
      new InputsWithOptionals(el);
    }

    document.querySelectorAll(".carousel").forEach((el) => {
      new Carousel(el);
    });

    this.resize();

  }

  scroll() {
    this.scrollTop = $(window).scrollTop();
    this.scrollAnimInView();
  }

  resize() {
    this.windowHeight = $(window).height();
    this.calcAnimInView();
  }

  calcAnimInView() {
    var top: any;
    var offset: any;
    var trigger: any;
    offset = 100;
    for (var i = 0; i < this.animViews.length; i++) {
      var view: any;
      view = this.animViews[i];
      if (view.animDidTrigger) continue;
      top = view.el.offset().top + offset;
      trigger = top - this.windowHeight;
      view.trigger = trigger;
    }
    this.scroll();
  }

  scrollAnimInView() {
    let animQueuePos = [];
    for (let i = 0; i < this.animViews.length; i++) {
      let view: any;
      view = this.animViews[i];
      if (view.animDidTrigger) continue;
      if (this.scrollTop > view.trigger) {
        animQueuePos.push(i);
        view.animDidTrigger = true;
      }
    }
    let chainDelay: number = 0;
    for (let i = 0; i < animQueuePos.length; i++) {
      let pos = animQueuePos[i];
      let view = this.animViews[pos];

      if(i > 0) chainDelay += view.chainDelay;
      chainDelay += view.delay;

      setTimeout(() => {
        view.el.addClass("animation-triggered");
      }, chainDelay);
    }
  }


}
