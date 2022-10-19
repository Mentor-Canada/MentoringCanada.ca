<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ParagraphUtils;

  class ParagraphContentSectionView extends ParagraphSectionView {

    public $blocks;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_content_section";
      parent::__construct($p, $node, $theme);

      $blockValues = $p->get("field_section_blocks")->getValue();
      $blocks = [];
      foreach($blockValues as $block) {
        $target_id = $block["target_id"];
        $p = ParagraphUtils::getParagraph($target_id, $node);
        $blocks[] = $p;
      }
      $this->blocks = $blocks;

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
