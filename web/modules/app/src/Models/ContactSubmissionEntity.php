<?php

namespace Drupal\app\Models;

use DateTime;
use DateTimeZone;
use Drupal\app\Utils\Utils;
use Drupal\node\Entity\Node;

class ContactSubmissionEntity {

  public $EMAIL = "field_submission_email_address";
  public $FIRST_NAME = "field_submission_first_name";
  public $LAST_NAME = "field_submission_last_name";
  public $SUBJECT = "field_submission_subject";
  public $MESSAGE = "field_submission_message";
  public $LANGUAGE = "field_submission_language";

  private $values = [];

  public function save() {
    $date = new DateTime("now", new DateTimeZone('America/Montreal') );
    $dateString = $date->format('Y-m-d H:i:s');
    $title = "Contact Submission {$this->get($this->EMAIL)} $dateString";

    $lang = Utils::lang();

    $fields = [
      'type' => 'submission_contact',
      'title' => $title,
      $this->EMAIL => ['value' => $this->get($this->EMAIL)],
      $this->FIRST_NAME => ['value' => $this->get($this->FIRST_NAME)],
      $this->LAST_NAME => ['value' => $this->get($this->LAST_NAME)],
      $this->SUBJECT => ['value' => $this->get($this->SUBJECT)],
      $this->MESSAGE => ['value' => $this->get($this->MESSAGE)],
      $this->LANGUAGE => ['value' => $lang],
    ];

    $node = Node::create($fields);
    $node->save();
    return $node;
  }

  public function set($field, $value) {
    $this->values[$field] = $value;
  }

  public function get($field) {
    return $this->values[$field];
  }

}
