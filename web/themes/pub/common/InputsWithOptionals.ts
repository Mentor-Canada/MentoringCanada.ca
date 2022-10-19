import $ from "jquery";
import {Datepicker} from "../common/Datepicker.ts";

export class InputsWithOptionals {

  private readonly el;
  private readonly supportedInputsSelector = "input[type='radio'], input[type='checkbox']";
  private readonly supportedSelectsSelector = "select";
  private lastNoShow = [] as string[];

  constructor(el: HTMLElement) {
    this.el = el;
    const inputs = this.el.querySelectorAll(this.supportedInputsSelector);
    const selects = this.el.querySelectorAll(this.supportedSelectsSelector);
    this.addEventListnersInputs(inputs);
    this.addEventListnersSelects(selects);
    setTimeout(() => {
      this.onEvent();
    }, 0);
  }

  addEventListnersInputs(inputs) {
    inputs.forEach((input: HTMLInputElement) => {
      input.addEventListener("click", () => {
        this.onEvent();
      });
    })
  }

  addEventListnersSelects(selects) {
    selects.forEach((select: HTMLSelectElement) => {
      select.addEventListener("change", () => {
        this.onEvent();
      });
    })
  }

  onEvent() {

    // get templates to show
    let show = [] as string[];
    const inputs = this.el.querySelectorAll("input:checked") as HTMLInputElement[];
    const selects = this.el.querySelectorAll("select") as HTMLSelectElement[];
    inputs.forEach((input: HTMLInputElement) => {
      const optionalsAttribute = input.getAttribute("data-show");
      if(optionalsAttribute) {
        const optionals = optionalsAttribute.split(",");
        optionals.forEach((optional) => {
          show.push(optional);
        });
      }
    });
    selects.forEach((select: HTMLSelectElement) => {
      let selectedOption = select.selectedOptions[0]
      const optionalsAttribute = selectedOption.getAttribute("data-show");
      if(optionalsAttribute) {
        const optionals = optionalsAttribute.split(",");
        optionals.forEach((optional) => {
          show.push(optional);
        });
      }
    });
    // filter out duplicates
    show = show.filter((v, i, a) => a.indexOf(v) === i);

    // keep those that are already there and should be shown, delete others
    const showing = this.el.querySelectorAll(".optional");
    let removedOptional = false;
    showing.forEach((el) => {

      const template = el.getAttribute("data-template");
      const i = show.indexOf(template);

      if(i != -1) {
        // keep element and remove from show list
        show.splice(i, 1)
      } else {
        // kill any optional datepickers
        el.querySelectorAll("input[type='datepicker']").forEach((dpel: HTMLInputElement) => {
          $(dpel).datepicker("destroy");
        });
        // remove from dom
        el.parentNode.removeChild(el);
        removedOptional = true;
      }

    });

    if(removedOptional) {
      // Maybe other optionals elsewhere need removing as
      // a consequence of these having been removed
      this.onEvent();
      return;
    }

    let noshow = [] as string[];
    show.forEach((optional: string) => {

      const selector = `template[data-template-name="${optional}"]`;
      const template = this.el.querySelector(selector);
      if(template) {
        const clone = template.cloneNode(true) as HTMLTemplateElement;
        const target = template.getAttribute("data-target");
        const targetEl = this.el.querySelector(`.${target}`)
        if(targetEl) {
          targetEl.appendChild(clone.content);

          // add events on any new inputs
          const inputs = targetEl.querySelectorAll(this.supportedInputsSelector);
          const selects = targetEl.querySelectorAll(this.supportedSelectsSelector);
          this.addEventListnersInputs(inputs);
          this.addEventListnersSelects(selects);

          targetEl.querySelectorAll("input[type='datepicker']").forEach((el: HTMLInputElement) => {
            new Datepicker(el);
          });

        } else {
          // can't show, we'll loop over it all again to see if
          // new optionals added may contain the optional
          noshow.push(optional);
        }

      }

    });

    if(this.arraysEqual(noshow, this.lastNoShow)) {
      // We checked the DOM again, and still
      // couldn't find the optional, so we abort
      this.lastNoShow = [];
      return;
    } else {
      this.lastNoShow = noshow;
      this.onEvent();
    }

  }


  arraysEqual(_arr1, _arr2) {
    if (!Array.isArray(_arr1) || ! Array.isArray(_arr2) || _arr1.length !== _arr2.length)
      return false;
    var arr1 = _arr1.concat().sort();
    var arr2 = _arr2.concat().sort();
    for (var i = 0; i < arr1.length; i++) {
      if (arr1[i] !== arr2[i])
        return false;
    }
    return true;
  }

}
