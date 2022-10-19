<?php

namespace Drupal\datadashboard;

class DataDashboardTree {

  public $tree = [];

  /* @var $rows \Drupal\datadashboard\DataDashboardRow[] */
  public function __construct(array $rows) {
    foreach($rows as $row) {
      $this
        ->addRegionGroup($row)
        ->addGenderGroup($row)
        ->addAgeGroup($row)
        ->addData($row);
    }
  }

  public function addRegionGroup(DataDashboardRow $row): DataDashboardTree {
    if(!isset($this->tree[$row->region])) {
      $group = new DataDashboardGroup();
      $group->title = $row->region;
      $group->trail[] = $row->region;
      $this->tree[$row->region] = $group;
    }
    return $this;
  }

  public function addGenderGroup(DataDashboardRow $row): DataDashboardTree {
    if(!isset($this->tree[$row->region]->groups[$row->gender])) {
      $group = new DataDashboardGroup();
      $group->skip = preg_match('/^All/', $row->gender);
      $group->title = $row->gender;
      $group->trail[] = $row->region;
      $group->trail[] = $row->gender;
      $this->tree[$row->region]->groups[$row->gender] = $group;
    }
    return $this;
  }

  public function addAgeGroup(DataDashboardRow $row): DataDashboardTree {
    if(!isset($this->tree[$row->region]->groups[$row->gender]->groups[$row->age])) {
      $group = new DataDashboardGroup();
      $group->skip = preg_match('/^All/', $row->age);
      $group->title = $row->age;
      $group->trail[] = $row->region;
      $group->trail[] = $row->gender;
      $group->trail[] = $row->age;
      $this->tree[$row->region]->groups[$row->gender]->groups[$row->age] = $group;
    }
    return $this;
  }

  public function addData(DataDashboardRow $row) {
    $element = $this->getElement($this->tree[$row->region]->groups[$row->gender]->groups[$row->age]->elements[$row->age]);
    $element->region = $row->region;
    $element->age = $row->age;
    $element->gender = $row->gender;
    $element->factor = $row->factor;
    $element->trail[0] = $row->region;
    $element->trail[1] = $row->gender;
    $element->trail[2] = $row->age;

    $key = strtolower($row->factor);
    $key = str_replace('/', '_', $key);
    $key = str_replace(' ', '_', $key);

    $element->$key = str_replace("%", "", $row->percentage);
  }

  protected function getElement(&$elementReference): DataDashboardElementInterface {
    if(empty($elementReference)) {
      $elementReference = new DataDashboardElement();
    }
    return $elementReference;
  }

  public function getFlat(): array {
    /* @var $row DataDashboardElement */
    $list = [];
    foreach($this->tree as $region) {
      foreach($region->groups as $gender) {
        foreach($gender->groups as $age) {
          foreach($age->elements as $row) {
            $list[] = $row->json();
          }
        }
      }
    }
    return $list;
  }

}
