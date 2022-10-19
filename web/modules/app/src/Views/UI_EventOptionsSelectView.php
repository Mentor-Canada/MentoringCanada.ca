<?php

namespace Drupal\app\Views;

class UI_EventOptionsSelectView {

  public $name;
  public $months;
  public $optionsMonths = [];

  public function __construct($name, $months) {
    $this->name = $name;
    $this->months = $months;

    foreach($months as $key => $month) {
      $this->optionsMonths[$key] = $month['label'];
    }

  }

}
