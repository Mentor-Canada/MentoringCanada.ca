<?php

namespace Drupal\app\Models;

use Drupal\app\Utils\Utils;
use Drupal\app\Views\SocialMediaToolkitTeaserView;
use Drupal\node\Entity\Node;

class SocialMediaCampaign {

  public $id;
  public $node;
  public $url;
  public $title;
  public $titleLocalized;
  public $logoLocalized;
  public $contactCopy;
  public $contactName;
  public $contactPosition;
  public $contactEmail;
  public $teaserImage;

  public $toolkits;
  public $toolkitTeasers;
  public $toolkitCount;

  function __construct(\Drupal\node\Entity\Node $node) {

    $lang = Utils::lang();
    if($node->hasTranslation($lang)) {
      $node = $node->getTranslation($lang);
    }
    $this->id = $node->id();
    $this->node = $node;
    $this->url = $node->toUrl()->toString();

    $this->title = $node->getTitle();
    if($node->hasTranslation('en')) {
      $this->titleLocalized['en'] = $node->getTranslation('en')->getTitle();
    }
    if($node->hasTranslation('fr')) {
      $this->titleLocalized['fr'] = $node->getTranslation('fr')->getTitle();
    }

    $this->logoLocalized['en'] = $this->getImage('field_smc_logo_en', 'en');
    $this->logoLocalized['fr'] = $this->getImage('field_smc_logo_fr', 'fr');

    $this->contactCopy = $node->get('field_contact_copy')->getValue()[0]['value'];
    $this->contactName = $node->get('field_contact_name')->getValue()[0]['value'];
    $this->contactPosition = $node->get('field_contact_position')->getValue()[0]['value'];
    $this->contactEmail = $node->get('field_contact_email')->getValue()[0]['value'];

    $this->toolkits = $this->getToolkits();
    $teasers = [];
    foreach($this->toolkits as $toolkit) {
      $teasers[] = new SocialMediaToolkitTeaserView($toolkit);
    }
    $this->toolkitTeasers = $teasers;
    $this->toolkitCount = count($this->toolkits);

    /* @var $mediaEntity \Drupal\media\Entity\Media */
    $mediaEntity = $node->get("field_teaser_image")->entity;
    if($mediaEntity) {
      $imageEntity = $mediaEntity->get("field_media_image")->entity;
      $imageUrl = file_create_url($imageEntity->getFileUri());
      $this->teaserImage = $imageUrl;
    }

  }

  private function getImage($fieldname, $lang) {
    /* @var $mediaEntity \Drupal\media\Entity\Media */
    $mediaEntity = $this->node->get($fieldname)->entity;
    if($mediaEntity) {
      $imageEntity = $mediaEntity->get("field_media_image")->entity;
      $imageUrl = file_create_url($imageEntity->getFileUri());
      $imageData = $mediaEntity->get("field_media_image")->getValue()[0];
      $imageWidth = $imageData['width'];
      $imageHeight = $imageData['height'];
      $alt = $this->titleLocalized[$lang];
    }
    return [
      'url'     => $imageUrl,
      'width'   => $imageWidth,
      'height'  => $imageHeight,
      'alt'     => $alt
    ];
  }

  private function getToolkits() {
    $lang = Utils::lang();
    $toolkits = [];

    /* @var $query \Drupal\Core\Entity\Query\Sql\Query */
    $query = \Drupal::entityQuery('node');

    $query->condition('langcode', $lang);
    $query->condition('status', 1);
    $query->condition('type', 'social_media_toolkit');
    $query->condition('field_smt_campaign', $this->id, '=');
    $query->sort('field_smt_date', 'DESC');

    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);

    foreach($nodes as $node) {
      $languages = $node->getTranslationLanguages();
      if(isset($languages[$lang])) {
        $node = $node->getTranslation($lang);
      }
      $toolkits[] = new SocialMediaToolkit($node, $this);
    }

    return $toolkits;
  }


}
