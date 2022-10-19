<?php

namespace Drupal\app\Views;

abstract class PageView {

  public $messages;

  public function __construct() {
    $messages = drupal_get_messages();
    $el = ["#theme" => "status_messages", "#message_list" => $messages];
    $messages = render($el);
    $this->messages = $messages;
  }

}
