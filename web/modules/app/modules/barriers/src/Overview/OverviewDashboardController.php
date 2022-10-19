<?php

namespace Drupal\barriers\Overview;

use Drupal\datadashboard\DataDashboardControllerBase;
use Drupal\datadashboard\DataDashboardControllerInterface;

class OverviewDashboardController extends DataDashboardControllerBase implements DataDashboardControllerInterface {

  function getKPILabels(): array {
    return [[t("% of youth who faced barriers"), t("accessing mentors during adolescence")]];
  }

  function getTree($rows) {
    return new OverviewTree($rows);
  }

  function getRows(): array {
    $handle = fopen(__DIR__ . '/data/data.csv', "r");
    $rows = [];
    $headerRow = true;
    $key = 0;

    while (($data = fgetcsv($handle)) !== FALSE) {
      if($headerRow) {
        $headerRow = false;
        continue;
      }

      $rows[] = $this->getRow($data);
      $key++;
    }
    return $rows;
  }

}
