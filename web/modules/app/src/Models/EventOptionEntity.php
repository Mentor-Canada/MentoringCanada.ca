<?php

namespace Drupal\app\Models;

use Drupal\app\Utils\DateUtils;
use Drupal\app\Utils\EventUtils;
use Drupal\Core\Url;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

class EventOptionEntity {

  public $id;
  public $d;
  public $date;
  public $dateTime;
  public $endD;
  public $endDate;
  public $endDateTime;
  public $dateRange;
  public $dateTimeRange;
  public $timezone;
  public $timezoneString;
  public $name;
  public $occupancy;
  public $link;
  public $available;
  public $isPast;
  public $isFull;
  public $isActive;
  public $calendarLinks;
  public $trainingType;
  public $keepOpenAfterStart;
  public $hideCalendarLinks;

  public function __construct(\Drupal\node\Entity\Node $node, \Drupal\node\Entity\Node $event = null) {
    $this->id = $node->id();
    $date = $node->get('field_event_date_time_range')->getValue()[0]['value'];
    $endDate = $node->get('field_event_date_time_range')->getValue()[0]['end_value'];
    $timezone = $node->get('field_event_timezone')->getValue()[0]['value'];
    $timezone .= "-Timezone";
    $this->d = $date;
    $this->date = DateUtils::localizeDate($date);
    $this->dateTime = DateUtils::localizeDateTime($date);
    $this->endD = $endDate;
    $this->endDate = DateUtils::localizeDate($endDate);
    $this->endDateTime = DateUtils::localizeDateTime($endDate);
    $this->dateRange = DateUtils::localizeDateRange($date, $endDate);
    $this->dateTimeRange = DateUtils::localizeDateRange($date, $endDate, null, true);
    $this->timezone = $timezone;
    $this->timezoneString = DateUtils::getUTCTimezoneOffsetString($timezone);
    $this->name = $node->get("field_event_name")->getValue()[0]['value'];
    $this->occupancy = $node->get("field_event_occupancy")->getValue()[0]['value'];
    $uri = $node->get("field_event_link")->getValue()[0]['uri'];
    if($uri) {
      $this->link = Url::fromUri($uri)->setAbsolute()->toString();
    } else {
      $this->link = null;
    }
    $this->trainingType = $node->get('field_one_on_one_training')->getValue()[0]['value'] ? "one-on-one" : "group";
    $this->keepOpenAfterStart = (bool)$node->get('field_keep_open_after_start')->getValue()[0]['value'];
    $this->hideCalendarLinks = (bool)$node->get('field_hide_calendar_links')->getValue()[0]['value'];

    $now = DateUtils::dateToUTCTimezone('now');
    $now = $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
    $now = strtotime($now);

    if($this->keepOpenAfterStart) {
      $this->isPast = strtotime($this->endD) < $now;
    } else {
      $this->isPast = strtotime($this->d) < $now;
    }

    $this->isFull = false;
    $this->available = null;
    if($this->occupancy) {
      /* @var $query \Drupal\Core\Entity\Query\Sql\Query */
      $query = \Drupal::entityQuery('node');
      $query->condition('type', 'submission_event');
      $query->condition('field_submission_event_option', $this->id, '=');
      $submissions = $query->execute();
      $count = count($submissions);
      $available = $this->occupancy - $count;
      if($available < 0) $available = 0;
      $this->isFull = !$available;
      $this->available = $available;
    }

    $this->isActive = !($this->isPast || $this->isFull);

    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();

    if($event) {
      $calendarLinks = EventUtils::getEventCalendarLinks($this, $event);
      $this->calendarLinks = [
        "google"  => $calendarLinks->google(),
        "outlook" => $calendarLinks->webOutlook(),
        "ics"     => "${GLOBALS['base_url']}/${lang}/events/iCalendar/{$event->id()}/{$node->id()}"
      ];
    }

  }

}
