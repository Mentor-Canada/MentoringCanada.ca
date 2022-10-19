<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Views\SocialMediaToolkitView;

class SocialMediaToolkitController extends BaseController {

  public static $type = "/^social_media_toolkit$/";

  /** @var $v SocialMediaToolkitView() */
  private $v;

  public function preprocess_html(&$vars) {
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new SocialMediaToolkitView($node);
  }

  public function preprocess_page(&$vars) {
    $vars['v'] = $this->v;
  }

}
