<?php

namespace Drupal\app\Controllers;

use Drupal;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class BaseController extends Drupal\Core\Controller\ControllerBase {

  public static function content() {
    return [];
  }

  protected function onSubmit(array $data, array $files, array &$vars) {}

  public function preprocess_html(&$vars) {
    if(count($_POST) || count($_FILES)) {
      $this->onSubmit($_POST, $_FILES, $vars);
    }
  }

  public function metatags_alter(&$vars) {}

  public function preprocess_page(&$vars) {
    $vars['messages'] = $this->getMessages();
  }

  public function theme_suggestions_page_alter(&$suggestions, $vars) {}

  public function system_breadcrumb_alter(\Drupal\Core\Breadcrumb\Breadcrumb &$breadcrumb, \Drupal\Core\Routing\RouteMatchInterface $route_match, array $context) {}

  public function form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {}

  public function preprocess_views_view(&$vars) {}

  public function page_bottom(&$vars) {}

  public function admin_preprocess_breadcrumb(&$breadcrumb) {}

  public function admin_preprocess_region(&$vars) { }

  public function goto($url) { drupal_goto($url); }

  public function getMessages() {
    $messages = drupal_get_messages();
    if(!count($messages)) {
      return;
    }
    $messages = render($el = ["#theme" => "status_messages", "#message_list" => $messages]);
    return $messages;
  }

  protected function theme($template, $v = null) {
    $vars = [
      '#theme' => $template,
      '#v' => $v
    ];
    return render($vars);
  }

}

