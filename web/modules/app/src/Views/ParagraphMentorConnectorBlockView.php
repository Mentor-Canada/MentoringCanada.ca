<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\Utils;

  class ParagraphMentorConnectorBlockView extends ParagraphBlockView {

    public $lang;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_mentor_connector_block";
      parent::__construct($p, $node, $theme);

      $this->lang = Utils::lang();
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
