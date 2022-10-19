$ = require "jquery"

class Carousel

  constructor: (args) ->

    _carouselClickEase = "cubic-bezier(.5,0,0,1)"
    _carouselClickSpeed = 700
    _carouselSwipeEase = "cubic-bezier(0.19, 1, 0.22, 1)"
    _carouselSwipeSpeed = 600

    @$el = args.el
    slidesToShow = args.slidesToShow
    responsive = args.responsive

    @carouselObject = @$el.find(".carousel-track").slick
      slidesToShow: slidesToShow
      useTransform: true
      centerMode: true
      dots: true
      cssEase: _carouselClickEase
      speed: _carouselClickSpeed
      prevArrow: @$el.find ".prev"
      nextArrow: @$el.find ".next"
      responsive: responsive

    @$el.on "touchstart.slick mousedown.slick", (e) =>
      if $(e.target).is(".slick-arrow") or
        $(e.target).is(".slick-arrow span") or
        $(e.target).is(".slick-dots li") or
        $(e.target).is(".slick-dots li button")
          @carouselMode = "click"
      else
        @carouselMode = "swipe"

    @$el.on "beforeChange", (event, carousel) =>
      if @carouselMode is "click"
        carousel.slickSetOption "cssEase", _carouselClickEase, false
        carousel.slickSetOption "speed", _carouselClickSpeed, false

    @$el.on "afterChange", (event, carousel) =>
      carousel.slickSetOption "cssEase", _carouselSwipeEase, false
      carousel.slickSetOption "speed", _carouselSwipeSpeed, false

module.exports = Carousel