import $ from "jquery";

export class Datepicker {

  private readonly el;

  constructor(el: HTMLInputElement) {
    this.el = $(el);

    this.el.on("beforeinput", (e) => {
      e.preventDefault();
      return false;
    });

    const hiddenInput = this.el.prev();
    let processedOptions = {
      onClose: this.onClose,
      afterShow: this.afterShow,
      altField: hiddenInput,
      altFormat: "yy-mm-dd"
    }
    let options = this.el.attr("data-options");
    options = options.split(",");
    options.forEach((option) => {
      const optionKeyValue = option.split(":");
      if(optionKeyValue.length == 2) {
        const key = optionKeyValue[0].trim();
        const value = optionKeyValue[1].trim();
        processedOptions[key] = value;
      }
    });

    this.el.datepicker(processedOptions);
    if(!this.el.datepicker("option", "changeMonth")) {
      this.el.datepicker("option", "monthNamesShort", this.el.datepicker("option", "monthNames"));
    }

    $(window).on("resize", () => {
      this.el.datepicker("hide");
    });

  }

  afterShow() {
    const lang = $("html").attr("lang") == "fr-CA" ? "fr" : "en";
    const obj = this;
    const dpDiv = obj.dpDiv;
    const selectedDateDiv = $("<div class='ui-datepicker-selected-date'></div>");
    const date = obj.input.datepicker("getDate");
    let displayDate = lang == "fr" ? "Sélectionnez une date" : "Select a date";
    if(date) {
      displayDate = lang == "fr" ? $.datepicker.formatDate('d MM, yy', date) : $.datepicker.formatDate('MM d, yy', date);
    }
    selectedDateDiv.text(displayDate);
    dpDiv.prepend(selectedDateDiv);


    setTimeout(() => {
      if(obj.input.offset().top <= dpDiv.offset().top) {
        dpDiv.removeClass("open-top");
        dpDiv.addClass("open-bottom");
      } else {
        dpDiv.removeClass("open-bottom");
        dpDiv.addClass("open-top");
      }
    }, 0)
  }

  onClose() {
    $(this).blur();
  }

}
