<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ParagraphUtils;

  class ParagraphReferenceBlockView extends ParagraphBlockView {

    public $noResults;
    public $references;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_reference_block";
      parent::__construct($p, $node, $theme);

      $this->noResults = $p->get("field_wysiwyg")->getValue()[0]["value"];
      $this->references = ParagraphUtils::getReferenceItemsFromParagraph($p);
      $this->blockWidth = $p->get("field_width_options_full")->getValue()[0]["value"];

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
