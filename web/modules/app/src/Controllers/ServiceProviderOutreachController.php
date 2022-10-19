<?php

namespace Drupal\app\Controllers;

use Drupal;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\ServiceProviderOutreachView;
use Drupal\app\Utils\Utils;
use Drupal\app\Models\MailingLists;

class ServiceProviderOutreachController extends BaseController {

  public static $path = "/^\/service-provider-outreach$/";

  /** @var $v ServiceProviderOutreachView */
  protected $v;

  public function preprocess_html(&$vars) {
    parent::preprocess_html($vars);
    $this->v = new ServiceProviderOutreachView();

    $this->v->banner = new BannerView();
    $this->v->banner->title = t("Service Provider Outreach");
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }
  }

  public function preprocess_page(&$vars) {
    $vars['v'] = $this->v;
  }

  public function onSubmit(array $data, array $files, array &$vars) {

    $lang = Utils::lang();

    /** @var $entity ServiceProviderOutreachView */
    $entity = new ServiceProviderOutreachView();

    foreach($data as $key => $value) {
      if(strpos($key, "field_") !== 0) {
        continue;
      }
      $entity->set($key, $value);
    }
    $entity->save();

    $email = $data[$entity->EMAIL];
    $name = "{$data[$entity->FIRST_NAME]} {$data[$entity->LAST_NAME]}";
    $firstName = $data[$entity->FIRST_NAME];

    $mailingLists = new MailingLists();

    if($email) {

      if($data[$entity->NEWSLETTER_LIST] == "Yes") {
        $mailingLists->addToNewsletter($email, Utils::lang(), $name);
      }

      if($data[$entity->SURVEY_ASK_1] == "Yes" || $data[$entity->SURVEY_ASK_2] == "Yes") {
        $requestedSurvey = true;
      }

      if($data[$entity->CONNECTOR_LIST] == "Yes") {
        $requestedConnector = true;
      }

      if($requestedSurvey && $requestedConnector) {
        $emailTheme = "spo_2020_07_survey_and_connector";
      } elseif($requestedSurvey) {
        $emailTheme = "spo_2020_07_survey_only";
      } elseif($requestedConnector) {
        $emailTheme = "spo_2020_07_connector_only";
      }

      if($emailTheme) {

        /* @var $mailManager \Drupal\Core\Mail\MailManager */
        $mailManager = \Drupal::service('plugin.manager.mail');

        $emailSubject = $lang == "fr" ? "Suivi de notre conversation téléphonique" : "Phone Call Follow-up";

        $mailManager->mail("app", $emailTheme, $email, $lang, [
          "lang" => $lang,
          "name" => $firstName,
          "subject" => $emailSubject,
        ]);

      }

    }


  }

}
