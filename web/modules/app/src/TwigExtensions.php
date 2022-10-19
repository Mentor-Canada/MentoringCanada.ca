<?php

namespace Drupal\app;

use Drupal\app\Utils\ImageUtils;
use Drupal\app\Views\BannerView;

use Drupal\app\Views\UI_InputFieldView;
use Drupal\app\Views\UI_TextareaFieldView;
use Drupal\app\Views\UI_SelectFieldView;
use Drupal\app\Views\UI_DatepickerFieldView;
use Drupal\app\Views\UI_RadioFieldView;
use Drupal\app\Views\UI_CheckboxFieldView;
use Drupal\app\Views\UI_CheckboxesFieldView;
use Drupal\app\Views\UI_EventOptionsView;
use Drupal\app\Views\UI_EventOptionsSelectView;

class TwigExtensions extends \Twig_Extension {

  public function getName() {
    return 'app.twigextensions';
  }

  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('l', [$this, 'link']),
      new \Twig_SimpleFunction('banner', [$this, 'banner']),
      new \Twig_SimpleFunction('image', [$this, 'image']),
      new \Twig_SimpleFunction('ui_input', [$this, 'ui_input']),
      new \Twig_SimpleFunction('ui_textarea', [$this, 'ui_textarea']),
      new \Twig_SimpleFunction('ui_select', [$this, 'ui_select']),
      new \Twig_SimpleFunction('ui_datepicker', [$this, 'ui_datepicker']),
      new \Twig_SimpleFunction('ui_radio', [$this, 'ui_radio']),
      new \Twig_SimpleFunction('ui_checkbox', [$this, 'ui_checkbox']),
      new \Twig_SimpleFunction('ui_checkboxes', [$this, 'ui_checkboxes']),
      new \Twig_SimpleFunction('ui_event_options', [$this, 'ui_event_options']),
      new \Twig_SimpleFunction('ui_event_options_select', [$this, 'ui_event_options_select']),
    ];
  }

  public function link($url) {
    global $base_url;
    return "$base_url/$url";
  }

  public function image($uri, $width = null, $height = null): string {
    $image = ImageUtils::getImageFromUri($uri, $width, $height);
    return $image['url'];
  }

  public function banner($headline, $node): string {
    $v = new BannerView();
    $v->headline = $headline;
//    $v->image = $image;
    $el = [
      "#theme" => "banner",
      "#v" => $v
    ];
    $html = render($el);
    return $html;
  }

  public static function ui_input($name, $label, $required = "", $type = "text", $autocomplete = null, $pattern = null) {
    $v = new UI_InputFieldView();
    $v->name = $name;
    $v->type = $type;
    if($required) $v->required = "required";
    $v->label = $label;
    $v->autocomplete = $autocomplete;
    if(!$autocomplete) {
      $v->autocomplete = $name;
    }
    $v->pattern = $pattern;
    $el = [
      "#theme" => "ui_input",
      "#v" => $v
    ];
    return render($el);
  }

  public static function ui_textarea($name, $label, $required = "", $autocomplete = null, $rows = 4) {
    $v = new UI_TextareaFieldView();
    $v->name = $name;
    if($required) $v->required = "required";
    $v->label = $label;
    $v->rows = $rows;
    $v->autocomplete = $autocomplete;
    if(!$autocomplete) {
      $v->autocomplete = $name;
    }
    $el = [
      "#theme" => "ui_textarea",
      "#v" => $v
    ];
    return render($el);
  }

  public static function ui_select($name, $options, $label, $required = "", $blankString = "", $show = null) {
    $v = new UI_SelectFieldView();
    $v->name = $name;
    $v->options = $options;
    $v->label = $label;
    if($required) $v->required = "required";
    $v->blankString = $blankString;
    if($show) {
      $v->show = $show;
    }
    $el = [
      "#theme" => "ui_select",
      "#v" => $v
    ];
    return render($el);
  }

  public static function ui_datepicker($name, $label, $required = "", $options = "") {
    $v = new UI_DatepickerFieldView();
    $v->name = $name;
    if($required) $v->required = "required";
    $v->label = $label;
    $v->options = $options;
    $el = [
      "#theme" => "ui_datepicker",
      "#v" => $v
    ];
    return render($el);
  }

  public static function ui_radio($id, $name, $label, $value, $checked = "", $required = false, $show = null) {
    $v = new UI_RadioFieldView();
    $v->id = $id;
    $v->name = $name;
    $v->label = $label;
    $v->value = $value;
    $v->checked = $checked;
    if($required) $v->required = "required";
    if($show) {
      $v->show = "data-show=$show";
    }
    $el = [
      "#theme" => "ui_radio",
      "#v" => $v
    ];
    return render($el);
  }

  public static function ui_checkbox($id, $name, $label, $value, $checked = "", $required = "", $show = null, $attributes = null) {
    $v = new UI_CheckboxFieldView();
    $v->id = $id;
    $v->name = $name;
    $v->label = $label;
    $v->value = $value;
    $v->checked = $checked;
    $v->attributes = $attributes;
    if($required) $v->required = "required";
    if($show) {
      $v->show = "data-show=$show";
    }
    $el = [
      "#theme" => "ui_checkbox",
      "#v" => $v
    ];
    return render($el);
  }

  public static function ui_checkboxes($name, $options, $required = "") {
    $v = new UI_CheckboxesFieldView();
    $v->name = $name;
    $v->options = $options;
    if($required) $v->required = "required";
    $el = [
      "#theme" => "ui_checkboxes",
      "#v" => $v
    ];
    return render($el);
  }

  public static function ui_event_options($name, $options) {
    $v = new UI_EventOptionsView();
    $v->name = $name;
    $v->options = $options;
    $el = [
      "#theme" => "ui_event_options",
      "#v" => $v
    ];
    return render($el);
  }

  public static function ui_event_options_select($name, $months) {
    $v = new UI_EventOptionsSelectView($name, $months);
    $el = [
      "#theme" => "ui_event_options_select",
      "#v" => $v
    ];
    return render($el);
  }

}

