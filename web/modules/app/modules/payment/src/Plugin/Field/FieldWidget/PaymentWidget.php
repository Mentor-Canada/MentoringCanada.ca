<?php

namespace Drupal\app_payment\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "payment_widget",
 *   label = @Translation("Payment Widget"),
 *   field_types = {
 *     "payment_item"
 *   },
 * )
 */
class PaymentWidget extends WidgetBase implements WidgetInterface {

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['#attached']['library'][] = 'app_payment/payment';

    $values = $items->get($delta)->getValue();

    $element['fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Payment'
    ];

    $element['fieldset']['table'] = [
      '#type' => 'table'
    ];

    $element['fieldset']['table'][0]['payment'] = [
      '#type' => 'checkbox',
      '#title' => 'Payment Enabled',
      '#default_value' => $values['payment'],
      '#parents' => [$items->getName(), $delta, 'payment']
    ];

    $element['fieldset']['table'][0]['payment_amount'] = [
      '#type' => 'textfield',
      '#attributes' => [
        ' type' => 'number',
        ' step' => 'any'
      ],
      '#title' => 'Amount',
      '#description' => 'Total amount charged',
      '#default_value' => $values['payment_amount'] ? number_format($values['payment_amount'], 2) : "",
      '#parents' => [$items->getName(), $delta, 'payment_amount']
    ];

    $element['fieldset']['table'][1]['promo'] = [
      '#type' => 'checkbox',
      '#title' => 'Promo Code Enabled',
      '#default_value' => $values['promo'],
      '#parents' => [$items->getName(), $delta, 'promo'],
    ];

    $element['fieldset']['table'][1]['promo_amount'] = [
      '#type' => 'textfield',
      '#attributes' => [
        ' type' => 'number',
      ],
      '#title' => 'Amount',
      '#description' => 'Total amount charged with valid promo code',
      '#default_value' => $values['promo_amount'] ? number_format($values['promo_amount'], 2): "",
      '#parents' => [$items->getName(), $delta, 'promo_amount'],
    ];

    $element['fieldset']['table'][1]['promo_code'] = [
      '#type' => 'textfield',
      '#title' => 'Promo Code',
      '#default_value' => $values['promo_code'],
      '#parents' => [$items->getName(), $delta, 'promo_code'],
    ];

    $element['#element_validate'] = [
      [$this, 'validate'],
    ];

    return $element;
  }

  public function validate($element, FormStateInterface $form_state) {
    $payment = $form_state->getValue('field_payment')[0];
    if($payment['payment'] == 1) {
      if(!$payment['payment_amount']) {
        $el = $element['fieldset']['table'][0]['payment_amount'];
        $form_state->setError($el, 'Please enter the payment amount');
      }
    }

    if($payment['promo'] == 1) {
      if(!$payment['promo_amount']) {
        $el = $element['fieldset']['table'][1]['promo_amount'];
        $form_state->setError($el, 'Please enter the promo amount');
      }
      if(!$payment['promo_code']) {
        $el = $element['fieldset']['table'][1]['promo_code'];
        $form_state->setError($el, 'Please enter the promo code');
      }
    }
  }

}
