<?php

namespace Drupal\app\Controllers;

use Drupal\app\Models\MailingLists;
use Drupal\app\Utils\NodeUtils;
use Drupal\app\Utils\Utils;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\NewsletterView;
use Drupal\node\Entity\Node;

class PageNewsletterController extends BaseController {

  public static $path = "/^\/(?:newsletter|infolettre)$/";

  /** @var $v NewsletterView */
  private $v;

  public function theme_suggestions_page_alter( &$suggestions, $vars ) {
    $suggestions = ["page__newsletter"];
  }

  public function preprocess_html(&$vars) {
    parent::preprocess_html($vars);
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new NewsletterView($node);
    $this->v->banner = new BannerView($node);
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }
    $vars["classes"] .= " page-newsletter";

    $this->v->nid = $node->id();
    $this->v->lang = Utils::lang();
    $this->v->body = $node->get("body")->getValue()[0]["value"];

  }

  public function preprocess_page(&$vars) {
    $vars['v'] = $this->v;
  }

  public function onSubmit(array $data, array $files, array &$vars) {

    $mailingLists = new MailingLists();
    $nid = $data["nid"];
    $email = $data["email"];
    $subStatus = $data["substatus"] != "unsubscribe" ? 1 : 0;

    if(!$subStatus) {
      $mailingLists->removeFromNewsletter($email);
    } else {
      $firstName = $data["firstname"];
      $lastName = $data["lastname"];
      $name = "$firstName $lastName";
      $lang = $data["lang"];
      $province = $data["province"] ? $data["province"] : "N/A";
      $organization = $data["organization"] ? $data["organization"] : null;
      $mailingLists->addToNewsletter($email, $lang, $name, $firstName, $lastName, $province, null, $organization);
    }

    $submittedPath = Utils::lang() == "fr" ? "infolettre/soumis" : "newsletter/submitted";
    $submittedPath .= "?n=$nid";
    $submittedPath .= "&s=$subStatus";
    $this->goto($submittedPath);
  }

}
