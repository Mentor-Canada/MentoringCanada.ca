<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ParagraphUtils;

  class ParagraphImagesBlockView extends ParagraphBlockView {

    public $displayMode;
    public $imageCaptionItems;
    public $hdpiImages;

    public $carouselTransition;
    public $carouselArrows;
    public $carouselArrowsPlacement;
    public $carouselArrowsPersistent;
    public $carouselDots;
    public $carouselInfinite;
    public $carouselAutoplay;
    public $carouselAutoplaySpeed;

    public $gridLayout;
    public $gridNumberPerRow;
    public $gridFlexible;
    public $gridFlexibleMinWidth;
    public $gridHorizontalAlignment;
    public $gridVerticalAlignment;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_images_block";
      parent::__construct($p, $node, $theme);

      $this->displayMode = $p->get("field_images_display_mode")->getValue()[0]["value"];

      $imageValues = $p->get("field_images")->getValue();
      $imageCaptionItems = [];
      foreach($imageValues as $image) {
        $target_id = $image["target_id"];
        $i = ParagraphUtils::getParagraph($target_id, $node, "paragraph_image_caption_". $this->displayMode . "_field");
        $imageCaptionItems[] = $i;
      }

      $this->hdpiImages = $p->get("field_hdpi_images")->getValue()[0]["value"] ? true : false;
      if($this->hdpiImages) {
        foreach($imageCaptionItems as &$imageCaptionItem) {
          $imageCaptionItem->image["width"] = $imageCaptionItem->image["width"] / 2;
          $imageCaptionItem->image["height"] = $imageCaptionItem->image["height"] / 2;
        }
      }

      $this->imageCaptionItems = $imageCaptionItems;

      if($this->displayMode == "carousel") {
        $this->blockWidth = $p->get("field_width_options_wide")->getValue()[0]["value"];
        $this->carouselTransition = $p->get("field_carousel_transition")->getValue()[0]["value"];
        $this->carouselArrows = $p->get("field_carousel_arrows")->getValue()[0]["value"] ? "true" : "false";
        $this->carouselArrowsPlacement = $p->get("field_carousel_arrows_placement")->getValue()[0]["value"];
        $this->carouselArrowsPersistent = $p->get("field_carousel_arrows_persistent")->getValue()[0]["value"] ? "true" : "false";
        $this->carouselDots = $p->get("field_carousel_dots")->getValue()[0]["value"] ? "true" : "false";
        $this->carouselInfinite = $p->get("field_carousel_infinite")->getValue()[0]["value"] ? "true" : "false";
        $this->carouselAutoplay = $p->get("field_carousel_autoplay")->getValue()[0]["value"] ? "true" : "false";
        $this->carouselAutoplaySpeed = intval(floatval($p->get("field_carousel_autoplay_speed")->getValue()[0]["value"]) * 1000);
      } else {
        $this->blockWidth = $p->get("field_width_options_full")->getValue()[0]["value"];
        $this->gridLayout = $p->get("field_grid_layout")->getValue()[0]["value"];
        $this->gridNumberPerRow = $p->get("field_grid_number_per_row")->getValue()[0]["value"];
        $this->gridFlexible = $p->get("field_grid_flexible_rows")->getValue()[0]["value"] ? "true" : "false";
        $this->gridFlexibleMinWidth = $p->get("field_grid_reflow_width_percent")->getValue()[0]["value"];
        $this->gridHorizontalAlignment = $p->get("field_horizontal_alignment")->getValue()[0]["value"];
        $this->gridVerticalAlignment = $p->get("field_vertical_alignment")->getValue()[0]["value"];
        foreach($imageCaptionItems as &$imageCaptionItem) {
          $imageCaptionItem->image["flexMinWidth"] = $imageCaptionItem->image["width"] * $this->gridFlexibleMinWidth;
        }
      }

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
