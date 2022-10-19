<?php

namespace Drupal\app\Views {

  class ParagraphSectionView extends ParagraphView {

    public $id;
    public $zebra;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
      parent::__construct($p, $node, $theme);
      $this->id = $p->id();
    }

  }

}
