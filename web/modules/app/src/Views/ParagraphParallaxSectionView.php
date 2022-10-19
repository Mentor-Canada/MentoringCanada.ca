<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ParagraphUtils;
  use Drupal\app\Utils\Utils;
  use Drupal\image\Entity\ImageStyle;
  use Drupal\paragraphs\Entity\Paragraph;

  class ParagraphParallaxSectionView extends ParagraphSectionView {

    public $eyebrow;
    public $title;
    public $content;
    public $darkenImage;
    public $image;
    public $dynamicContentType;
    public $menu;
    public $references;
    public $sectionWidth;
    public $noResults;
    public $sectionHeightValue;
    public $sectionHeightUnits;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_parallax_section";
      parent::__construct($p, $node, $theme);

      $lang = Utils::lang();

      $this->eyebrow = $p->get("field_section_eyebrow")->getValue()[0]["value"];
      $this->title = $p->get("field_section_title")->getValue()[0]["value"];
      $this->content = $p->get("field_wysiwyg_2")->getValue()[0]["value"];
      $this->darkenImage = $p->get("field_darken_image")->getValue()[0]["value"];

      /* @var $imageEntities \Drupal\Core\Field\EntityReferenceFieldItemList */
      $imageEntities = $p->get("field_image")->referencedEntities();
      /* @var $imageEntity \Drupal\media\Entity\Media */
      $imageEntity = $imageEntities[0];
      /* @var $imageFile \Drupal\file\Entity\File */
      if($imageEntity) {
        $imageFile = $imageEntity->get("field_media_image")->entity;
        $fileUri = $imageFile->getFileUri();
        $imageUrl = ImageStyle::load('banner_1920x1280')->buildUrl($fileUri);
      }
      $this->image = $imageUrl;

      $this->sectionWidth = $p->get("field_width_options_wide")->getValue()[0]["value"];

      $this->noResults = $p->get("field_wysiwyg")->getValue()[0]["value"];

      $this->sectionHeightValue = $p->get("field_section_height_value")->getValue()[0]["value"];
      $this->sectionHeightUnits = $p->get("field_section_height_units")->getValue()[0]["value"];

      $this->dynamicContentType = $p->get("field_display_dynamic_content")->getValue()[0]["value"];

      if($this->dynamicContentType == "menu") {
        $menu = [];
        $sections = $node->get("field_sections")->getValue();
        foreach($sections as $section) {
          $target_id = $section["target_id"];

          /** @var $s \Drupal\paragraphs\Entity\Paragraph */
          $s = Paragraph::load($target_id);
          if($s->hasTranslation($lang)) $s = $s->getTranslation($lang);

          if($s->hasField("field_display_in_menus")) {
            $displayInMenus = $s->get("field_display_in_menus")->getValue()[0]["value"];
            if($displayInMenus) {
              $menuTitle = $s->get("field_section_menu_title")->getValue()[0]["value"];
              if($menuTitle) {
                $item = [
                  "id" => $s->id(),
                  "title" => $menuTitle
                ];
                $menu[] = $item;
              }
            }
          }
        }
        $this->menu = $menu;
      }

      if($this->dynamicContentType == "references") {
        $this->references = ParagraphUtils::getReferenceItemsFromParagraph($p);
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
