<?php

namespace Drupal\app_payment;

use Drupal\node\Entity\Node;
use Zend\Diactoros\Response\JsonResponse;

class PromoController {

  function validate() {
    $promoCode = PromoCode::createFromRequest();
    $eventNode = Node::load($promoCode->eventId);
    $paymentField = $eventNode->get('field_payment')->getValue()[0];
    $promoEnabled = $paymentField['promo'] == "1";
    if(!$promoEnabled || $paymentField['promo_code'] != $promoCode->promoCode) {
      return new JsonResponse(["error" => "invalid code"]);
    }
    return new JsonResponse(["data" => [
      "amount" => number_format($paymentField['promo_amount'], 2)
    ]]);
  }

}
