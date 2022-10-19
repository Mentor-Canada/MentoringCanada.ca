<?php

namespace Drupal\app_payment\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * @FieldFormatter(
 *   id = "payment_mode_formatter",
 *   label = @Translation("Payment Mode Formatter"),
 *   field_types = {
 *     "payment_details_item"
 *   }
 * )
 */
class PaymentModeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = ['#plain_text' => $item->getValue()['payment_mode']];
    }

    return $element;
  }

}
