<?php

namespace Drupal\app\Models;

use Drupal\app\Utils\Utils;

class SocialMediaToolkit {

  public $node;
  public $url;
  public $campaign;
  public $date;
  public $title;
  public $titleLocalized;
  public $fbLocalized;
  public $twLocalized;
  public $igLocalized;
  public $messageLocalized;
  public $teaserImage;

  function __construct(\Drupal\node\Entity\Node $node, $socialMediaCampaign = null) {

    $lang = Utils::lang();
    if($node->hasTranslation($lang)) {
      $node = $node->getTranslation($lang);
    }
    $this->node = $node;
    $this->url = $node->toUrl()->toString();

    $this->campaign = $this->getCampaign($node, $socialMediaCampaign);

    $date = $node->get("field_smt_date")->getValue()[0]['value'];
    $dateTime = new \DateTime($date);
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
    $this->date = $localizedDate;


    $this->title = $node->getTitle();
    if($node->hasTranslation('en')) {
      $this->titleLocalized['en'] = $node->getTranslation('en')->getTitle();
    }
    if($node->hasTranslation('fr')) {
      $this->titleLocalized['fr'] = $node->getTranslation('fr')->getTitle();
    }

    $this->fbLocalized['en'] = $this->getImage('field_smt_fb_en', 'en');
    $this->fbLocalized['fr'] = $this->getImage('field_smt_fb_fr', 'fr');
    $this->twLocalized['en'] = $this->getImage('field_smt_tw_en', 'en');
    $this->twLocalized['fr'] = $this->getImage('field_smt_tw_fr', 'fr');
    $this->igLocalized['en'] = $this->getImage('field_smt_ig_en', 'en');
    $this->igLocalized['fr'] = $this->getImage('field_smt_ig_fr', 'fr');

    $this->messageLocalized = [
      'en'  => $node->get('field_smt_messaging_en')->getValue()[0]['value'],
      'fr'  => $node->get('field_smt_messaging_fr')->getValue()[0]['value']
    ];

    /* @var $mediaEntity \Drupal\media\Entity\Media */
    $mediaEntity = $node->get("field_teaser_image")->entity;
    if($mediaEntity) {
      $imageEntity = $mediaEntity->get("field_media_image")->entity;
      $imageUrl = file_create_url($imageEntity->getFileUri());
      $this->teaserImage = $imageUrl;
    } else {
      $this->teaserImage = $this->fbLocalized[$lang]['url'];
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

  private function getCampaign(\Drupal\node\Entity\Node $node, $socialMediaCampaign = null): SocialMediaCampaign {
    if($socialMediaCampaign) {
      $campaign = $socialMediaCampaign;
    } else {
      $campaignNode = $node->get('field_smt_campaign')->referencedEntities()[0];
      $campaign = new SocialMediaCampaign($campaignNode);
    }
    return $campaign;
  }


}
