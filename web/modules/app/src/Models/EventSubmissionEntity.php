<?php

namespace Drupal\app\Models;

use DateTime;
use DateTimeZone;
use Drupal\app\Utils\Utils;
use Drupal\node\Entity\Node;

class EventSubmissionEntity {

  public $EVENT_ID = "field_submission_event_id";
  public $EVENT_OPTION = "field_submission_event_option";
  public $EMAIL = "field_submission_email_address";
  public $FIRST_NAME = "field_submission_first_name";
  public $LAST_NAME = "field_submission_last_name";
  public $LANGUAGE = "field_submission_language";
  public $ROLE = "field_submission_role";
  public $MENTOR_TITLE = "field_submission_mentor_title";
  public $ORGANIZATION = "field_submission_organization";
  public $PHONE_NUMBER = "field_submission_phone_number";
  public $POSTAL_CODE = "field_submission_postal_code";
  public $PROVINCE = "field_submission_province";
  public $COUNTRY = "field_submission_country";
  public $AGE = "field_submission_age";
  public $BIRTHDATE = "field_submission_birthdate";
  public $LINKEDIN = "field_submission_linkedin";
  public $COMMENTS = "field_submission_comments";
  public $PHOTO_VIDEO_CONSENT = "field_submission_pho_vid_consent";
  public $SHARING_CONSENT = "field_submission_sharing_consent";
  public $IN_CANADA = "field_submission_in_canada";
  public $IS_INDIGENOUS = "field_submission_is_indigenous";
  public $INDIGENOUS = "field_submission_indigenous";
  public $TRAINING_TYPE = "field_submission_training_type";
  public $ONE_ON_ONE_TOPICS = "field_submission_1_on_1_topics[]";
  public $ONE_ON_ONE_COMMENTS = "field_submission_1_on_1_comments";
  public $EXTRAS = "field_extras";

  public $oneOnOneTopics;

  private $values = [];

  public function save() {
    $date = new DateTime("now", new DateTimeZone('America/Montreal') );
    $dateString = $date->format('Y-m-d H:i:s');
    $title = "Event Submission {$this->get($this->EMAIL)} $dateString";
    $lang = Utils::lang();

    $oneOnOneTopics = array_map(function($value) { return ["value" => $value]; }, $this->oneOnOneTopics);

    $fields = [
      'type' => 'submission_event',
      'title' => $title,
      $this->EVENT_ID => ['target_id' => $this->get($this->EVENT_ID)],
      $this->EVENT_OPTION => ['target_id' => $this->get($this->EVENT_OPTION)],
      $this->EMAIL => ['value' => $this->get($this->EMAIL)],
      $this->FIRST_NAME => ['value' => $this->get($this->FIRST_NAME)],
      $this->LAST_NAME => ['value' => $this->get($this->LAST_NAME)],
      $this->LANGUAGE => ['value' => $lang],
      $this->ROLE => ['value' => $this->get($this->ROLE)],
      $this->MENTOR_TITLE => ['value' => $this->get($this->MENTOR_TITLE)],
      $this->ORGANIZATION => ['value' => $this->get($this->ORGANIZATION)],
      $this->PHONE_NUMBER => ['value' => $this->get($this->PHONE_NUMBER)],
      $this->POSTAL_CODE => ['value' => $this->get($this->POSTAL_CODE)],
      $this->PROVINCE => ['value' => $this->get($this->PROVINCE)],
      $this->COUNTRY => ['value' => $this->get($this->COUNTRY)],
      $this->AGE => ['value' => $this->get($this->AGE)],
      $this->BIRTHDATE => ['value' => $this->get($this->BIRTHDATE)],
      $this->LINKEDIN => ['value' => $this->get($this->LINKEDIN)],
      $this->COMMENTS => ['value' => $this->get($this->COMMENTS)],
      $this->PHOTO_VIDEO_CONSENT => ['value' => $this->get($this->PHOTO_VIDEO_CONSENT)],
      $this->SHARING_CONSENT => ['value' => $this->get($this->SHARING_CONSENT)],
      $this->IN_CANADA => ['value' => $this->get($this->IN_CANADA)],
      $this->IS_INDIGENOUS => ['value' => $this->get($this->IS_INDIGENOUS)],
      $this->INDIGENOUS => ['value' => $this->get($this->INDIGENOUS)],
      $this->TRAINING_TYPE => ['value' => $this->get($this->TRAINING_TYPE)],
      $this->ONE_ON_ONE_COMMENTS => ['value' => $this->get($this->ONE_ON_ONE_COMMENTS)],
      "field_submission_1_on_1_topics" => $oneOnOneTopics,
      $this->EXTRAS => ['value' => $this->get($this->EXTRAS)]
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
