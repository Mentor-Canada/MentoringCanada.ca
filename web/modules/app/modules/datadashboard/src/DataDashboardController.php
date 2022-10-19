<?php

namespace Drupal\datadashboard;

class DataDashboardController extends DataDashboardControllerBase implements DataDashboardControllerInterface {

  public static $path = "/^\/(?:state-of-mentoring-research-initiative\/dashboard|enquete-sur-letat-du-mentorat\/tableau-de-bord)$/";

  function getKPILabels(): array {
    return [[t("Access to"), t("Mentors")], [t("Access to"), t("Formal Mentors (among mentored youth)")], [t("Unmet Needs:"), t("Access to Mentors (Unmet Needs)")]];
  }

  function getTree($rows) {
    return new DataDashboardTree($rows);
  }

  function getRows(): array {
    $handle = fopen(__DIR__ . '/../data/Output for Dashboard_June 15.csv', "r");
    $rows = [];
    $headerRow = true;
    $key = 0;

    while (($data = fgetcsv($handle)) !== FALSE) {
      if($headerRow) {
        $headerRow = false;
        continue;
      }

      $factorLabel = $data[1];
      if($factorLabel != "Access to Mentoring" && $factorLabel != "Access to Formal Mentoring" && $factorLabel != "Unmet Mentoring Needs/ Barriers") {
        continue;
      }
      $rows[] = $this->getRow($data);
      $key++;
    }
    return $rows;
  }
}
