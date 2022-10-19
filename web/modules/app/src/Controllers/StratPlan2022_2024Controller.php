<?php

namespace Drupal\app\Controllers;

use Drupal;
use Drupal\app\Utils\NodeUtils;
use Drupal\app\Views\BannerView;
use Drupal\app\Views\StratPlan2022_2024View;

class StratPlan2022_2024Controller extends BaseController {

  public static $path = "/^\/strategic-plan-2022-2024$/";

  /** @var $v StratPlan2022_2024View */
  protected $v;

  public function theme_suggestions_page_alter(&$suggestions, $vars) {
    $suggestions = ["page__strat_plan_2022_2024"];
  }

  public function preprocess_html(&$vars) {
    /** @var $htmlView \Drupal\app\Views\HtmlView */
    $htmlView = $vars['v'];
    $htmlView->footer->display = false;
    parent::preprocess_html($vars);
    $this->v = new StratPlan2022_2024View();

    $node = NodeUtils::getNodeFromUrl();
    $this->v->banner = new BannerView($node);
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }
  }

  public function preprocess_page(&$vars) {

    $node = $vars['node'];

    $bodies = [];
    $bodyValues = $node->get('body')->getValue();
    foreach($bodyValues as $body) {
      $bodies[] = $body['value'];
    }
    $this->v->bodies = $bodies;

    $images = [];
    /* @var $imageEntities \Drupal\Core\Field\EntityReferenceFieldItemList */
    $imageEntities = $node->get('field_image')->referencedEntities();
    /* @var $imageEntity \Drupal\media\Entity\Media */
    foreach($imageEntities as $imageEntity) {
      /* @var $imageFile \Drupal\file\Entity\File */
      if($imageEntity) {
        $imageFile = $imageEntity->get("field_media_image")->entity;
        $imageUrl = file_create_url($imageFile->getFileUri());
        $imageMeta = $imageEntity->get("field_media_image")->getValue()[0];
        $imageWidth = $imageMeta["width"];
        $imageHeight = $imageMeta["height"];
        $imageData = [
          "url"     => $imageUrl,
          "width"   => $imageWidth,
          "height"  => $imageHeight
        ];
        $images[] = $imageData;
      }
    }
    $this->v->images = $images;

    $vars['v'] = $this->v;
  }


}
