<?php

namespace Drupal\app\Controllers;

class ContactSubmittedController extends SubmittedController {

  public static $path = "/(contact|nous-joindre)\/(submitted|soumis)$/";

  function __construct() {
    $this->heading = t("Thank you!")->__toString();
    $this->subheading = t("Thanks for contacting us! We'll get back to you shortly.")->__toString();
  }

}
