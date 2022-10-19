<?php

namespace Drupal\app\Views {

  use Drupal\image\Entity\ImageStyle;

  class PageTeaserView {

    public $url;
    public $title;
    public $teaserSummary;
    public $teaserImage;

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->url = $node->toUrl()->toString();
      $this->title = $node->getTitle();
      $this->teaserSummary = $node->get("field_teaser_summary")->getValue()[0]['value'];

      /* @var $mediaEntity \Drupal\media\Entity\Media */
      $mediaEntity = $node->get("field_teaser_image")->entity;
      if($mediaEntity) {
        $imageEntity = $mediaEntity->get("field_media_image")->entity;
        $fileUri = $imageEntity->getFileUri();
        $imageUrl = ImageStyle::load('teaser_330x220')->buildUrl($fileUri);
      }

      if(!$imageUrl) {
        /* @var $mediaEntity \Drupal\media\Entity\Media */
        $mediaEntity = $node->get("field_banner_image")->entity;
        if($mediaEntity) {
          $imageEntity = $mediaEntity->get("field_media_image")->entity;
          $fileUri = $imageEntity->getFileUri();
          $imageUrl = ImageStyle::load('teaser_330x220')->buildUrl($fileUri);
        }
      }

      $this->teaserImage = $imageUrl;

    }

    public function __toString() {
      $vars = [
        '#theme' => "page_teaser_more",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
