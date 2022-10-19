<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Views\MentorCityProcessMapView;

class MentorCityProcessMapController extends BaseController {

  public static $template = "/^mentorcity-advanced-set-up$/";

  /** @var $v MentorCityProcessMapView() */
  private $v;

  public function theme_suggestions_page_alter( &$suggestions, $vars ) {
    $suggestions = ["page__mentorcity_advanced_customization_process_map"];
  }

  public function preprocess_html(&$vars) {
    $node = NodeUtils::getNodeFromUrl();
    $this->v = new MentorCityProcessMapView($node);
    $vars["classes"] .= " page-mentorcity-advanced-customization-process-map";
  }

  public function preprocess_page(&$vars) {
    $vars['v'] = $this->v;
  }

}
