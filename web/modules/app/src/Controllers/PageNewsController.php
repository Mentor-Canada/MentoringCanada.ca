<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Utils\Utils;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\NewsView;
use Drupal\app\Views\PageHeaderView;
use Drupal\app\Utils\ParagraphUtils;

class PageNewsController extends BaseController {

  public static $type = "/^news$/";

  /** @var $v NewsView */
  private $v;

  public function preprocess_html(&$vars) {
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new NewsView($node);

    $this->v->banner = new BannerView($node);
    $this->v->banner->eyebrow = $this->v->date;
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }

    $this->v->header = new PageHeaderView($node);
    $this->v->header->eyebrow = $this->v->date;

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
    $this->v->header->width = ParagraphUtils::getSectionAttributes($sections, "first")["width"];

    $vars['v'] = $this->v;
  }

}
