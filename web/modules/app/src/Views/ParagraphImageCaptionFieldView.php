<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ParagraphUtils;

  class ParagraphImageCaptionFieldView extends ParagraphView {

    public $image;
    public $caption;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_image_caption_field";
      parent::__construct($p, $node, $theme);

      $this->image = ParagraphUtils::getImageDataFromFieldname($p, "field_image");
      $this->caption = $p->get("field_caption")->getValue()[0]["value"];

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
