<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\Utils;

  class HeaderView {

    public $baseUrl;
    public $lang;
    public $gs;
    public $logo;
    public $menu;

    public $display = true;
    public $displayLogo = true;
    public $displayMenu = true;

    public $languageToggleButton;
    public $iconHamburger;

    public function __construct($vars) {

      $this->baseUrl = $vars['baseUrl'];
      $this->lang = $vars['lang'];
      $this->gs = $vars['gs'];
      $this->logo = $vars['logo'];
      $this->menu = $vars['menu'];

      $this->languageToggleButton = Utils::getLanguageToggleHTML();
      $this->iconHamburger = $this->theme('icon_hamburger');

      /* @var $node \Drupal\node\Entity\Node */
      $node = \Drupal::routeMatch()->getParameter('node');
      if($node) {
        if($node->hasField('field_hidden_header')) {
          $this->display = $node->get('field_hidden_header')->getValue()[0]['value'] ? false : true;
        }

        if($node->hasField('field_hidden_header_logo')) {
          $this->displayLogo = $node->get('field_hidden_header_logo')->getValue()[0]['value'] ? false : true;
        }

        if($node->hasField('field_hidden_header_menu')) {
          $this->displayMenu = $node->get('field_hidden_header_menu')->getValue()[0]['value'] ? false : true;
        }
      }
    }

    protected function theme($template, $v = null) {
      $vars = [
        '#theme' => $template,
        '#v' => $v
      ];
      return render($vars);
    }

    public function __toString() {
      $vars = [
        '#theme' => "header",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
