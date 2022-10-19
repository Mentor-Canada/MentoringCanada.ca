<?php

namespace Drupal\app\Models;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class PaymentDetails {

  public $promoCode;
  public $payPalId;
  public $totalAmount;
  public $paidAmount;
  public $paymentMode;

  private $paymentField;
  private $submission;

  public static function create($eventNode, $submission) {
    $payment = $eventNode->get('field_payment')->getValue()[0];
    if(!$payment && $payment['payment'] != "1") {
      return null;
    }

    return new PaymentDetails($payment, $submission);
  }

  private function __construct($paymentField, $submission) {
    $this->paymentField = $paymentField;
    $this->submission = $submission;

    $this->promoCode = $this->getPromoCode();
    $this->payPalId = $submission["order_id"];
    $this->paidAmount = $this->getPaidAmount();
    $this->totalAmount = $this->getTotalAmount();
    $this->paymentMode = $this->getPaymentMode();
  }

  private function getPaidAmount() {
    if(!$this->payPalId) return 0;

    if($_ENV["PAYPAL_CLIENT_ID"]) {
      $environment = new ProductionEnvironment($_ENV["PAYPAL_CLIENT_ID"], $_ENV["PAYPAL_SECRET"]);
    } else {
      $clientId = "ARU2Hzq8E8w0fwxLjUYkpE9f6q6Q_ebLdAtyElwVKXya71TamAJKVo3B25HwnM8sYhef1nEIR5xusVXC";
      $clientSecret = "EI0bcDtr7l4-iUv19IRMzwCBZX2C-HIPRG8HmTi7Sif-5vJSn1tODCbLbWOjCDlnaKqimAs2NXOhjc8J";
      $environment = new SandboxEnvironment($clientId, $clientSecret);
    }

    $client = new PayPalHttpClient($environment);
    $response = $client->execute(new OrdersGetRequest($this->payPalId));

    return $response->result->purchase_units[0]->amount->value;
  }

  private function getPromoCode() {
    if($this->paymentField['promo'] == '1' && $this->paymentField['promo_code'] == $this->submission['active-promo-code']) {
      return $this->paymentField['promo_code'];
    }
  }

  private function getTotalAmount() {
    return $this->promoCode ? $this->paymentField['promo_amount'] : $this->paymentField['payment_amount'];
  }

  private function getPaymentMode() {
    return $this->payPalId ? "Online" : "Offline";
  }

}
