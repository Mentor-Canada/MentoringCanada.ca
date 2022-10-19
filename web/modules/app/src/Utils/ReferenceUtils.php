<?php

namespace Drupal\app\Utils;

use Drupal\app\Views\EventTeaserView;
use Drupal\app\Views\NewsTeaserView;
use Drupal\app\Views\PageTeaserView;
use Drupal\app\Views\TeamMemberTeaserView;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\node\Entity\Node;

class ReferenceUtils {

  public static function getTeamMembers($args = []): array {

    $nidsToReturn = array_key_exists("nidsToReturn", $args) ? $args["nidsToReturn"] : null;

    $lang = Utils::lang();
    $teamMembers = [];

    /* @var $query \Drupal\Core\Entity\Query\Sql\Query */
    $query = \Drupal::entityQuery('node');

    $query->condition('langcode', $lang);
    $query->condition('status', 1);
    $query->condition('type', 'team_member');

    if(!is_null($nidsToReturn)) {
      if(count($nidsToReturn)) {
        $query->condition('nid', $nidsToReturn, 'IN');
      } else {
        return [];
      }
    }

    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);

    foreach($nodes as $node) {
      $languages = $node->getTranslationLanguages();
      if(isset($languages[$lang])) {
        $node = $node->getTranslation($lang);
      }
      $teamMembers[] = new TeamMemberTeaserView($node);
    }

    return $teamMembers;
  }

  public static function getNews($args = []): array {

    $promoted = array_key_exists("promoted", $args) ? $args["promoted"] : false;
    $showTypeTip = array_key_exists("showTypeTip", $args) ? $args["showTypeTip"] : false;
    $nidToExclude = array_key_exists("nidToExclude", $args) ? $args["nidToExclude"] : null;
    $nidsToReturn = array_key_exists("nidsToReturn", $args) ? $args["nidsToReturn"] : null;
    $limit = array_key_exists("limit", $args) ? $args["limit"] : null;
    $imageStyle = array_key_exists("imageStyle", $args) ? $args["imageStyle"] : "teaser_960x640";

    global $base_url;
    $lang = Utils::lang();
    $news = [];

    /* @var $query \Drupal\Core\Entity\Query\Sql\Query */
    $query = \Drupal::entityQuery('node');

    $query->condition('langcode', $lang);
    $query->condition('status', 1);
    $query->condition('type', 'news');
    $query->condition('field_unlisted', 0);
    $query->sort('created' , 'DESC', $lang);

    if(is_null($nidsToReturn)) {

      if($promoted) {
        $query->condition('promote', 1);
      }
      if($nidToExclude) {
        $query->condition('nid', $nidToExclude, '<>');
      }

    } else {

      if(count($nidsToReturn)) {
        $query->condition('nid', $nidsToReturn, 'IN');
      } else {
        return [];
      }

    }

    if(!is_null($limit)) {
      $query->range(0, $limit);
    }

    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);

    $defaultTeaserImagePointer = 1;
    foreach($nodes as $node) {
      $languages = $node->getTranslationLanguages();
      if(isset($languages[$lang])) {
        $node = $node->getTranslation($lang);
      }
      $newsTeaserView = new NewsTeaserView($node, $showTypeTip, $imageStyle);
      if(!$newsTeaserView->teaserImage) {
        $newsTeaserView->teaserImage = "$base_url/assets/default-teaser-images/default-teaser-image-$defaultTeaserImagePointer.jpg";
        $defaultTeaserImagePointer++;
        if($defaultTeaserImagePointer > 5) $defaultTeaserImagePointer = 1;
      }
      $news[] = $newsTeaserView;
    }
    return $news;
  }

  public static function getEvents($args = []): array {

    $promoted = array_key_exists("promoted", $args) ? $args["promoted"] : false;
    $showTypeTip = array_key_exists("showTypeTip", $args) ? $args["showTypeTip"] : false;
    $nidToExclude = array_key_exists("nidToExclude", $args) ? $args["nidToExclude"] : null;
    $nidsToReturn = array_key_exists("nidsToReturn", $args) ? $args["nidsToReturn"] : null;
    $types = array_key_exists("types", $args) ? $args["types"] : ['event', 'event_pom', 'event_training'];
    $limit = array_key_exists("limit", $args) ? $args["limit"] : null;
    $imageStyle = array_key_exists("imageStyle", $args) ? $args["imageStyle"] : "teaser_960x640";

    if(!count($types)) return [];

    global $base_url;
    $lang = Utils::lang();
    $events = [];

    $typesTermLookup = [
      "event"           => "1",
      "event_pom"       => "2",
      "event_training"  => "3"
    ];

    /* @var $query \Drupal\Core\Entity\Query\Sql\Query */
    $query = \Drupal::entityQuery('node');

    $orGroup = $query->orConditionGroup();
    foreach($types as $type) {
      $t = is_numeric($type) ? $type : $typesTermLookup[$type];
      $orGroup->condition('field_event_type', $t);
    }
    $query->condition($orGroup);
    $query->condition('langcode', $lang);
    $query->condition('status', 1);
    $query->condition('field_unlisted', 0);
    $query->sort('field_event_options.entity:node.field_event_date_time_range.value', 'ASC');

    if(is_null($nidsToReturn)) {

      if($promoted) {
        $query->condition('promote', 1);
      }
      if($nidToExclude) {
        $query->condition('nid', $nidToExclude, '<>');
      }
      $now = DateUtils::dateToUTCTimezone('now');
      $optionOrGroup = $query->orConditionGroup();
      $optionOrGroup->condition('field_event_options.entity:node.field_event_date_time_range.value', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=');
      $optionOrGroup->notExists('field_event_options');
      $query->condition($optionOrGroup);

    } else {

      if(count($nidsToReturn)) {
        $query->condition('nid', $nidsToReturn, 'IN');
      } else {
        return [];
      }

    }

    if(!is_null($limit)) {
      $query->range(0, $limit);
    }

    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);
    $eventsWithOptions = [];
    $eventsWithoutOptions = [];
    foreach($nodes as $node) {
      if(count($node->get('field_event_options')->getValue())) {
        $eventsWithOptions[] = $node;
      } else {
        $eventsWithoutOptions[] = $node;
      }
    }
    $nodes = array_merge($eventsWithOptions, $eventsWithoutOptions);

    $defaultTeaserImagePointer = 1;
    foreach($nodes as $node) {
      $languages = $node->getTranslationLanguages();
      if(isset($languages[$lang])) {
        $node = $node->getTranslation($lang);
      }
      $eventTeaserView = new EventTeaserView($node, $showTypeTip, $imageStyle);
      if(!$eventTeaserView->teaserImage) {
        $eventTeaserView->teaserImage = "$base_url/assets/default-teaser-images/default-teaser-image-$defaultTeaserImagePointer.jpg";
        $defaultTeaserImagePointer++;
        if($defaultTeaserImagePointer > 5) $defaultTeaserImagePointer = 1;
      }
      $events[] = $eventTeaserView;
    }

    return $events;
  }


  public static function getPages($nids): array {

    global $base_url;
    $lang = Utils::lang();
    $pages = [];

    $nodes = Node::loadMultiple($nids);

    $defaultTeaserImagePointer = 1;
    foreach($nodes as $node) {
      $languages = $node->getTranslationLanguages();
      if(isset($languages[$lang])) {
        $node = $node->getTranslation($lang);
      }

      $pageTeaserView = new PageTeaserView($node);
      if(!$pageTeaserView->teaserImage) {
        $pageTeaserView->teaserImage = "$base_url/assets/default-teaser-images/default-teaser-image-$defaultTeaserImagePointer.jpg";
        $defaultTeaserImagePointer++;
        if($defaultTeaserImagePointer > 5) $defaultTeaserImagePointer = 1;
      }
      $pages[] = $pageTeaserView;
    }

    return $pages;
  }


}
