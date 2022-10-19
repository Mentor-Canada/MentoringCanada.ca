<?php

namespace Drupal\app_payment\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "payment_details_widget",
 *   label = @Translation("Payment Details Widget"),
 *   field_types = {
 *     "payment_details_item"
 *   },
 * )
 */
class PaymentDetailsWidget extends WidgetBase implements WidgetInterface {

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $values = $items->get($delta)->getValue();

    $element['promo_code'] = [
      '#type' => 'textfield',
      '#title' => 'Promo Code',
      '#default_value' => $values['promo_code'],
    ];

    $element['paypal_id'] = [
      '#type' => 'textfield',
      '#title' => 'PayPal Id',
      '#default_value' => $values['paypal_id'],
    ];

    $element['total_amount'] = [
      '#type' => 'textfield',
      '#title' => 'Total Amount',
      '#default_value' => $values['total_amount'],
    ];

    $element['paid_amount'] = [
      '#type' => 'textfield',
      '#title' => 'Paid Amount',
      '#default_value' => $values['paid_amount'],
    ];

    $element['payment_mode'] = [
      '#type' => 'textfield',
      '#title' => 'Payment Mode',
      '#default_value' => $values['payment_mode'],
    ];

    return $element;
  }

}
