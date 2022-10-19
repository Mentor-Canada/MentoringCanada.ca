<?php

namespace Drupal\app\Models;

use Drupal\app\Utils\Utils;

class MentorConnectorKit {

  public function getProgramNameOptions(): array {
    $list = [];
    $lang = Utils::lang();
    $response = file_get_contents("https://server.connect.mentoringcanada.ca/api/program");
    if($response) {
      $response = json_decode($response);
      foreach($response->data as $row) {
        if($lang == "fr" && !empty($row->attributes->name->fr)) {
          $name = $row->attributes->name->fr;
        }
        else if(!empty($row->attributes->name->en)) {
          $name = $row->attributes->name->en;
        }
        if(!empty($name)) {
          $name = trim($name);
          $list[$name] = $name;
        }
      }
    }
    natcasesort($list);
    return array_unique($list);
  }

}
