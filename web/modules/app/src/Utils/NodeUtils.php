<?php

namespace Drupal\app\Utils;

use Drupal\node\Entity\Node;

class NodeUtils {

  public static function getNidFromUrl() {
    $current_path = \Drupal::service('path.current')->getPath();
    $matches = [];
    preg_match("/\/node\/([0-9]*)/", $current_path, $matches);
    if(count($matches)) {
      $nid = $matches[1];
      return $nid;
    }
  }

  public static function getNodeFromUrl() {
    $nid = self::getNidFromUrl();
    if(!$nid) {
      return false;
    }
    /* @var $node \Drupal\node\Entity\Node */
    $node = Node::load($nid);
    $lang = Utils::lang();
    if($node->hasTranslation($lang)) {
      $node = $node->getTranslation($lang);
    }
    return $node;
  }

}
