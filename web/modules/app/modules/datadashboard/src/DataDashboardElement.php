<?php

namespace Drupal\datadashboard {

  class DataDashboardElement extends DataDashboardElementBase implements DataDashboardElementInterface {

    public $access_to_mentoring;
    public $access_to_formal_mentoring;
    public $unmet_mentoring_needs__barriers;

    public function getValues(): array {
      return [
        $this->access_to_mentoring,
        $this->access_to_formal_mentoring,
        $this->unmet_mentoring_needs__barriers
      ];
    }

  }

}
