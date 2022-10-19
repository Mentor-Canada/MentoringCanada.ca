// missing forEach on NodeList for IE11
if (window.NodeList && !NodeList.prototype.forEach) {
  NodeList.prototype.forEach = Array.prototype.forEach;
}

import recaptchaUtils from "./common/Recaptcha.ts"

window.onloadCallback = function() {
  let googleRecaptchaEl = document.querySelector("#google-recaptcha")
  if(googleRecaptchaEl) {
    window.grecaptcha.render(googleRecaptchaEl, {
      'sitekey' : '6Lfm1tEZAAAAAEmB77JGVs7Oi_X1zqBUT0L2Zp3N',
      'callback': recaptchaUtils.onRecaptchaSuccess,
      'expired-callback': recaptchaUtils.onRecaptchaError,
      'error-callback': recaptchaUtils.onRecaptchaError
    });
  }
};

import "./style.scss"
import $ from "jquery";
import datepickerFactory from 'jquery-datepicker';
import {Datepicker} from "./common/Datepicker.ts";

import {Page} from "./common/Page.ts"
import {Submitted} from './submitted/Submitted.ts'
import {DataDashboard} from './page-data-dashboard/DataDashboard.ts'
import {Barriers} from './page-data-dashboard/Barriers.ts'
import {OnlineOrientationForMentors} from './page-online-orientation-for-mentors/OnlineOrientationForMentors.ts'
import {StratPlan2022_2024} from './page-strat-plan-2022-2024/StratPlan2022_2024Page.ts'
import {YearEnd2021, YearEnd2021Page} from './page-year-end-2021/YearEnd2021Page.ts'
import EventPage from './page-event/EventPage.ts'
import EmploymentOpportunities from './page-templates/employment-opportunities/Page-Template-Employment-Opportunities.ts'


declare var window: any;

class Index {

  private readonly types: {[key: string]: any};

  private $html: HTMLElement;
  private globalNav: HTMLElement;
  private hasGlobalNav: Boolean;

  private windowWidth: any;
  private windowHeight: any;
  private globalNavMenuWidth: any = 0;

  constructor() {
    this.types = {
      submitted: Submitted,
      onlineOrientationForMentors: OnlineOrientationForMentors,
      dataDashboard: DataDashboard,
      barriers: Barriers,
      stratPlan2022_2024: StratPlan2022_2024,
      yearEnd2021: YearEnd2021Page,
      event: EventPage,
      "employment-opportunities": EmploymentOpportunities
    };

    if(window.navigator.userAgent.indexOf("Edge") > -1) {
      $("html").addClass("edge");
    }

    document.addEventListener("DOMContentLoaded", () => {
      if(document.fonts) {
        document.fonts.ready.then((a) => {
          this.go()
        });
      } else {
        this.go()
      }
    });
  }

  private resize() {
    this.windowWidth = window.innerWidth;
    this.windowHeight = window.innerHeight;
    if(this.hasGlobalNav) {
      this.resizeGlobalNav()
    }
  }

  private initGlobalNav() {
    this.globalNavMenuWidth = this.globalNav.offsetWidth;
  }

  private resizeGlobalNav() {
    this.$html.classList.remove("compact-menu-open");
    $("html").removeClass("compact-menu-open");
    const logoWidth = 193
    const logoPadding = 50
    if(this.windowWidth * 0.9 < this.globalNavMenuWidth + logoWidth + logoPadding) {
      this.$html.classList.add("compact-menu");
    } else {
      this.$html.classList.remove("compact-menu");
    }
  }

  private initDatepickers() {
    datepickerFactory($);
    const lang = $("html").attr("lang") == "fr-CA" ? "fr" : "en";
    if(lang == "fr") {
      $.datepicker.setDefaults({
        closeText: "Fermer",
        currentText: "Aujourd'hui",
        dateFormat: "d MM, yy",
        dayNames: ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
        dayNamesMin: ["D", "L", "M", "M", "J", "V", "S"],
        dayNamesShort: ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
        firstDay: 0,
        isRTL: false,
        monthNames: ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"],
        monthNamesShort: ["jan", "fév", "mar", "avr", "mai", "jun", "jul", "aoû", "sep", "oct", "nov", "déc"],
        nextText: "Suivant",
        prevText: "Précédent",
        showMonthAfterYear: false,
        weekHeader: "Sem.",
        yearSuffix: ""
      });
    } else {
      $.datepicker.setDefaults({
        dateFormat: 'MM d, yy',
        dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
      });
    }
    $.datepicker.setDefaults({
      changeYear: true,
      showButtonPanel: true,
      yearRange: "-100:+100",
      minDate: "-10y",
      maxDate: "+10y"
    });

    // Keep datepicker open after selecting a date
    $.datepicker._selectDateOverload = $.datepicker._selectDate;
    $.datepicker._selectDate = function(id, dateStr) {
      var target = $(id);
      var inst = this._getInst(target[0]);
      inst.inline = true;
      $.datepicker._selectDateOverload(id, dateStr);
      inst.inline = false;
      this._updateDatepicker(inst);
    }

    // Add aftershow event since existing beforeshow triggers before markup is rendered
    $.datepicker._updateDatepicker_original = $.datepicker._updateDatepicker;
    $.datepicker._updateDatepicker = function(inst) {
      $.datepicker._updateDatepicker_original(inst);
      var afterShow = this._get(inst, 'afterShow');
      if (afterShow)
        afterShow.apply(inst);  // trigger custom callback
    }

    document.querySelectorAll("input[type='datepicker']").forEach((el: HTMLInputElement) => {
      new Datepicker(el);
    });
  }

  private go() {

    if(window.pageData) {
      let Controller = this.types[window.pageData.type];
      if(Controller) {
        new Controller;
      } else {
        new Page;
      }
    }

    this.$html = document.getElementsByTagName("html")[0];
    this.globalNav = document.getElementById("global-nav");
    this.hasGlobalNav = !!this.globalNav
    if(this.hasGlobalNav) {
      this.initGlobalNav()
    }
    window.addEventListener("resize", () => this.resize());

    document.querySelectorAll(".compact-menu-toggle").forEach((el) => {
      el.addEventListener("click", () => {
        this.$html.classList.toggle("compact-menu-open");
      })
    });

    document.getElementById("compact-menu").addEventListener("click", (e: Event) => {
      const el = e.target as HTMLElement;
      if(el.matches("#compact-menu")) {
        this.$html.classList.remove("compact-menu-open");
      }
    });

    this.initDatepickers();

    this.resize();

    $("html").addClass("loaded");

  }

}

new Index;
