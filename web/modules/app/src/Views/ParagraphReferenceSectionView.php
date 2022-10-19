<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ParagraphUtils;

  class ParagraphReferenceSectionView extends ParagraphSectionView {

    public $eyebrow;
    public $title;
    public $noResults;
    public $references;
    public $sectionWidth;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_reference_section";
      parent::__construct($p, $node, $theme);

      $this->eyebrow = $p->get("field_section_eyebrow")->getValue()[0]["value"];
      $this->title = $p->get("field_section_title")->getValue()[0]["value"];
      $this->noResults = $p->get("field_wysiwyg")->getValue()[0]["value"];
      $this->sectionWidth = $p->get("field_width_options_full")->getValue()[0]["value"];

      $this->references = ParagraphUtils::getReferenceItemsFromParagraph($p);

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
