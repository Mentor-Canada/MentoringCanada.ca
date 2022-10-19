import $ from "jquery";

export class Banner {

  private window: JQuery<HTMLElement>;
  private $el: JQuery<HTMLElement>;
  private stickyContainer: JQuery<HTMLElement>;
  private imageScrollEffects: JQuery<HTMLElement>;
  private contentScrollEffects: JQuery<HTMLElement>;

  private visibilityFlag: any;
  private visibilityEvent: any;
  private windowVisible: any;

  private rafId: any;
  private rafLastTimestamp: any;
  private rafCurrentTimestamp: any;
  private rafDelta: any;
  private rafNormalizedTime: any;

  private triggerImage: any;
  private imageOver: any;
  private targetPercentImage: any;
  private currentPercentImage: number = 0;

  private triggerContent: any;
  private contentOver: any;
  private targetPercentContent: any;
  private currentPercentContent: number = 0;

  private scrollTop: any;
  private windowHeight: any;


  constructor(el: any) {

    this.$el = $(el);
    if (!this.$el.length) return;
    if(this.$el.hasClass("page-banner-no-image")) return;

    this.window = $(window);

    this.stickyContainer = $("main");
    this.imageScrollEffects = this.$el.find(".page-banners-image-scroll-effects-container");
    this.contentScrollEffects = this.$el.find(".page-banners-content-scroll-effects-container");

    this.registerVisibility();
    this.visibility();
    this.resize();

    $(window).on("scroll", () => {
      this.scroll();
    });
    $(window).on("resize", () => {
      this.resize();
    });


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

  scroll() {
    this.scrollTop = this.window.scrollTop();
    this.handleTriggers();
  }

  resize() {

    this.windowHeight = this.window.height();
    this.scrollTop = this.window.scrollTop();

    let top = 0;
    const bannerHeight = this.$el.outerHeight();

    if (this.window.width() <= 700) {

      this.triggerImage = top;
      this.imageOver = Math.min(bannerHeight, this.windowHeight);

    } else {

      if (bannerHeight > this.windowHeight) {
        top = this.windowHeight - bannerHeight;
      }
      this.triggerImage = -top;
      this.imageOver = Math.min(bannerHeight, this.windowHeight);

    }
    this.$el.css("top", top);

    this.triggerContent = this.triggerImage;
    this.contentOver = this.imageOver / 2;

    this.scroll();

    this.rafLastTimestamp = performance.now();
    this.raf();

  }

  handleTriggers() {
    // Image
    if (this.scrollTop >= this.triggerImage && this.scrollTop <= this.triggerImage + this.imageOver) {
      this.targetPercentImage = (this.scrollTop - this.triggerImage) / this.imageOver;
    }
    else {
      if (this.scrollTop < this.triggerImage) {
        this.targetPercentImage = 0;
      } else {
        this.targetPercentImage = 1;
      }
    }

    // Content
    if (this.scrollTop >= this.triggerContent && this.scrollTop <= this.triggerContent + this.contentOver) {
      this.targetPercentContent = (this.scrollTop - this.triggerContent) / this.contentOver;
    }
    else {
      if (this.scrollTop < this.triggerContent) {
        this.targetPercentContent = 0;
      } else {
        this.targetPercentContent = 1;
      }
    }
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

    this.renderImageComponent();
    this.renderContentComponent();
  }

  renderImageComponent() {
    if (Math.abs(this.currentPercentImage - this.targetPercentImage) < 0.001) {
      if (this.targetPercentImage == 0 || this.targetPercentImage == 1) {
        this.currentPercentImage = Math.round(this.currentPercentImage);
      }
      else {
        return;
      }
    }

    let currentPercentImage = this.currentPercentImage;
    currentPercentImage += (this.targetPercentImage - currentPercentImage) / (15 * this.rafNormalizedTime);
    this.currentPercentImage = currentPercentImage;

    let scaleImage = this.remap(currentPercentImage, 0, 1, 1, (100 / 110));
    this.prefix(this.imageScrollEffects, "transform", `scale(${scaleImage})`);
  }

  renderContentComponent() {
    if (Math.abs(this.currentPercentContent - this.targetPercentContent) < 0.001) {
      if (this.targetPercentContent == 0 || this.targetPercentContent == 1) {
        this.currentPercentContent = Math.round(this.currentPercentContent);
      }
      else {
        return;
      }
    }

    let currentPercentContent = this.currentPercentContent;
    currentPercentContent += (this.targetPercentContent - currentPercentContent) / (15 * this.rafNormalizedTime);
    this.currentPercentContent = currentPercentContent;

    let scaleContent = this.remap(currentPercentContent, 0, 1, 1, (98 / 110))
    let fadeContent = this.remap(currentPercentContent, 0, 1, 1, 0);
    this.prefix(this.contentScrollEffects, "transform", `scale(${scaleContent})`);
    this.contentScrollEffects.css("opacity", fadeContent);
  }

  remap(v: number, aMin: number, aMax: number, bMin: number, bMax: number) {
    return ((v - aMin) / (aMax - aMin)) * (bMax - bMin) + bMin;
  }

  prefix(el: JQuery<HTMLElement>, prop: string, val: any) {
    el.css(`-webkit-${prop}`, val);
    el.css(`-moz-${prop}`, val);
    el.css(`-ms-${prop}`, val);
    el.css(`-o-${prop}`, val);
    el.css(prop, val);
  }


}
