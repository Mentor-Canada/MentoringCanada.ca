<?php

namespace Drupal\datadashboard;

interface DataDashboardElementInterface {
  function getValues(): array;
}
