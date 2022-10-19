<?php

namespace Drupal\barriers\Breakdown;

use Drupal\datadashboard\DataDashboardControllerBase;
use Drupal\datadashboard\DataDashboardControllerInterface;

class BreakdownChartController extends DataDashboardControllerBase implements DataDashboardControllerInterface {

  function getKPILabels(): array {
    return [
      [t("I didn’t know"), t("how to find a mentor")],
      [t("I didn’t understand what mentoring "), t("was or the value of having a mentor")],
      [t("There weren’t any"), t("mentoring programs available to me")]
    ];
  }

  function getTree($rows) {
    return new Tree($rows);
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
