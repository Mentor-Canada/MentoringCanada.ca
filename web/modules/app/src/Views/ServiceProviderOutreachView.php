<?php

namespace Drupal\app\Views {

  use DateTime;
  use DateTimeZone;
  use Drupal\app\Utils\Utils;
  use Drupal\node\Entity\Node;

  class ServiceProviderOutreachView {

    public $CALLER = "field_submission_caller";
    public $EMAIL = "field_submission_email_address";
    public $FIRST_NAME = "field_submission_first_name";
    public $LAST_NAME = "field_submission_last_name";
    public $ORGANIZATION = "field_submission_organization";
    public $LANGUAGE = "field_submission_language";
    public $AWARE_SRDC_CMP = "field_submission_aware_srdc_cmp";
    public $HAS_SERVICES = "field_submission_has_services";
    public $RECEIVED_SURVEY = "field_submission_received_survey";
    public $SURVEY_COMPLETE = "field_submission_survey_complete";
    public $SURVEY_STARTED = "field_submission_survey_started";
    public $SURVEY_WILLING = "field_submission_survey_willing";
    public $SURVEY_WHY_NOT = "field_submission_survey_why_not";
    public $SURVEY_CONCERNS = "field_submission_survey_concerns";
    public $SURVEY_ASK_1 = "field_submission_survey_ask_1";
    public $SURVEY_ASK_2 = "field_submission_survey_ask_2";
    public $CONNECTOR_LIST = "field_submission_connector_list";
    public $NEWSLETTER_LIST = "field_submission_newsletter_list";

    private $values = [];

    public function save() {
      $date = new DateTime("now", new DateTimeZone('America/Montreal') );
      $dateString = $date->format('Y-m-d H:i:s');
      $title = "SPO Submission - {$this->get($this->FIRST_NAME)} {$this->get($this->LAST_NAME)} - $dateString";

      $lang = Utils::lang();

      /** @var $user \Drupal\Core\Session\AccountProxy */
      $user = \Drupal::currentUser();
      $caller = $user->getUsername();

      $fields = [
        'type' => 'submission_spo',
        'title' => $title,
        $this->CALLER => ['value' => $caller],
        $this->EMAIL => ['value' => $this->get($this->EMAIL) ? $this->get($this->EMAIL) : "N/A"],
        $this->FIRST_NAME => ['value' => $this->get($this->FIRST_NAME)],
        $this->LAST_NAME => ['value' => $this->get($this->LAST_NAME)],
        $this->LANGUAGE => ['value' => $lang],
        $this->ORGANIZATION => ['value' => $this->get($this->ORGANIZATION)],
        $this->AWARE_SRDC_CMP => ['value' => $this->get($this->AWARE_SRDC_CMP) ? $this->get($this->AWARE_SRDC_CMP) : "N/A"],
        $this->HAS_SERVICES => ['value' => $this->get($this->HAS_SERVICES) ? $this->get($this->HAS_SERVICES) : "N/A"],
        $this->RECEIVED_SURVEY => ['value' => $this->get($this->RECEIVED_SURVEY) ? $this->get($this->RECEIVED_SURVEY) : "N/A"],
        $this->SURVEY_COMPLETE => ['value' => $this->get($this->SURVEY_COMPLETE) ? $this->get($this->SURVEY_COMPLETE) : "N/A"],
        $this->SURVEY_STARTED => ['value' => $this->get($this->SURVEY_STARTED) ? $this->get($this->SURVEY_STARTED) : "N/A"],
        $this->SURVEY_WILLING => ['value' => $this->get($this->SURVEY_WILLING) ? $this->get($this->SURVEY_WILLING) : "N/A"],
        $this->SURVEY_WHY_NOT => ['value' => $this->get($this->SURVEY_WHY_NOT) ? $this->get($this->SURVEY_WHY_NOT) : "N/A"],
        $this->SURVEY_CONCERNS => ['value' => $this->get($this->SURVEY_CONCERNS) ? $this->get($this->SURVEY_CONCERNS) : "N/A"],
        $this->SURVEY_ASK_1 => ['value' => $this->get($this->SURVEY_ASK_1) ? $this->get($this->SURVEY_ASK_1) : "N/A"],
        $this->SURVEY_ASK_2 => ['value' => $this->get($this->SURVEY_ASK_2) ? $this->get($this->SURVEY_ASK_2) : "N/A"],
        $this->CONNECTOR_LIST => ['value' => $this->get($this->CONNECTOR_LIST) ? $this->get($this->CONNECTOR_LIST) : "N/A"],
        $this->NEWSLETTER_LIST => ['value' => $this->get($this->NEWSLETTER_LIST) ? $this->get($this->NEWSLETTER_LIST) : "N/A"],
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

}
