<?php

namespace Drupal\app\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * @FieldType(
 *  id = "dashboard_data_item",
 *  label = "Dashboard Data Item",
 *  category = "App",
 *  default_widget = "dashboard_data_widget"
 * )
 */
class DashboardDataItem extends FieldItemBase implements FieldItemInterface {

  public static $columns = [
    'region', 'factor', 'age_group', 'gender', 'number', 'percentage', 'additional_factor_details'
  ];

  /**
   * Defines field item properties.
   *
   * Properties that are required to constitute a valid, non-empty item should
   * be denoted with \Drupal\Core\TypedData\DataDefinition::setRequired().
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface[]
   *   An array of property definitions of contained properties, keyed by
   *   property name.
   *
   * @see \Drupal\Core\Field\BaseFieldDefinition
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    foreach(self::$columns as $column) {
      $properties[$column] = DataDefinition::create('string');
    }
    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $result = ['columns' => []];
    foreach(self::$columns as $column) {
      $result['columns'][$column] = ['type' => 'text'];
    }
    return $result;
  }

  public function isEmpty() {
    $result = true;
    foreach(self::$columns as $column) {
      if($this->get($column)->getValue() != '') {
        $result = false;
      }
    }
    return $result;
  }

}
