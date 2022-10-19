<?php

namespace Drupal\datadashboard;

class DataDashboardGroup {

  /* @var $element DataDashboardElement[] */
  public $elements = [];

  /* @var $groups DataDashboardGroup[] */
  public $groups = [];

  public $title;

  public $trail = [];

  public $skip = false;

}
