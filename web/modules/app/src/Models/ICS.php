<?php

namespace Drupal\app\Models;

use DateTime;
use DateTimeZone;

class ICS {

  private $data;
  private $name;

  function __construct(DateTime $start, DateTime $end, $name, $description, $location) {
    $this->name = $name;

    $start->setTimezone(new DateTimeZone('UTC'));
    $startString = $start->format('Ymd\THis\Z');

    $end->setTimezone(new DateTimeZone('UTC'));
    $endString = $end->format('Ymd\THis\Z');
    $description = str_replace("\n", "\\n", $description);
    $this->data = "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART:".$startString."\nDTEND:".$endString."\nLOCATION:".$location."\nTRANSP: OPAQUE\nSEQUENCE:0\nUID:\nDTSTAMP:".date("Ymd\THis\Z")."\nSUMMARY:".$name."\nDESCRIPTION:".$description."\nPRIORITY:1\nCLASS:PUBLIC\nEND:VEVENT\nEND:VCALENDAR\n";
  }

  function show() {
    header("Content-type:text/calendar");
    header('Content-Disposition: attachment; filename="'.$this->name.'.ics"');
    Header('Content-Length: '.strlen($this->data));
    Header('Connection: close');
    print $this->data;
    exit;
  }

}
