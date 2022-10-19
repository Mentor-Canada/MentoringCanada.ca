<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\DateUtils;
  use Drupal\app\Utils\EventUtils;
  use Drupal\app\Utils\ReferenceUtils;
  use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
  use Drupal\app\Views\EventFormView;

  class EventView {

    public $banner;
    public $header;

    public $sections;
    public $moreItems;
    public $form;

    public $activeEvent;
    public $postEventContent;

    public $eventWhereWhenWidth;
    public $formZebra;

    public $eventOptions;
    public $eventOptionsAvailable;
    public $eventOptionsGroupedMonthly;
    public $eventOptionsGroupedByTraining;
    public $hasEventOptions;

    public $bannerAndHeaderDate;
    public $pageSectionDate;
    public $pageSectionDetails;

    public $eventType;

    public function __construct(\Drupal\node\Entity\Node $node) {

      $typesTermLookup = [
        "1" =>  "event",
        "2" =>  "event_pom",
        "3" =>  "event_training"
      ];
      $this->eventType = $typesTermLookup[$node->get('field_event_type')->getValue()[0]['target_id']];

      // GET OPTIONS
      $orderedEventOptions = EventUtils::getEventOptions($node);
      $eventOptions = $orderedEventOptions["orderedByStart"];
      $this->eventOptions = $eventOptions;
      $this->eventOptionsAvailable = EventUtils::getAvailableEventOptions($this->eventOptions);
      $this->eventOptionsGroupedMonthly = EventUtils::groupEventOptionsByMonth($this->eventOptionsAvailable);
      $this->eventOptionsGroupedByTraining = EventUtils::groupEventOptionsByTraining($this->eventOptionsAvailable);
      $this->hasEventOptions = (bool)count($eventOptions);


      // BANNER, PAGE HEADER, AND PAGE WHEN/WHERE SECTION DATE
      $this->bannerAndHeaderDate = EventUtils::getEventDateRangeString($orderedEventOptions, $node);
      $this->pageSectionDate = EventUtils::getEventDateTimeRangeWithTimezone($orderedEventOptions, $node);
      $this->pageSectionDetails = EventUtils::getEventDetailsString($eventOptions);


      // ACTIVE EVENT
      $this->activeEvent = (bool)count($this->eventOptionsAvailable);


      // POST EVENT CONTENT
      if(!$this->activeEvent && $this->hasEventOptions) {
        $postEventContent = $node->get("field_post_event_page_content")->getValue()[0]["value"];
        $this->postEventContent = $postEventContent;
      }


      // FORM
      $hasForm = $node->get("field_has_event_form")->getValue()[0]["value"];
      if($hasForm) $this->form = new EventFormView($node, $eventOptions);


      // MORE EVENTS
      $moreItems = ReferenceUtils::getEvents([
        "nidToExclude"  => $node->id(),
        "limit"         => 5,
        "imageStyle"    => "teaser_330x220"
      ]);
      $teasers = [];
      foreach($moreItems as $item) {
        $vars = [
          '#theme' => "event_teaser_more",
          '#v' => $item
        ];
        $teaser = strval(render($vars));
        $teasers[] = $teaser;
      }
      $this->moreItems = $teasers;

    }

  }

}
