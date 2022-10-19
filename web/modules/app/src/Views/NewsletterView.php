<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\Utils;

  class NewsletterView {

    public $nid;
    public $lang;
    public $body;
    public $provinceSelectOptions;

    public function __construct() {
      $provinces = [
        "ON" => t("Ontario")->__toString(),
        "QC" => t("Quebec")->__toString(),
        "NS" => t("Nova Scotia")->__toString(),
        "NB" => t("New Brunswick")->__toString(),
        "MB" => t("Manitoba")->__toString(),
        "BC" => t("British Columbia")->__toString(),
        "PE" => t("Prince Edward Island")->__toString(),
        "SK" => t("Saskatchewan")->__toString(),
        "AB" => t("Alberta")->__toString(),
        "NL" => t("Newfoundland and Labrador")->__toString(),
        "NT" => t("Northwest Territories")->__toString(),
        "YT" => t("Yukon")->__toString(),
        "NU" => t("Nunavut")->__toString()
      ];
      $provinces = Utils::sort($provinces);
      $this->provinceSelectOptions = $provinces;
    }

  }

}
