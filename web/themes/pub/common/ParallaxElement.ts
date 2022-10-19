import $ from "jquery";

export class ParallaxElement {

  private window: JQuery<HTMLElement>;
  private el: JQuery<HTMLElement>;

  private easing: boolean = false;
  private parallax: number = 0;
  private parallaxPx: number = 0;

  private lastPosY: number = 0;

  private visibilityFlag: any;
  private visibilityEvent: any;
  private windowVisible: any;

  private rafId: any;
  private rafLastTimestamp: any;
  private rafCurrentTimestamp: any;
  private rafDelta: any;
  private rafNormalizedTime: any;

  private top: any;
  private height: any;
  private inViewRelativeScrollTop: any;
  private outViewRelativeScrollTop: any;
  private inViewRange: any;

  private scrollTop: any;
  private windowHeight: any;

  constructor(args) {
    this.window = $(window);
    this.el = args.el;
    if(args.parallax) {
      this.parallax = args.parallax * -1;
    } else {
      this.parallax = this.el.attr('data-parallax')
    }
    if(args.easing) {
       this.easing = args.easing;
    }

    this.registerVisibility();
    this.window.on(this.visibilityEvent, () => { this.visibility() });
    this.window.on("resize", () => { this.resize() });
    this.window.on("scroll", () => { this.scroll() });

    this.resize();
    this.visibility();
    this.raf();

  }

  registerVisibility() {
    if (typeof document.hidden !== "undefined") {
      this.visibilityFlag = "hidden";
      this.visibilityEvent = "visibilitychange";
    }
    else if (typeof document.msHidden !== "undefined") {
      this.visibilityFlag = "msHidden";
      this.visibilityEvent = "msvisibilitychange";
    }
    else if (typeof document.webkitHidden !== "undefined") {
      this.visibilityFlag = "webkitHidden";
      this.visibilityEvent = "webkitvisibilitychange";
    }
    $(document).on(this.visibilityEvent, () => {
      this.visibility();
    });
  }

  visibility() {
    this.windowVisible = !document[this.visibilityFlag];
    if (this.windowVisible) {
      this.rafLastTimestamp = performance.now();
    }
  }

  resize() {
    this.windowHeight = this.window.height();
    this.scrollTop = this.window.scrollTop();
    this.parallaxPx = this.parallax * this.windowHeight;

    this.top = this.el.offset().top + this.parallaxPx;
    this.height = this.el.outerHeight() - (2 * this.parallaxPx);
    this.inViewRelativeScrollTop = this.top - this.windowHeight;
    this.outViewRelativeScrollTop = this.inViewRelativeScrollTop + this.height + this.windowHeight;
    this.inViewRange = this.outViewRelativeScrollTop - this.inViewRelativeScrollTop;
  }

  scroll() {
    this.scrollTop = this.window.scrollTop();
  }

  raf() {
    this.rafId = window.requestAnimationFrame(this.raf.bind(this));
    if (!this.windowVisible) return;
    this.rafCurrentTimestamp = performance.now();
    this.rafDelta = this.rafCurrentTimestamp - this.rafLastTimestamp;
    if (this.rafDelta == 0) return;
    this.rafLastTimestamp = this.rafCurrentTimestamp;
    const targetFrameRate = 1000 / 60;
    this.rafNormalizedTime = targetFrameRate / this.rafDelta;

    const percentInView = (this.scrollTop - this.inViewRelativeScrollTop) / this.inViewRange;
    if(percentInView < 0 || percentInView > 1) return;

    const remap = (percentInView * 2) - 1;
    const posY = remap * this.parallaxPx;
    let targetY = this.lastPosY;
    targetY += (posY - targetY) / (15 * this.rafNormalizedTime);
    this.lastPosY = targetY;

    if(!this.easing) {
      targetY = posY;
    }

    this.prefix(this.el, "transform", `translateY(${targetY}px) translateZ(0)`);

  }

  prefix(el: JQuery<HTMLElement>, prop: string, val: any) {
    el.css(`-webkit-${prop}`, val);
    el.css(`-moz-${prop}`, val);
    el.css(`-ms-${prop}`, val);
    el.css(`-o-${prop}`, val);
    el.css(prop, val);
  }


}
