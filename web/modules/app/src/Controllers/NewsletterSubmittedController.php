<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\Utils;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsletterSubmittedController extends SubmittedController {

  public static $path = "/(newsletter|infolettre)\/(submitted|soumis)$/";

  function __construct() {
    $lang = Utils::lang();
    $nid = $_REQUEST['n'];
    $subStatus = $_REQUEST['s'];

    /** @var $node \Drupal\node\Entity\Node */
    $node = Node::load($nid);
    if(is_null($node)) {
      throw new NotFoundHttpException();
    }
    if($node->hasTranslation($lang)) {
      $node = $node->getTranslation($lang);
    }

    if($subStatus) {
      $this->body = $node->get("body")->getValue()[1]["value"];
    } else {
      $this->body = $node->get("body")->getValue()[2]["value"];
    }

    $this->renderBodyMessage = true;
  }

}
