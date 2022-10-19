<?php

namespace Drupal\app\Views {

  class ParagraphDividerBlockView extends ParagraphBlockView {

    public $paddingTop;
    public $paddingBottom;
    public $paddingTopPixels;
    public $paddingBottomPixels;
    public $divider;
    public $dividerThicknessMinimal;
    public $dividerThicknessVibrant;
    public $dividerThicknessPixels;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_divider_block";
      parent::__construct($p, $node, $theme);

      $this->paddingTop = $p->get("field_padding_top")->getValue()[0]['value'];
      $this->paddingBottom = $p->get("field_padding_bottom")->getValue()[0]['value'];
      $this->paddingTopPixels = $p->get("field_padding_top_pixels")->getValue()[0]['value'];
      $this->paddingBottomPixels = $p->get("field_padding_bottom_pixels")->getValue()[0]['value'];
      $this->divider = $p->get("field_divider")->getValue()[0]['value'];
      $this->dividerThicknessMinimal = $p->get("field_divider_thickness_minimal")->getValue()[0]['value'];
      $this->dividerThicknessVibrant = $p->get("field_divider_thickness_vibrant")->getValue()[0]['value'];
      $this->dividerThicknessPixels = $p->get("field_divider_thickness_pixels")->getValue()[0]['value'];

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
