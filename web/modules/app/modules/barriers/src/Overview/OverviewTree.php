<?php

namespace Drupal\barriers\Overview;

use Drupal\datadashboard\DataDashboardElementInterface;
use Drupal\datadashboard\DataDashboardTree;

class OverviewTree extends DataDashboardTree {

  public function __construct(array $rows) {
    parent::__construct($rows);
  }

  protected function getElement(&$elementReference): DataDashboardElementInterface {
    if(empty($elementReference)) {
      $elementReference = new OverviewElement();
    }
    return $elementReference;
  }

}
