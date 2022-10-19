<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Utils\ReferenceUtils;
use Drupal\app\Utils\Utils;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\PageBasicView;
use Drupal\app\Utils\ParagraphUtils;

class PageBasicController extends BaseController {

  public static $type = "/^basic$/";

  /** @var $v PageBasicView */
  private $v;

  public function preprocess_html(&$vars) {
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new PageBasicView();
    $this->v->banner = new BannerView($node);
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }

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
    $this->v->children = ReferenceUtils::getPages(Utils::getChildrenByNid($node->id()));
    $this->v->title = $node->getTitle();

    $vars['v'] = $this->v;
  }

}
