<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ImageUtils;

  class CustomView extends PageView {

    /** @var $banner \Drupal\app\Views\BannerView */
    public $banner;
    public $bodies;
    public $images;
    public $extendedView;

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->banner = new BannerView($node);

      $bodies = [];
      $bodyValues = $node->get('body')->getValue();
      foreach($bodyValues as $body) {
        $bodies[] = $body['value'];
      }
      $this->bodies = $bodies;

      $images = [];
      /* @var $imageEntities \Drupal\Core\Field\EntityReferenceFieldItemList */
      $imageEntities = $node->get('field_image')->referencedEntities();
      /* @var $imageEntity \Drupal\media\Entity\Media */
      foreach($imageEntities as $imageEntity) {
        /* @var $imageFile \Drupal\file\Entity\File */
        if($imageEntity) {
          $imageFile = $imageEntity->get("field_media_image")->entity;
          $images[] = ImageUtils::getImage($imageFile);
        }
      }
      $this->images = $images;

    }

  }

}
