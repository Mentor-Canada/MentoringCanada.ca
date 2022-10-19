<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\Utils;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventSubmittedController extends SubmittedController {

  public static $path = "/(events|evenements)\/(submitted|soumis)$/";

  function __construct() {
    $lang = Utils::lang();
    $eventId = $_REQUEST['e'];

    /** @var $node \Drupal\node\Entity\Node */
    $node = Node::load($eventId);
    if(is_null($node)) {
      throw new NotFoundHttpException();
    }
    if($node->hasTranslation($lang)) {
      $node = $node->getTranslation($lang);
    }
    $heading = $node->get("field_submitted_heading")->getValue()[0]["value"];
    $subheading = $node->get("field_submitted_subheading")->getValue()[0]["value"];
    $paragraph = $node->get("field_submitted_paragraph")->getValue()[0]["value"];

    $emailYoutubeId = $node->get("field_email_youtube_id")->getValue()[0]["value"];
    $renderYoutubeOnPage = $node->get("field_insert_youtube_on_page")->getValue()[0]["value"];
    if($emailYoutubeId && $renderYoutubeOnPage) {
      $ytShareUrl = "https://youtu.be/";
      if(substr($emailYoutubeId, 0, strlen($ytShareUrl)) === $ytShareUrl) {
        $emailYoutubeId = str_replace($ytShareUrl, "", $emailYoutubeId);
      }
      $ytLinkUrl = "https://youtube.com/embed/$emailYoutubeId";
    }

    $this->heading = $heading;
    $this->subheading = $subheading;
    $this->paragraph = $paragraph;
    $this->ytLinkUrl = $ytLinkUrl;
  }

}
