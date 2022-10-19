<?php

namespace Drupal\adultswhomentor;

use Drupal\datadashboard\DataDashboardElementBase;
use Drupal\datadashboard\DataDashboardElementInterface;

class AdultsWhoMentorElement extends DataDashboardElementBase implements DataDashboardElementInterface {

  public $current_mentor;
  public $past_mentor;
  public $likely_to_mentor;

  public function getValues(): array {
    return [
      $this->current_mentor,
      $this->past_mentor,
      $this->likely_to_mentor,
    ];
  }
}
