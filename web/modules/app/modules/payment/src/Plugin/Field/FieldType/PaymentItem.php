<?php

namespace Drupal\app_payment\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * @FieldType(
 *  id = "payment_item",
 *  label = "Payment Item",
 *  category = "App",
 *  default_widget = "payment_widget"
 * )
 */
class PaymentItem extends FieldItemBase implements FieldItemInterface {

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
    $properties['payment'] = DataDefinition::create('any');
    $properties['payment_amount'] = DataDefinition::create('any');
    $properties['promo'] = DataDefinition::create('any');
    $properties['promo_amount'] = DataDefinition::create('any');
    $properties['promo_code'] = DataDefinition::create('any');
    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $result = ['columns' => []];
    $result['columns']['payment'] = [
      'type' => 'int',
      'size' => 'tiny',
      'unsigned' => true,
      'not null' => true
    ];
    $result['columns']['payment_amount'] = [
      'type' => 'float',
      'unsigned' => true,
    ];
    $result['columns']['promo'] = [
      'type' => 'int',
      'size' => 'tiny',
      'unsigned' => true,
      'not null' => true
    ];
    $result['columns']['promo_amount'] = [
      'type' => 'float',
      'unsigned' => true,
    ];
    $result['columns']['promo_code'] = [
      'type' => 'text',
    ];
    return $result;
  }

  public function isEmpty() {
    $enabled = $this->get('payment')->getValue();
    if($enabled == 1) {
      return false;
    }
    return true;
  }

}
