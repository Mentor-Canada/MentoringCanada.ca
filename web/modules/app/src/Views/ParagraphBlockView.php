<?php

namespace Drupal\app\Views {

  class ParagraphBlockView extends ParagraphView {

    public $blockWidth;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
      parent::__construct($p, $node, $theme);
    }

  }

}
