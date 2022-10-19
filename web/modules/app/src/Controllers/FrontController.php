<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Utils\ParagraphUtils;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\FrontView;

class FrontController extends BaseController {

  public static $type = "/^front$/";

  /** @var $v FrontView */
  private $v;

  public function preprocess_html(&$vars) {
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new FrontView();
    $this->v->banner = new BannerView($node);
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }
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
