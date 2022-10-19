<?php

namespace Drupal\app\Views {

  class ParagraphWysiwygBlockView extends ParagraphBlockView {

    public $column1;
    public $column2;
    public $columnCount;
    public $columnSplit;

    public $blockConstrained;
    public $blockAlignment;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_wysiwyg_block";
      parent::__construct($p, $node, $theme);

      $this->column1 = $p->get("field_wysiwyg")->getValue()[0]['value'];
      $this->column2 = $p->get("field_wysiwyg_2")->getValue()[0]['value'];
      $this->columnCount = str_replace("columns-", "", $p->get("field_number_of_columns")->getValue()[0]["value"]);
      $this->columnSplit = $p->get("field_column_split")->getValue()[0]["value"];

      $this->blockConstrained = $p->get("field_constrain_block_width")->getValue()[0]["value"] ? "true" : "false";
      $this->blockAlignment = $p->get("field_wide_block_alignment")->getValue()[0]["value"];

      $this->blockWidth = $p->get("field_width_options_wide")->getValue()[0]["value"];

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
