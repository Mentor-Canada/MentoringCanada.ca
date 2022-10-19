<?php

namespace Drupal\app_payment\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * @FieldType(
 *  id = "payment_details_item",
 *  label = "Payment Details Item",
 *  category = "App",
 *  default_widget = "payment_details_widget"
 * )
 */
class PaymentDetailsItem extends FieldItemBase implements FieldItemInterface {

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
    $properties['promo_code'] = DataDefinition::create('any');
    $properties['paypal_id'] = DataDefinition::create('any');
    $properties['total_amount'] = DataDefinition::create('any');
    $properties['paid_amount'] = DataDefinition::create('any');
    $properties['payment_mode'] = DataDefinition::create('any');
    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $result = ['columns' => []];
    $result['columns']['promo_code'] = [ 'type' => 'text', ];
    $result['columns']['paypal_id'] = [ 'type' => 'text', ];
    $result['columns']['total_amount'] = [ 'type' => 'text', ];
    $result['columns']['paid_amount'] = [ 'type' => 'text', ];
    $result['columns']['payment_mode'] = [ 'type' => 'text', ];
    return $result;
  }

  public function isEmpty() {
    if(!empty($this->get('promo_code')->getValue())) return false;
    if(!empty($this->get('paypal_id')->getValue())) return false;
    if(!empty($this->get('total_amount')->getValue())) return false;
    if(!empty($this->get('paid_amount')->getValue())) return false;
    if(!empty($this->get('payment_mode')->getValue())) return false;
    return true;
  }

}
