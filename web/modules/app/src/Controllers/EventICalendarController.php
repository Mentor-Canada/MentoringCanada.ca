<?php

namespace Drupal\app\Controllers;

use Drupal\app\Models\EventOptionEntity;
use Drupal\app\Utils\EventUtils;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventICalendarController extends BaseController {

  public static function render($eventId, $optionId) {

    $event = Node::load($eventId);
    if(!$event) {
      throw new NotFoundHttpException();
    }

    $option = Node::load($optionId);
    if(!$option) {
      throw new NotFoundHttpException();
    }

    $eventOption = new EventOptionEntity($option, $event);
    EventUtils::ICS($eventOption, $event);
  }

}
