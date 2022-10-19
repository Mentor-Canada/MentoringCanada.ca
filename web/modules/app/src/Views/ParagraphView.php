<?php

namespace Drupal\app\Views {

  class ParagraphView {

    public $theme;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
      $this->theme = $theme;
    }

  }

}
