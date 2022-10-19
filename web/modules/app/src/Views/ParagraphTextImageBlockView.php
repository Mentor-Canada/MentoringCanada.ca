<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ParagraphUtils;

  class ParagraphTextImageBlockView extends ParagraphBlockView {

    public $text1;
    public $text2;
    public $image1;
    public $image2;

    public $imageCount;
    public $horizontalAlignment;
    public $verticalAlignment;
    public $imagePosition;
    public $imageWidth;

    public $horizontalAlignmentAlt;
    public $textAlignmentAlt;

    public $responsiveImageToTop;
    public $hdpiImages;
    public $reducedTypography;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_text_image_block";
      parent::__construct($p, $node, $theme);

      $this->text1 = $p->get("field_wysiwyg")->getValue()[0]['value'];
      $this->text2 = $p->get("field_wysiwyg_2")->getValue()[0]['value'];
      $this->image1 = ParagraphUtils::getImageDataFromFieldname($p, "field_image");
      $this->image2 = ParagraphUtils::getImageDataFromFieldname($p, "field_image_2");

      $this->imageCount = str_replace("images-", "", $p->get("field_number_of_images")->getValue()[0]["value"]);
      $this->imageWidth = str_replace("/", "-", $p->get("field_image_width")->getValue()[0]["value"]);
      $this->imagePosition = $p->get("field_image_position")->getValue()[0]["value"];

      $this->horizontalAlignment = $p->get("field_horizontal_alignment")->getValue()[0]["value"];
      $this->verticalAlignment = $p->get("field_vertical_alignment")->getValue()[0]["value"];

      $this->horizontalAlignmentAlt = $p->get("field_horizontal_alignment_alt")->getValue()[0]["value"];
      $this->textAlignmentAlt = $p->get("field_text_alignment_alt")->getValue()[0]["value"];

      $this->responsiveImageToTop = $p->get("field_responsive_image_to_top")->getValue()[0]["value"] ? "top" : "default";
      $this->hdpiImages = $p->get("field_hdpi_images")->getValue()[0]["value"] ? true : false;
      $this->reducedTypography = $p->get("field_reduced_typography")->getValue()[0]["value"] ? true : false;

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
