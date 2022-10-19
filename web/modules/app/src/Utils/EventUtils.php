<?php

namespace Drupal\app\Utils;

use Drupal\app\Models\EventOptionEntity;
use Drupal\app\Models\ICS;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Spatie\CalendarLinks\Link;

class EventUtils {

  public static function getEventOptions(\Drupal\node\Entity\Node $node) {
    $tids = [];
    foreach($node->get("field_event_options")->getValue() as $n) {
      $tids[] = $n['target_id'];
    }

    $options = Node::loadMultiple($tids);
    $eventOptions = [];
    foreach($options as $option) {
      $closed = (bool)$option->get('field_event_option_closed')->getValue()[0]['value'];
      if($closed) continue;
      $eventOptions[] = new EventOptionEntity($option, $node);
    }
    return EventUtils::getEventOptionsOrderedByDate($eventOptions);
  }

  public static function getEventOptionsOrderedByDate($eventOptions) {
    /** @var $eventOptions \Drupal\App\Models\EventOptionEntity[] */
    $cmpDatesStart = function($a, $b) {
      $a = strtotime($a->d);
      $b = strtotime($b->d);
      if ($a == $b) {
        return 0;
      }
      return ($a < $b) ? -1 : 1;
    };
    $cmpDatesEnd = function($a, $b) {
      $a = strtotime($a->endD);
      $b = strtotime($b->endD);
      if ($a == $b) {
        return 0;
      }
      return ($a < $b) ? -1 : 1;
    };
    $orderedByStart = $eventOptions;
    $orderedByEnd = $eventOptions;
    usort($orderedByStart, $cmpDatesStart);
    usort($orderedByEnd, $cmpDatesEnd);
    return [
      "orderedByStart" => $orderedByStart,
      "orderedByEnd"   => $orderedByEnd
    ];
  }

  public static function getAvailableEventOptions($eventOptions) {
    /** @var $eventOptions \Drupal\App\Models\EventOptionEntity[] */
    $availableEventOptions = [];
    foreach($eventOptions as $option) {
      if($option->isActive) $availableEventOptions[] = $option;
    }
    return $availableEventOptions;
  }

  public static function groupEventOptionsByMonth($eventOptions) {
    /** @var $eventOptions \Drupal\App\Models\EventOptionEntity[] */
    $eventOptionsByMonth = [];
    $currentMonth = "";
    $currentYear = "";
    $group = [];

    foreach($eventOptions as $option) {
      $ts = strtotime($option->d);
      $date = getdate($ts);
      $month = $date['mon'];
      $year = $date['year'];

      if(!($month == $currentMonth && $year == $currentYear)) {
        $currentMonth = $month;
        $currentYear = $year;
        if(count($group)) $eventOptionsByMonth[] = $group;
        $group = [
          "month"   => $month,
          "year"    => $year,
          "label"   => DateUtils::localizeMonth($month) . " $year",
          "options" => []
        ];
      }

      $group["options"][] = $option;
    }

    if(count($group)) $eventOptionsByMonth[] = $group;

    return $eventOptionsByMonth;
  }

  public static function groupEventOptionsByTraining($eventOptions) {
    /** @var $eventOptions \Drupal\App\Models\EventOptionEntity[] */
    $eventOptionsByTraining = [
      "group"      => [],
      "one-on-one" => []
    ];
    $trainingGroup = [];
    $trainingOneOnOne = [];

    foreach($eventOptions as $option) {
      $type = $option->trainingType;
      if($type == "group") $trainingGroup[] = $option;
      if($type == "one-on-one") $trainingOneOnOne[] = $option;
    }

    $eventOptionsByTraining["group"] = EventUtils::groupEventOptionsByMonth($trainingGroup);
    $eventOptionsByTraining["one-on-one"] = EventUtils::groupEventOptionsByMonth($trainingOneOnOne);

    return $eventOptionsByTraining;
  }

  public static function getEventDateRangeString($orderedEventOptions, $event) {
    /** @var $orderedByStart \Drupal\App\Models\EventOptionEntity[] */
    /** @var $orderedByEnd \Drupal\App\Models\EventOptionEntity[] */
    /** @var $event \Drupal\node\Entity\Node */
    $orderedByStart = $orderedEventOptions["orderedByStart"];
    $orderedByEnd = $orderedEventOptions["orderedByEnd"];
    if(!count($orderedByStart)) {
      $label = $event->get('field_coming_soon_label')->getValue()[0]['value'];
      if($label) {
        return $label;
      } else {
        return t("Coming Soon");
      }
    }
    $startDate = $orderedByStart[0]->d;
    $endDate = $orderedByEnd[count($orderedByEnd) - 1]->endD;
    return DateUtils::localizeDateRange($startDate, $endDate);
  }

  public static function getEventDateTimeRangeString($orderedEventOptions, $event) {
    /** @var $orderedByStart \Drupal\App\Models\EventOptionEntity[] */
    /** @var $orderedByEnd \Drupal\App\Models\EventOptionEntity[] */
    /** @var $event \Drupal\node\Entity\Node */
    $orderedByStart = $orderedEventOptions["orderedByStart"];
    $orderedByEnd = $orderedEventOptions["orderedByEnd"];
    if(!count($orderedByStart)) {
      $label = $event->get('field_coming_soon_label')->getValue()[0]['value'];
      if($label) {
        return $label;
      } else {
        return t("Coming Soon");
      }
    }
    $startDate = $orderedByStart[0]->d;
    $endDate = $orderedByEnd[count($orderedByEnd) - 1]->endD;
    return DateUtils::localizeDateTimeRange($startDate, $endDate);;
  }

  public static function getEventDateTimeRangeWithTimezone($orderedEventOptions, $event) {
    /** @var $orderedByStart \Drupal\App\Models\EventOptionEntity[] */
    /** @var $orderedByEnd \Drupal\App\Models\EventOptionEntity[] */
    $orderedByStart = $orderedEventOptions["orderedByStart"];
    $orderedByEnd = $orderedEventOptions["orderedByEnd"];
    $dateTimeRange = EventUtils::getEventDateTimeRangeString($orderedEventOptions, $event);
    $timezone = EventUtils::getTimezoneString($orderedByStart[0]);
    return [
      "dateTimeRange" => $dateTimeRange,
      "timezone"      => $timezone
    ];
  }

  public static function getTimezoneString($eventOption) {
    if(!$eventOption) return "";
    $timezone = $eventOption->timezone;
    return DateUtils::getUTCTimezoneOffsetString($timezone);
  }

  public static function getEventDetailsString($eventOptions) {
    if(!count($eventOptions)) return "";
    $string = count($eventOptions) > 1 ? t("Multiple event details") : $eventOptions[0]->name;
    return $string;
  }

  public static function getCommonCalendarData(\Drupal\app\Models\EventOptionEntity $eventOption, \Drupal\node\Entity\Node $event) {
    $startDate = $eventOption->d;
    $endDate = $eventOption->endD;

    $startD = new \DateTime();
    $startTs = DateUtils::dateToUTCTimezoneTS($startDate);
    $startD->setTimestamp($startTs);

    $endD = new \DateTime();
    $endTs = DateUtils::dateToUTCTimezoneTS($endDate);
    $endD->setTimestamp($endTs);

    $configDefaultTimezone = \Drupal::config('system.date')->get('timezone.default');
    $appServerTimezone = DateUtils::getPhpToAppTimezone($configDefaultTimezone);
    $offsetSeconds = DateUtils::getTimezoneOffset($eventOption->timezone, $appServerTimezone);
    $offsetInterval = new \DateInterval("PT" . abs($offsetSeconds) . "S");

    if($offsetSeconds < 0) {
      $startD->sub($offsetInterval);
      $endD->sub($offsetInterval);
    } else {
      $startD->add($offsetInterval);
      $endD->add($offsetInterval);
    }

    $title = $event->getTitle();
    $location = "";
    $description = $eventOption->name;
    $description = $description . "\n\n";
    $description = $description . $event->get('field_teaser_summary')->getValue()[0]['value'];
    if($eventOption->link) $description = $description . "\n\n" . $eventOption->link;
    return [
      "title"       => $title,
      "startD"      => $startD,
      "endD"        => $endD,
      "description" => $description,
      "location"    => $location
    ];
  }

  public static function getEventCalendarLinks(\Drupal\app\Models\EventOptionEntity $eventOption, \Drupal\node\Entity\Node $event) {
    $calendarData = EventUtils::getCommonCalendarData($eventOption, $event);
    return Link::create($calendarData['title'], $calendarData['startD'], $calendarData['endD'])
               ->description($calendarData['description'])
               ->address($calendarData['location']);
  }

  public static function ICS(\Drupal\app\Models\EventOptionEntity $eventOption, \Drupal\node\Entity\Node $event) {
    $calendarData = EventUtils::getCommonCalendarData($eventOption, $event);
    $ics = new ICS($calendarData['startD'], $calendarData['endD'], $calendarData['title'], $calendarData['description'], $calendarData['location']);
    $ics->show();
  }

  public static function notificationsUnregisteredEventOptions() {
    $events = EventUtils::notificationsCloseUnregisteredOptions();
    EventUtils::notificationNotifyUnregisteredOptions($events);
    exit;
  }

  public static function notificationsCloseUnregisteredOptions(): array {
    /* @var $option EventOptionEntity */
    /* @var $nowDateTime \DateTimeImmutable */
    /* @var $cutoffDateTime \DateTimeImmutable */
    /* @var $query \Drupal\Core\Entity\Query\Sql\Query */

    $now = DateUtils::dateToUTCTimezone('now');
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'event');
    $query->condition('status', 1);
    $query->condition('field_notify_unregistered_events', 1);
    $query->condition('field_event_options.entity:node.field_event_date_time_range.value', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=');
    $query->condition('field_event_options.entity:node.field_event_option_closed.value', 0);

    $nids = $query->execute();
    $events = Node::loadMultiple($nids);

    $eventsWithNotifications = [];
    foreach($events as $event) {

      $offsetHours = $event->get('field_event_options_cutoff')->getValue()[0]['value'] ?: 48;
      $offsetMinutes = (int)($offsetHours * 60);
      $orderedOptions = EventUtils::getEventOptions($event);
      $options = $orderedOptions["orderedByStart"];

      $nowDateTime = DateUtils::dateToUTCTimezone('now', true);
      $nowDrupalFormat = $nowDateTime->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
      $nowTs = strtotime($nowDrupalFormat);

      $cutoffDateTime = $nowDateTime->add(new \DateInterval("PT". $offsetMinutes ."M"));
      $cutoffDrupalFormat = $cutoffDateTime->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
      $cutoffTs = strtotime($cutoffDrupalFormat);

      $activeOptions = [];
      foreach($options as $option) {
        $endTs = strtotime($option->endD);
        if($nowTs <= $endTs) $activeOptions[] = $option;
      }

      $consideredOptions = [];
      foreach($activeOptions as $option) {
        $startTs = strtotime($option->d);
        if($startTs <= $cutoffTs) $consideredOptions[] = $option;
      }

      $notificationOptions = [];
      foreach($consideredOptions as $option) {
        /* @var $query \Drupal\Core\Entity\Query\Sql\Query */
        $query = \Drupal::entityQuery('node');
        $query->condition('type', 'submission_event');
        $query->condition('field_submission_event_option', $option->id, '=');
        $submissions = $query->execute();
        if(!count($submissions)) {
          $optionNode = Node::load($option->id);
          $optionNode->set('field_event_option_closed', '1');
          $optionNode->save();

          $notificationOptions[] = [
            'option'  => $option,
            'date'    => [
              'en'  => [
                'date'      => DateUtils::localizeDateRange($option->d, $option->endD, 'en', true),
                'timezone'  => DateUtils::getUTCTimezoneOffsetString($option->timezone, 'en')
              ],
              'fr'  => [
                'date'      => DateUtils::localizeDateRange($option->d, $option->endD, 'fr', true),
                'timezone'  => DateUtils::getUTCTimezoneOffsetString($option->timezone, 'fr')
              ]
            ]
          ];
        }
      }

      if(count($notificationOptions)) {
        $eventsWithNotifications[] = [
          'event'   => $event,
          'offset'  => EventUtils::notificationsFormatOffset($offsetHours),
          'options' => $notificationOptions
        ];
      }

    }

    return $eventsWithNotifications;
  }

  public static function notificationNotifyUnregisteredOptions($events) {
    /* @var $mailManager \Drupal\Core\Mail\MailManager */
    $mailManager = \Drupal::service('plugin.manager.mail');

    foreach($events as $event) {
      $userIds = [];
      foreach($event['event']->get('field_notify_event_users')->getValue() as $user) {
        $userIds[] = $user["target_id"];
      }
      /* @var $emailRecipient \Drupal\user\Entity */
      $users = User::loadMultiple($userIds);
      foreach($users as $user) {
        $userName              = $user->get("name")->getValue()[0]["value"];
        $userEmail             = $user->get("mail")->getValue()[0]["value"];
        $userPreferredLanguage = $user->get("preferred_langcode")
                                      ->getValue()[0]["value"];
        $recipient = [
          "userName"              => $userName,
          "userEmail"             => $userEmail,
          "userPreferredLanguage" => $userPreferredLanguage
        ];

        $emailSubjectTitle = t("Event Registration Notification", [], ["langcode" => $recipient["userPreferredLanguage"]]);
        $now               = DateUtils::localizeDateTime('now', $recipient["userPreferredLanguage"]);
        $emailSubject      = "$emailSubjectTitle - $now";

        /* @var $localizedEvent \Drupal\node\Entity\Node */
        $localizedEvent = $event['event'];
        if($localizedEvent->hasTranslation($recipient["userPreferredLanguage"])) {
          $localizedEvent = $localizedEvent->getTranslation($recipient["userPreferredLanguage"]);
        }

        $mailManager->mail("app", "notify_unregistered_event_options", $recipient["userEmail"], $recipient["userPreferredLanguage"], [
          "lang" => $recipient["userPreferredLanguage"],
          "name" => $recipient["userName"],
          "subject" => $emailSubject,
          "v" => [
            "eventTitle"  => $localizedEvent->getTitle(),
            "options"     => $event['options'],
            "offset"      => $event['offset'],
            "title"       => $emailSubjectTitle
          ]
        ]);

      }
    }
  }

  public static function notificationsFormatOffset($offsetHours): string {
    $hours = (int)(floor($offsetHours));
    $minutes = (int)(round(($offsetHours - $hours) * 60));
    $string = "";
    if($hours) $string .= $hours . "h";
    if($minutes) $string .= $minutes . "m";
    return $string;
  }


}
