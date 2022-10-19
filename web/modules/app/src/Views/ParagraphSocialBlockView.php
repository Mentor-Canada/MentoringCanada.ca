<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\Utils;

  class ParagraphSocialBlockView extends ParagraphBlockView {

    public $fb = null;
    public $tw = null;
    public $li = null;
    public $ig = null;
    public $yt = null;
    public $newsletter = null;
    public $displayMinimal = null;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_social_block";
      parent::__construct($p, $node, $theme);

      $social = Utils::globalSettings()["social"];
      if($p->get("field_display_fb")->getValue()[0]["value"]) {
        $this->fb = $social["fb"];
      }
      if($p->get("field_display_tw")->getValue()[0]["value"]) {
        $this->tw = $social["tw"];
      }
      if($p->get("field_display_li")->getValue()[0]["value"]) {
        $this->li = $social["li"];
      }
      if($p->get("field_display_ig")->getValue()[0]["value"]) {
        $this->ig = $social["ig"];
      }
      if($p->get("field_display_yt")->getValue()[0]["value"]) {
        $this->yt = $social["yt"];
      }
      if($p->get("field_display_newsletter")->getValue()[0]["value"]) {
        $this->newsletter = true;
      }
      if($p->get("field_display_minimal")->getValue()[0]["value"]) {
        $this->displayMinimal = true;
      }

      $this->blockWidth = "standard";

    }

    public function __toString() {
      $vars = [
        '#theme' => $this->theme,
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
