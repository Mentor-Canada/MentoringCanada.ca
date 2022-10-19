<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\EventUtils;
  use Drupal\image\Entity\ImageStyle;

  class EventTeaserView {

    public $url;
    public $title;
    public $date;
    public $location;
    public $teaserSummary;
    public $teaserImage;

    public $showTypeTip;
    public $typeTip;

    public function __construct(\Drupal\node\Entity\Node $node, $showTypeTip = false, $imageStyle = "teaser_960x640") {

      $orderedEventOptions = EventUtils::getEventOptions($node);
      $eventOptions = $orderedEventOptions["orderedByStart"];
      $this->url = $node->toUrl()->toString();
      $this->title = $node->getTitle();
      $this->date = EventUtils::getEventDateRangeString($orderedEventOptions, $node);
      $this->location = EventUtils::getEventDetailsString($eventOptions);
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
      $this->typeTip = t("Event");

    }

    public function __toString() {
      $vars = [
        '#theme' => "event_teaser",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
