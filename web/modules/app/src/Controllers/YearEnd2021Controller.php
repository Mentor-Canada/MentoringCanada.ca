<?php

namespace Drupal\app\Controllers;

use Drupal;
use Drupal\app\Views\YearEnd2021View;

class YearEnd2021Controller extends BaseController {

  public static $path = "/^\/(?:2021-year-end-report|rapport-etape-de-2021)$/";

  /** @var $v YearEnd2021View */
  protected $v;

  public function theme_suggestions_page_alter(&$suggestions, $vars) {
    $suggestions = ["page__year_end_2021"];
  }

  public function preprocess_html(&$vars) {
    /** @var $htmlView \Drupal\app\Views\HtmlView */
    $htmlView = $vars['v'];
    $htmlView->footer->display = false;
    parent::preprocess_html($vars);
    $this->v = new YearEnd2021View();
    $vars["classes"] .= " page-year-end-2021";
  }

  public function preprocess_page(&$vars) {

    global $base_url;

    $node = $vars['node'];
    $content = $node->get('body')->getValue();

    $bodies = [];
    foreach($content as $body) {
      $bodies[] = $body['value'];
    }

    $intro = $bodies[0];
    $intro = str_replace('<h6 class="', '<h1 class="visually-h6 ', $intro);
    $intro = str_replace('</h6>', '</h1>', $intro);
    $this->v->intro = $intro;

    $ids = [
      'mentor-conector',
      'online-mentor-orientation',
      'e-mentoring',
      'truth-and-reconciliation',
      'power-of-mentoring',
      'pride',
      'technical-assistance',
      'mentoring-month',
      'state-of-mentoring',
      'data-dashboards'
    ];
    $cards = [];
    foreach($ids as $key=>$id) {
      $front = $bodies[$key * 2 + 1];
      $back = $bodies[$key * 2 + 2];
      $card = [
        'id'    => $id,
        'front' => $front,
        'back'  => $back
      ];
      $cards[] = $card;
    }

    $this->v->cards = $cards;

    $this->v->assetsUrl = "$base_url/assets/reporting-inspiration";


    $vars['v'] = $this->v;
  }


}
