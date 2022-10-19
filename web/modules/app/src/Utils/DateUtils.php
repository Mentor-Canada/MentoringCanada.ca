<?php

namespace Drupal\app\Utils;

use DateTimeZone;
use DateTime;
use DateTimeImmutable;

class DateUtils {

  public static function localizeMonth($month, $langcode = null): string {
    $lang = $langcode ?: Utils::lang();
    if($lang == "fr") {
      $monthNames = [
        "Janvier",
        "Février",
        "Mars",
        "Avril",
        "Mai",
        "Juin",
        "Juillet",
        "Août",
        "Septembre",
        "Octobre",
        "Novembre",
        "Décembre"
      ];
      return $monthNames[$month - 1];
    } else {
      $monthNames = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
      ];
      return $monthNames[$month - 1];
    }
  }

  public static function localizeDate($date, $langcode = null): string {
    $dateTime = DateUtils::dateToUTCTimezone($date);
    $lang = $langcode ?: Utils::lang();
    if($lang == "fr") {
      $currentLocale = setlocale(LC_ALL, 0);
      setlocale(LC_TIME, 'fr_CA.utf8');
      $localizedDate = strftime('%-e %B %Y', $dateTime->getTimestamp());
      setlocale(LC_TIME, $currentLocale);
    } else {
      $currentLocale = setlocale(LC_ALL, 0);
      setlocale(LC_TIME, 'en_CA.utf8');
      $localizedDate = strftime('%B %-e, %Y', $dateTime->getTimestamp());
      setlocale(LC_TIME, $currentLocale);
    }
    return $localizedDate;
  }

  public static function localizeTime($date, $langcode = null): string {
    $dateTime = DateUtils::dateToUTCTimezone($date);
    $lang = $langcode ?: Utils::lang();
    if(strftime('%M', $dateTime->getTimestamp()) != "00") $hasMinutes = true;

    if($lang == "fr") {
      $timeFormat = "%-Hh";
      if($hasMinutes) $timeFormat .= "%M";
    } else {
      $timeFormat = "%-l";
      if($hasMinutes) $timeFormat .= ":%M";
      $timeFormat .= "%P";
    }
    return strftime($timeFormat, $dateTime->getTimestamp());
  }

  public static function localizeDateTime($date, $langcode = null): string {
    $lang = $langcode ?: Utils::lang();
    if($lang == "fr") {
      $dateTimeAt = "à";
    } else {
      $dateTimeAt = "at";
    }
    $localizedDate = DateUtils::localizeDate($date, $lang);
    $localizedTime = DateUtils::localizeTime($date, $lang);
    return "$localizedDate $dateTimeAt $localizedTime";
  }

  public static function localizeDateRange($startDate, $endDate, $langcode = null, $includeTime = false): string {
    $lang = $langcode ?: Utils::lang();
    $sTS = DateUtils::dateToUTCTimezone($startDate)->getTimestamp();
    $eTS = DateUtils::dateToUTCTimezone($endDate)->getTimestamp();
    $sComponents = DateUtils::getDateComponents($startDate);
    $eComponents = DateUtils::getDateComponents($endDate);

    if($includeTime) {
      $sTime = DateUtils::localizeTime($startDate, $lang);
      $eTime = DateUtils::localizeTime($endDate, $lang);
    }

    if($lang == "fr") {
      $currentLocale = setlocale(LC_ALL, 0);
      setlocale(LC_TIME, 'fr_CA.utf8');
      $dateTimeAt = "à";
      $dateTimeTo = "à";
      $dateTimeFrom = "de";
      $dayToDay = strftime('%-e', $sTS) . " - " . strftime('%-e %B', $eTS) . " " . strftime('%Y', $eTS);
      $monthToMonth = strftime('%-e %B', $sTS) . " - " . strftime('%-e %B', $eTS) . " " . strftime('%Y', $eTS);
      $monthToMonthWithTime = strftime('%-e %B', $sTS) . " $dateTimeAt $sTime - " . strftime('%-e %B', $eTS) . " $dateTimeAt $eTime " . strftime('%Y', $eTS);
      setlocale(LC_TIME, $currentLocale);
    } else {
      $currentLocale = setlocale(LC_ALL, 0);
      setlocale(LC_TIME, 'en_CA.utf8');
      $dateTimeAt = "at";
      $dateTimeTo = "to";
      $dateTimeFrom = "from";
      $dayToDay = strftime('%B %-e', $sTS) . " - " . strftime('%-e', $eTS) . ", " . strftime('%Y', $eTS);
      $monthToMonth = strftime('%B %-e', $sTS) . " - " . strftime('%B %-e', $eTS) . ", " . strftime('%Y', $eTS);
      $monthToMonthWithTime = strftime('%B %-e', $sTS) . " $dateTimeAt $sTime - " . strftime('%B %-e', $eTS) . " $dateTimeAt $eTime, " . strftime('%Y', $eTS);
      setlocale(LC_TIME, $currentLocale);
    }

    if($sComponents['year'] == $eComponents['year']) {
      if($sComponents['month'] == $eComponents['month']) {
        if($sComponents['day'] == $eComponents['day']) {
          if($includeTime) {
            $localizedDateRange = DateUtils::localizeDate($startDate, $lang) . " $dateTimeFrom $sTime $dateTimeTo $eTime";
          } else {
            $localizedDateRange = DateUtils::localizeDate($startDate, $lang);
          }
        }
        else {
          if($includeTime) {
            $localizedDateRange = $monthToMonthWithTime;
          } else {
            $localizedDateRange = $dayToDay;
          }
        }
      }
      else {
        if($includeTime) {
          $localizedDateRange = $monthToMonthWithTime;
        } else {
          $localizedDateRange = $monthToMonth;
        }
      }
    } else {
      if($includeTime) {
        $localizedDateRange = DateUtils::localizeDateTime($startDate, $lang) . " - " . DateUtils::localizeDateTime($endDate, $lang);
      } else {
        $localizedDateRange = DateUtils::localizeDate($startDate, $lang) . " - " . DateUtils::localizeDate($endDate, $lang);
      }
    }

    return $localizedDateRange;
  }

  public static function localizeDateTimeRange($startDate, $endDate, $langcode = null): string {
    return DateUtils::localizeDateRange($startDate, $endDate, $langcode, true);
  }

  public static function getDateComponents($date): array {
    $ts = strtotime($date);
    return [
      "year"   => date('Y', $ts ),
      "month"  => date('m', $ts ),
      "day"    => date('d', $ts ),
      "hour"   => date('H', $ts ),
      "minute" => date('i', $ts ),
      "second" => date('s', $ts ),
    ];
  }

  public static function getAppToPhpTimezone($timezone) {
    $timezones = [
      "NST-Timezone" => "America/St_Johns",
      "AST-Timezone" => "America/Halifax",
      "EST-Timezone" => "America/Toronto",
      "CST-Timezone" => "America/Winnipeg",
      "MST-Timezone" => "America/Edmonton",
      "PST-Timezone" => "America/Vancouver"
    ];
    return $timezones[$timezone];
  }

  public static function getPhpToAppTimezone($timezone) {
    $timezones = [
      "America/St_Johns" => "NST-Timezone",
      "America/Halifax" => "AST-Timezone",
      "America/Toronto" => "EST-Timezone",
      "America/Winnipeg" => "CST-Timezone",
      "America/Edmonton" => "MST-Timezone",
      "America/Vancouver" => "PST-Timezone"
    ];
    return $timezones[$timezone];
  }

  public static function dateToUTCTimezone($date, $immutable = false) {
    if(is_numeric($date)) $date = "@" . $date;
    $utcTimezone = new DateTimeZone('UTC');
    if($immutable) return new DateTimeImmutable($date, $utcTimezone);
    return new DateTime($date, $utcTimezone);
  }

  public static function dateToUTCTimezoneTS($date): int {
    /* @var $dateTime DateTime */
    $dateTime = DateUtils::dateToUTCTimezone($date);
    return intval($dateTime->getTimestamp());
  }

  public static function getUTCTimezoneOffsetString($timezone, $langcode = null): string {
    $lang = $langcode ?: Utils::lang();
    $offset = DateUtils::getUTCTimezoneOffset($timezone);
    $prefix = "";
    if($offset < 0) $prefix = "-";
    $offset = abs($offset);
    $hours = (int)floor($offset / 3600);
    $minutes = (int)((($offset / 3600) - $hours) * 60);
    $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
    $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
    $utc = "UTC$prefix$hours:$minutes";
    return t($timezone, [], ["langcode" => $lang]) . " ($utc)";
  }

  public static function getUTCTimezoneOffset($timezone): int {
    $remote_tz = DateUtils::getAppToPhpTimezone($timezone);
    $origin_dtz = new DateTimeZone("UTC");
    $remote_dtz = new DateTimeZone($remote_tz);
    $origin_dt = new DateTime("now", $origin_dtz);
    $remote_dt = new DateTime("now", $remote_dtz);
    return $remote_dtz->getOffset($remote_dt) - $origin_dtz->getOffset($origin_dt);
  }

  public static function getTimezoneOffset($timezone1, $timezone2): int {
    $origin_dtz = new DateTimeZone(DateUtils::getAppToPhpTimezone($timezone1));
    $remote_dtz = new DateTimeZone(DateUtils::getAppToPhpTimezone($timezone2));
    $origin_dt = new DateTime("now", $origin_dtz);
    $remote_dt = new DateTime("now", $remote_dtz);
    return $remote_dtz->getOffset($remote_dt) - $origin_dtz->getOffset($origin_dt);
  }

}
