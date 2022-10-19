<?php

namespace Drupal\barriers\Breakdown;

use Drupal\datadashboard\DataDashboardElementBase;
use Drupal\datadashboard\DataDashboardElementInterface;

class Element extends DataDashboardElementBase implements DataDashboardElementInterface {

  public function getValues(): array {
    return [
      $this->{i_didn’t_know_how_to_find_a_mentor},
      $this->{i_didn’t_understand_what_mentoring_was_or_the_value_of_having_a_mentor},
      $this->{there_weren’t_any_mentoring_programs_available_to_me}
    ];
  }
}
