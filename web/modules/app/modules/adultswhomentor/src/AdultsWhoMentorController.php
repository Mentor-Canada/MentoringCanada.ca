<?php

namespace Drupal\adultswhomentor;

use Drupal\datadashboard\DataDashboardControllerBase;
use Drupal\datadashboard\DataDashboardControllerInterface;

class AdultsWhoMentorController extends DataDashboardControllerBase implements DataDashboardControllerInterface {

  public static $path = "/^\/(?:state-of-mentoring-research-initiative\/dashboard|enquete-sur-letat-du-mentorat\/tableau-de-bord)\/mentors$/";

  function getKPILabels(): array {
    return [[t("Current Mentors")], [t("Past Mentors")], [t("Likely to Mentor"), t("in the next 5 years")]];
  }

  function getTree($rows) {
    return new AdultsWhoMentorTree($rows);
  }

  function getRows(): array {
    $handle = fopen(__DIR__ . '/../data/data.csv', "r");
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
