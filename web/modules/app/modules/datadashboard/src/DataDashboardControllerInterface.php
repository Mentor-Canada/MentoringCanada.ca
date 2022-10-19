<?php

namespace Drupal\datadashboard;

interface DataDashboardControllerInterface {
  function getKPILabels(): array;
  function getTree($rows);
  function getRows(): array;
}
