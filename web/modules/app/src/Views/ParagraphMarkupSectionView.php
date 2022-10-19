<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ParagraphUtils;

  class ParagraphMarkupSectionView extends ParagraphSectionView {

    public $markup;
    public $ignoreDefaultPaddingTop;
    public $ignoreDefaultPaddingBottom;
    public $ignoreDefaultZebraStriping;
    public $sectionWidth;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_markup_section";
      parent::__construct($p, $node, $theme);

      $this->markup = $p->get("field_markup")->getValue()[0]['value'];
      $this->ignoreDefaultPaddingTop = $p->get("field_ignore_padding_top")->getValue()[0]['value'] ? 'true' : 'false';
      $this->ignoreDefaultPaddingBottom = $p->get("field_ignore_padding_bottom")->getValue()[0]['value'] ? 'true' : 'false';;
      $this->ignoreDefaultZebraStriping = $p->get("field_ignore_zebra_striping")->getValue()[0]['value'];
      $this->sectionWidth = $p->get("field_width_options_full_bleed")->getValue()[0]["value"];

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
