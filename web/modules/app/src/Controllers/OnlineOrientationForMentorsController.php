<?php

namespace Drupal\app\Controllers;

use Drupal;
use Drupal\app\Utils\ParagraphUtils;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\OnlineOrientationForMentorsView;
use Drupal\app\Utils\NodeUtils;

class OnlineOrientationForMentorsController extends BaseController {

  public static $path = "/^\/(?:online-orientation-mentors|orientation-en-ligne-pour-les-mentors)$/";

  /** @var $v OnlineOrientationForMentorsView */
  protected $v;

  public function theme_suggestions_page_alter(&$suggestions, $vars) {
    $suggestions = ["page__online_orientation_for_mentors"];
  }

  public function preprocess_html(&$vars) {
    $this->v = new OnlineOrientationForMentorsView();

    $node = NodeUtils::getNodeFromUrl();
    $this->v->banner = new BannerView($node);
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }

    $vars["classes"] .= " page-online-orientation-for-mentors";
  }

  public function preprocess_page(&$vars) {
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
    $this->v->banner->width = ParagraphUtils::getSectionAttributes($sections, "first")["width"];

    $vars['v'] = $this->v;
  }

}
