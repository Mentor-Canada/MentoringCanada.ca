import $ from "jquery";

export class Carousel {

  private readonly el;
  private carouselObject;
  private carouselMode;

  constructor(el: HTMLInputElement) {
    this.el = $(el);

    const _carouselClickEase = "cubic-bezier(.5,0,0,1)";
    const _carouselClickSpeed = 700;
    const _carouselSwipeEase = "cubic-bezier(0.19, 1, 0.22, 1)";
    const _carouselSwipeSpeed = 600;

    let args = {
      slidesToShow: 1,
      useTransform: true,
      rows: 0,
      cssEase: _carouselClickEase,
      speed: _carouselClickSpeed,
    };
    if(this.el.attr("data-carousel-transition") == "fade") {
      args["fade"] = true;
    }
    if(this.el.attr("data-carousel-arrows") == "true") {
      args["arrows"] = true;
      args["prevArrow"] = this.el.find(".prev");
      args["nextArrow"] = this.el.find(".next");
    } else {
      args["arrows"] = false;
    }
    if(this.el.attr("data-carousel-dots") == "true") {
      args["dots"] = true;
    } else {
      args["dots"] = false;
    }
    if(this.el.attr("data-carousel-infinite") == "true") {
      args["infinite"] = true;
    } else {
      args["infinite"] = false;
    }
    if(this.el.attr("data-carousel-autoplay") == "true") {
      args["autoplay"] = true;
      args["autoplaySpeed"] = this.el.attr("data-carousel-autoplay-speed");
    } else {
      args["autoplay"] = false;
    }

    this.carouselObject = this.el.find(".carousel-track").slick(args);

    this.el.on("touchstart.slick mousedown.slick", (e) => {
      if( $(e.target).is(".slick-arrow") ||
          $(e.target).is(".slick-arrow span") ||
          $(e.target).is(".slick-dots li") ||
          $(e.target).is(".slick-dots li button")
      ) {
        this.carouselMode = "click";
      } else {
        this.carouselMode = "swipe";
      }
    });

    this.el.on("beforeChange", (e, carousel) => {
      if(this.carouselMode == "click") {
        carousel.slickSetOption("cssEase", _carouselClickEase, false);
        carousel.slickSetOption("speed", _carouselClickSpeed, false);
      }
    });

    this.el.on("afterChange", (e, carousel) => {
      carousel.slickSetOption("cssEase", _carouselSwipeEase, false);
      carousel.slickSetOption("speed", _carouselSwipeSpeed, false);
    });

  }

}
