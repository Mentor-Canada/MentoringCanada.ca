<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Views\SocialMediaCampaignView;

class SocialMediaCampaignController extends BaseController {

  public static $type = "/^social_media_campaign$/";

  /** @var $v SocialMediaCampaignView() */
  private $v;

  public function preprocess_html(&$vars) {
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new SocialMediaCampaignView($node);
  }

  public function preprocess_page(&$vars) {
    $vars['v'] = $this->v;
  }

}
