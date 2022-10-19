<?php

namespace Drupal\app\Controllers;

use Drupal\app\Models\PaymentDetails;
use Drupal\app\Utils\NodeUtils;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\EventView;
use Drupal\app\Views\PageHeaderView;
use Drupal\app\Models\EventSubmissionEntity;
use Drupal\node\Entity\Node;
use Drupal\app\Utils\Utils;
use Drupal\app\Models\MailingLists;
use Drupal\app\Utils\ParagraphUtils;

class PageEventController extends BaseController {

  public static $type = "/^event$/";

  /** @var $v EventView */
  protected $v;

  public function preprocess_html(&$vars) {
    parent::preprocess_html($vars);
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new EventView($node);

    $this->v->banner = new BannerView($node);
    $this->v->banner->eyebrow = $this->v->bannerAndHeaderDate;
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }

    $this->v->header = new PageHeaderView($node);
    $this->v->header->eyebrow = $this->v->bannerAndHeaderDate;

    $template = $node->get('field_template')->getValue()[0]['value'];
    if($template) {
      $vars["classes"] .= " page-template--$template";
    }
  }

  public function theme_suggestions_page_alter(&$suggestions, $vars) {
    $node = NodeUtils::getNodeFromUrl();
    $template = $node->get('field_template')->getValue()[0]['value'];
    if($template) {
      $template = str_replace("-", "_", $template);
      $suggestions[] = "page__template__$template";
    }
  }

  public function preprocess_page(&$vars) {
    parent::preprocess_page($vars);

    /** @var $node \Drupal\node\Entity\Node */
    $node = $vars["node"];
    $sectionValues = $node->get("field_sections")->getValue();
    $sections = [];
    foreach($sectionValues as $section) {
      $target_id = $section["target_id"];
      $p = ParagraphUtils::getParagraph($target_id, $node);
      $sections[] = $p;
    }
    $sections = ParagraphUtils::zebraSections($sections);
    $this->v->sections = $sections;
    $firstSectionWidth = ParagraphUtils::getSectionAttributes($sections, "first")["width"];
    $this->v->header->width = $firstSectionWidth;
    $this->v->eventWhereWhenWidth = $firstSectionWidth;
    $this->v->formZebra = ParagraphUtils::getSectionAttributes($sections, "last")["zebra"] == "odd" ? "even" : "odd";

    $vars['v'] = $this->v;
  }

  public function onSubmit(array $data, array $files, array &$vars) {
    if(!Utils::isValidReCaptcha()) {
      return;
    }

    $lang = Utils::lang();

    $entity = new EventSubmissionEntity();

    $eventId = $data[$entity->EVENT_ID];
    $node = Node::load($eventId);
    if(is_null($node)) return;
    if($node->hasTranslation($lang)) {
      $node = $node->getTranslation($lang);
    }

    $photoVideoConsent = $data[$entity->PHOTO_VIDEO_CONSENT];
    if($node->get("field_has_form_pho_vid_consent")->getValue()[0]["value"]) {
      if(!$photoVideoConsent) $data[$entity->PHOTO_VIDEO_CONSENT] = "No";
    } else {
      $data[$entity->PHOTO_VIDEO_CONSENT] = "N/A";
    }

    if($data[$entity->IN_CANADA] == "Yes") {
      $data[$entity->COUNTRY] = "Canada";
    } else {
      if(!$node->get("field_has_form_country")->getValue()[0]["value"]) {
        $data[$entity->COUNTRY] = "N/A";
      }
    }

    if($node->get("field_has_form_indigenous")->getValue()[0]["value"]) {
      if($data[$entity->IS_INDIGENOUS] == "No") {
        $data[$entity->INDIGENOUS] = "N/A";
      }
    } else {
      $data[$entity->IS_INDIGENOUS] = "N/A";
      $data[$entity->INDIGENOUS] = "N/A";
    }

    if($node->get("field_event_type")->getValue()[0]["target_id"] != "3") {
      $data[$entity->TRAINING_TYPE] = "N/A";
    }

    foreach($data as $key => $value) {
      if(strpos($key, "field_") !== 0) {
        continue;
      }
      $entity->set($key, $value);
    }

    $entity->oneOnOneTopics = $data["field_submission_1_on_1_topics"];

    if(!empty($data['extras'])) {
      $entity->set($entity->EXTRAS, json_encode($data['extras']));
    }

    $submissionNode = $entity->save();

    $email = $data[$entity->EMAIL];
    $eventTitle = $node->getTitle();
    $eventUrl = $node->toUrl()->setAbsolute()->toString();
    $name = "{$data[$entity->FIRST_NAME]} {$data[$entity->LAST_NAME]}";
    $firstName = $data[$entity->FIRST_NAME];
    $lastName = $data[$entity->LAST_NAME];
    $province = $data[$entity->PROVINCE] ? $data[$entity->PROVINCE] : "N/A";
    if($province == "N/A") {
      $postalCode = "N/A";
    } else {
      $postalCode = $data[$entity->POSTAL_CODE] ? $data[$entity->POSTAL_CODE] : null;
    }
    $organization = $data[$entity->ORGANIZATION];

    $optionId = $data[$entity->EVENT_OPTION];
    $eventOptionNode = Node::load($optionId);
    if(is_null($eventOptionNode)) return;
    if($eventOptionNode->hasTranslation($lang)) {
      $eventOptionNode = $eventOptionNode->getTranslation($lang);
    }
    $eventOption = new \Drupal\app\Models\EventOptionEntity($eventOptionNode, $node);

    $mailingLists = new MailingLists();
    if($data['newsletter'] == "Yes") {
      $mailingLists->addToNewsletter($email, $lang, $name, $firstName, $lastName, $province, $postalCode, $organization);
    }

    /* @var $mailManager \Drupal\Core\Mail\MailManager */
    $mailManager = \Drupal::service('plugin.manager.mail');

    $emailSubject = $node->get("field_email_subject")->getValue()[0]["value"];
    $emailBody = $node->get("field_email_body")->getValue()[0]["value"];

    $emailYoutubeId = $node->get("field_email_youtube_id")->getValue()[0]["value"];
    $renderYoutubeInEmail = $node->get("field_insert_youtube_in_email")->getValue()[0]["value"];
    if($emailYoutubeId && $renderYoutubeInEmail) {
      $ytShareUrl = "https://youtu.be/";
      if(substr($emailYoutubeId, 0, strlen($ytShareUrl)) === $ytShareUrl) {
        $emailYoutubeId = str_replace($ytShareUrl, "", $emailYoutubeId);
      }
      $ytLinkUrl = "$ytShareUrl$emailYoutubeId";
      $ytPosterUrl = "https://img.youtube.com/vi/$emailYoutubeId/hqdefault.jpg";
    }

    $paymentDetails = PaymentDetails::create($node, $data);
    if($paymentDetails) {
      $submissionNode->set('field_payment_details', [
        'promo_code' => $paymentDetails->promoCode,
        'paypal_id' => $paymentDetails->payPalId,
        'total_amount' => $paymentDetails->totalAmount,
        'paid_amount' => $paymentDetails->paidAmount,
        'payment_mode' => $paymentDetails->paymentMode
      ]);
      $submissionNode->save();
    }

    $mailManager->mail("app", "event", $email, $lang, [
      "lang"        => $lang,
      "name"        => $firstName,
      "body"        => $emailBody,
      "subject"     => $emailSubject,
      "ytLinkUrl"   => $ytLinkUrl,
      "ytPosterUrl" => $ytPosterUrl,
      "eventTitle"  => $eventTitle,
      "eventOption" => $eventOption,
      "eventUrl"    => $eventUrl,
      "payment" => $paymentDetails
    ]);

    $submittedPath = Utils::lang() == "fr" ? "evenements/soumis" : "events/submitted";
    $submittedPath .= "?e=$eventId";
    $this->goto($submittedPath);
  }

}
