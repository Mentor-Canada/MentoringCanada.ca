<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Views\CustomView;
use Drupal\app\Views\EmploymentOpportunitiesView;

class EmploymentOpportunitiesController extends BaseController {

  public static $template = "/^employment-opportunities$/";

  /** @var $v CustomView */
  private $v;

  public function theme_suggestions_page_alter( &$suggestions, $vars ) {
    $suggestions = ["page__template__employment_opportunities"];
  }

  public function preprocess_html(&$vars) {
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new CustomView($node);
    $this->v->extendedView = new EmploymentOpportunitiesView($node);
    $vars["classes"] .= " page-template--employment-opportunities";
  }

  public function preprocess_page(&$vars) {
    $vars['v'] = $this->v;
  }

}
