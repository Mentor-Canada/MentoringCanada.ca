<?php

namespace Drupal\app_payment\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * @FieldFormatter(
 *   id = "total_formatter",
 *   label = @Translation("Total Formatter"),
 *   field_types = {
 *     "payment_details_item"
 *   }
 * )
 */
class TotalFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = ['#plain_text' => number_format($item->getValue()['total_amount'], 2)];
    }

    return $element;
  }

}
