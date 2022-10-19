<?php

namespace Drupal\datadashboard;

abstract class DataDashboardElementBase {
  public $region;
  public $age;
  public $gender;
  public $factor;

  public $color = '';
  public $trail = [];

  public function json() {
    return [
      'label' => t($this->region) . ": " . t($this->gender) . ": " . t($this->age),
      'color' => $this->color,
      'trail' => $this->trail,
      'values' => $this->getValues()
    ];
  }
}
