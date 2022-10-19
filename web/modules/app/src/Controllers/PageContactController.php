<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\DateUtils;
use Drupal\app\Utils\NodeUtils;
use Drupal\app\Utils\Utils;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\PageContactView;
use Drupal\app\Models\ContactSubmissionEntity;

class PageContactController extends BaseController {

  public static $type = "/^contact$/";

  /** @var $v PageContactView */
  private $v;

  public function preprocess_html(&$vars) {
    parent::preprocess_html($vars);
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new PageContactView();
    $this->v->banner = new BannerView($node);
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }

    $this->v->globalSettings = Utils::globalSettings();
  }

  public function preprocess_page(&$vars) {
    /* @var $entity \Drupal\app\Models\ContactSubmissionEntity */
    $entity = new ContactSubmissionEntity();
    $this->v->entity = $entity;
    $vars['v'] = $this->v;
  }

  public function onSubmit(array $data, array $files, array &$vars) {
    if(!Utils::isValidReCaptcha()) {
      return;
    }

    /* @var $entity \Drupal\app\Models\ContactSubmissionEntity */
    $entity = new ContactSubmissionEntity();

    foreach($data as $key => $value) {
      if(strpos($key, "field_") !== 0) {
        continue;
      }
      $entity->set($key, $value);
    }
    $entity->save();

    /* @var $mailManager \Drupal\Core\Mail\MailManager */
    $mailManager = \Drupal::service('plugin.manager.mail');

    $fname = $data[$entity->FIRST_NAME];
    $lname = $data[$entity->LAST_NAME];
    $email = $data[$entity->EMAIL];
    $subject = $data[$entity->SUBJECT];
    $message = $data[$entity->MESSAGE];
    $language = Utils::lang();

    $gs = Utils::globalSettings();
    $recipients = $gs["contactFormRecipients"];
    foreach($recipients as $recipient) {

      $emailSubjectTitle = t("MENTOR Canada Contact Form Submission", [], ["langcode" => $recipient["userPreferredLanguage"]]);
      $now = DateUtils::localizeDateTime('now', $recipient["userPreferredLanguage"]);
      $emailSubject = "$emailSubjectTitle - $now";

      $mailManager->mail("app", "contact", $recipient["userEmail"], $recipient["userPreferredLanguage"], [
        "lang" => $recipient["userPreferredLanguage"],
        "name" => $recipient["userName"],
        "subject" => $emailSubject,
        "v" => [
          "fname"     => $fname,
          "lname"     => $lname,
          "email"     => $email,
          "subject"   => $subject,
          "message"   => $message,
          "language"  => $language,
          "title"     => $emailSubjectTitle,
          "datetime"  => $now
        ]
      ]);

    }

    $submittedPath = Utils::lang() == "fr" ? "nous-joindre/soumis" : "contact/submitted";
    $this->goto($submittedPath);
  }


}
