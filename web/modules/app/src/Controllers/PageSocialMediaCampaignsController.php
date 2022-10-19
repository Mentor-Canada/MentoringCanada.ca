<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Views\PageSocialMediaCampaignsView;

class PageSocialMediaCampaignsController extends BaseController {

  public static $path = "/^\/(?:social-media-campaigns|campagnes-medias-sociaux)$/";

  /** @var $v PageSocialMediaCampaignsView() */
  private $v;

  public function theme_suggestions_page_alter(&$suggestions, $vars) {
    $suggestions = ["page__social_media_campaigns"];
  }

  public function preprocess_html(&$vars) {
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new PageSocialMediaCampaignsView($node);
    $vars["classes"] .= " page-social-media-campaigns";
  }

  public function preprocess_page(&$vars) {
    $vars['v'] = $this->v;
  }

}
