<?php

namespace Drupal\app\Controllers;

use Drupal\app\Views\SubmittedView;

class SubmittedController extends BaseController {

  protected $heading;
  protected $subheading;
  protected $paragraph;
  protected $body;
  protected $renderBodyMessage;
  protected $ytLinkUrl;

  public static function content() {
    return [];
  }

  public function theme_suggestions_page_alter(&$suggestions, $vars) {
    $suggestions = ["page__submitted"];
  }

  public function preprocess_html(&$vars) {
    parent::preprocess_html($vars);
    $vars["classes"] = "page-submitted";
  }

  public function preprocess_page(&$vars) {
    $v = new SubmittedView();

    $v->heading = $this->heading;
    $v->subheading = $this->subheading;
    $v->paragraph = $this->paragraph;
    $v->body = $this->body;
    $v->renderBodyMessage = $this->renderBodyMessage;
    $v->ytLinkUrl = $this->ytLinkUrl;

    $vars['v'] = $v;
  }

}
