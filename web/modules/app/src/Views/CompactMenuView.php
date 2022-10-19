<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\Utils;

  class CompactMenuView {

    public $baseUrl;
    public $lang;
    public $gs;
    public $logo;
    public $menu;

    public $languageToggleLink;
    public $iconClose;

    public function __construct($vars) {

      $this->baseUrl = $vars['baseUrl'];
      $this->lang = $vars['lang'];
      $this->gs = $vars['gs'];
      $this->logo = $vars['logo'];
      $this->menu = $vars['menu'];

      $this->languageToggleLink = Utils::getLanguageToggleHTML(false);
      $this->iconClose = $this->theme('icon_close');

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
        '#theme' => "compact_menu",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
