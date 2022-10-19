<?php

namespace Drupal\app\Views {

  use Drupal\Core\Url;
  use Drupal\image\Entity\ImageStyle;

  class BannerView {

    public $title;
    public $eyebrow;
    public $heading;
    public $subheading;
    public $linkUrl;
    public $linkTitle;
    public $hasLink;
    public $image;
    public $hasImage = false;
    public $width = "standard";
    public $hideContent = false;
    public $hideScrim = false;

    public function __construct(\Drupal\node\Entity\Node $node = null) {
      if($node) {

        $this->title = $node->getTitle();

        if($node->hasField("field_banner_eyebrow")) {
          $this->eyebrow = $node->get("field_banner_eyebrow")->getValue()[0]['value'];
        }

        if($node->hasField("field_banner_heading")) {
          $this->heading = $node->get("field_banner_heading")->getValue()[0]['value'];
        }

        if($node->hasField("field_banner_subheading")) {
          $this->subheading = $node->get("field_banner_subheading")->getValue()[0]['value'];
        }

        if($node->hasField("field_banner_link")) {
          $link = $node->get("field_banner_link")->getValue()[0];
          if($link["uri"]) {
            $this->linkUrl = Url::fromUri($link["uri"])->toString();
            $this->linkTitle = $link["title"];
            $this->hasLink = true;
          }
        }

        if($node->hasField("field_banner_image")) {
          /* @var $mediaEntity \Drupal\media\Entity\Media */
          $mediaEntity = $node->get("field_banner_image")->entity;
          if($mediaEntity) {
            $imageEntity = $mediaEntity->get("field_media_image")->entity;
            $fileUri = $imageEntity->getFileUri();
            $imageUrl = ImageStyle::load('banner_1920x1280')->buildUrl($fileUri);
            $this->image = $imageUrl;
            $this->hasImage = true;
          }
        }

        if($node->hasField("field_banner_hide_content")) {
          $this->hideContent = $node->get("field_banner_hide_content")->getValue()[0]['value'] ? true : false;
        }

        if($node->hasField("field_banner_hide_scrim")) {
          $this->hideScrim = $node->get("field_banner_hide_scrim")->getValue()[0]['value'] ? true : false;
        }

      }
    }

    public function __toString() {
      $vars = [
        '#theme' => "banner",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
