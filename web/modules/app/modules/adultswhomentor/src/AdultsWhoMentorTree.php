<?php

namespace Drupal\adultswhomentor;

use Drupal\datadashboard\DataDashboardElementInterface;
use Drupal\datadashboard\DataDashboardTree;

class AdultsWhoMentorTree extends DataDashboardTree {

  public function __construct(array $rows) {
    parent::__construct($rows);
  }

  protected function getElement(&$elementReference): DataDashboardElementInterface {
    if(empty($elementReference)) {
      $elementReference = new AdultsWhoMentorElement();
    }
    return $elementReference;
  }

}
