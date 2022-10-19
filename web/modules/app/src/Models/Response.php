<?php

namespace Drupal\app\Models;

class Response {

  public $success;
  public $message;
  public $data = [];

  public function __construct($success = true, $message = null) {
    $this->success = $success;
    $this->message = $message;
  }

}
