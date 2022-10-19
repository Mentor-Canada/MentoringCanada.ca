import $ from "jquery";
import {StratPlan2022_2024Banner} from "./StratPlan2022_2024Banner.ts";
import {FocusManager} from "../common/FocusManager.ts";
import {ParallaxElement} from "../common/ParallaxElement.ts";

export class StratPlan2022_2024 {

  private animViews: any;
  private animInView: any;
  private scrollTop: any;
  private windowHeight: any;

  constructor() {
    window.addEventListener("scroll", () => { this.scroll() });
    window.addEventListener("resize", () => { this.resize() });

    new StratPlan2022_2024Banner("#page-banner");

    this.animInView = [];

    if(!this.animViews) {
      this.animViews = document.querySelectorAll(".anim-view");
    }
    this.animViews.forEach((el: any) => {
      var view: any;
      view = {
        el: $(el),
        animDidTrigger: false,
        trigger: null
      };
      this.animInView.push(view);
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

    new FocusManager()

    $("[data-parallax]").each((k: any, el: any) => {
      new ParallaxElement({
        el: $(el),
      });
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
    for (var i = 0; i < this.animInView.length; i++) {
      var view: any;
      view = this.animInView[i];
      if (view.animDidTrigger) continue;
      top = view.el.offset().top + offset;
      trigger = top - this.windowHeight;
      view.trigger = trigger;
    }
    this.scroll();
  }

  scrollAnimInView() {
    for (var i = 0; i < this.animInView.length; i++) {
      var view: any;
      view = this.animInView[i];
      if (view.animDidTrigger) continue;
      if (this.scrollTop > view.trigger) {
        view.el.addClass("animation-triggered");
        view.animDidTrigger = true;
      }
    }
  }


}
