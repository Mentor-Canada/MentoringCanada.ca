<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\Utils;

  class FooterView {

    public $baseUrl;
    public $lang;
    public $gs;
    public $menu;

    public $display = true;

    public $footerMenu;
    public $partnerLogos;

    public function __construct($vars) {

      $this->baseUrl = $vars['baseUrl'];
      $this->lang = $vars['lang'];
      $this->gs = $vars['gs'];
      $this->menu = $vars['menu'];

      /* @var $node \Drupal\node\Entity\Node */
      $node = \Drupal::routeMatch()->getParameter('node');
      if($node) {
        if($node->hasField('field_hidden_footer')) {
          $this->display = $node->get('field_hidden_footer')->getValue()[0]['value'] ? false : true;
        }
      }

      $this->footerMenu = Utils::getMenuItems("footer");

      $this->partnerLogos = [
        "partner" => [
          [
            "src"     => $this->lang == "fr" ? "$this->baseUrl/assets/partner-logos/bbbsc-fr-logo.png" : "$this->baseUrl/assets/partner-logos/bbbsc-en-logo.png",
            "width"   => $this->lang == "fr" ? 241 : 211,
            "height"  => 85,
            "alt"     => $this->lang == "fr" ? "Grands Frères Grandes Soeurs du Canada" : "Big Brothers Big Sisters of Canada"
          ],
          [
            "src"     => "$this->baseUrl/assets/partner-logos/omc-logo.png",
            "width"   => 232,
            "height"  => 113,
            "alt"     => "Ontario Mentoring Coalition"
          ],
          [
            "src"     => "$this->baseUrl/assets/partner-logos/amp-logo.png",
            "width"   => 238,
            "height"  => 114,
            "alt"     => "Alberta Mentoring Partnership"
          ],
        ],
        "funder" => [
          [
            "src"     => "$this->baseUrl/assets/partner-logos/YESS-funding-recognition-and-Wordmark_Ca-$this->lang.png",
            "width"   => $this->lang == "fr" ? 450 : 522,
            "height"  => 80,
            "alt"     => $this->lang == "fr" ? "Financé par le Gouvernement du Canada par le programme de Stratégie d'emploi et compétence jeunesse" : "Funder by the Government of Canada's Youth Employment Skills Strategy"
          ],
          [
            "src"     => "$this->baseUrl/assets/partner-logos/BMO_TK_E_White.png",
            "width"   => 216,
            "height"  => 80,
            "alt"     => $this->lang == "fr" ? "BMO Banque de Montréal" : "BMO Bank of Montreal"
          ],
        ]
      ];


    }

    public function __toString() {
      $vars = [
        '#theme' => "footer",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
