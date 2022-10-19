<?php

namespace Drupal\app_payment\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * @FieldFormatter(
 *   id = "promo_code_formatter",
 *   label = @Translation("Promo Code Formatter"),
 *   field_types = {
 *     "payment_details_item"
 *   }
 * )
 */
class PromoCodeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = ['#plain_text' => $item->getValue()['promo_code']];
    }

    return $element;
  }

}
