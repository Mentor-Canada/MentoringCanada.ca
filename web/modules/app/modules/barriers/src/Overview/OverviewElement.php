<?php

namespace Drupal\barriers\Overview;

use Drupal\datadashboard\DataDashboardElementBase;
use Drupal\datadashboard\DataDashboardElementInterface;

class OverviewElement extends DataDashboardElementBase implements DataDashboardElementInterface {

  public function getValues(): array {
    return [
      $this->{"%_of_youth_who_faced_barriers_accessing_mentors_during_adolescence"}
    ];
  }
}
