<?php

namespace Drupal\app\Views {

  class ParagraphMarkupBlockView extends ParagraphBlockView {

    public $markup;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_markup_block";
      parent::__construct($p, $node, $theme);

      $this->markup = $p->get("field_markup")->getValue()[0]['value'];
      $this->blockWidth = $p->get("field_width_options_full_bleed")->getValue()[0]["value"];

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
