<?php

namespace Drupal\barriers\Breakdown;

use Drupal\datadashboard\DataDashboardElementInterface;
use Drupal\datadashboard\DataDashboardTree;

class Tree extends DataDashboardTree {

  public function __construct(array $rows) {
    parent::__construct($rows);
  }

  protected function getElement(&$elementReference): DataDashboardElementInterface {
    if(empty($elementReference)) {
      $elementReference = new Element();
    }
    return $elementReference;
  }

}
