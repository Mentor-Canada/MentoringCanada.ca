<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\DateUtils;
  use Drupal\image\Entity\ImageStyle;

  class NewsTeaserView {

    public $url;
    public $title;
    public $date;
    public $teaserSummary;
    public $teaserImage;

    public $showTypeTip;
    public $typeTip;

    public function __construct(\Drupal\node\Entity\Node $node, $showTypeTip = false, $imageStyle = "teaser_960x640") {

      $this->url = $node->toUrl()->toString();
      $this->title = $node->getTitle();
      $this->date = DateUtils::localizeDate($node->get("created")->getValue()[0]['value']);
      $this->teaserSummary = $node->get("field_teaser_summary")->getValue()[0]['value'];

      /* @var $mediaEntity \Drupal\media\Entity\Media */
      $mediaEntity = $node->get("field_teaser_image")->entity;
      if($mediaEntity) {
        $imageEntity = $mediaEntity->get("field_media_image")->entity;
        $fileUri = $imageEntity->getFileUri();
        $imageUrl = ImageStyle::load($imageStyle)->buildUrl($fileUri);
        $this->teaserImage = $imageUrl;
      }

      $this->showTypeTip = $showTypeTip;
      $this->typeTip = t("News");

    }

    public function __toString() {
      $vars = [
        '#theme' => "news_teaser",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
