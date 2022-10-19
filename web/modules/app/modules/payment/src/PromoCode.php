<?php

namespace Drupal\app_payment;

class PromoCode {

  public $eventId;
  public $promoCode;

  public static function createFromRequest(): PromoCode {
    $promoCode = new PromoCode();

    $payload = json_decode(\Drupal::request()->getContent());
    $promoCode->eventId = $payload->eventId;
    $promoCode->promoCode = $payload->promoCode;

    return $promoCode;
  }

}
