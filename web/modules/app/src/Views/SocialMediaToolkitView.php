<?php

namespace Drupal\app\Views {

  use Drupal\app\Models\SocialMediaToolkit;
  use Drupal\app\Utils\Utils;

  class SocialMediaToolkitView {

    public $socialMediaToolkit;

    public $date;
    public $title;
    public $campaign;
    public $channelImages;
    public $messageLocalized;
    public $aboutUs;
    public $langOrder;

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->socialMediaToolkit = new SocialMediaToolkit($node);

      $this->date = $this->socialMediaToolkit->date;
      $this->title = $this->socialMediaToolkit->title;
      $this->campaign = $this->socialMediaToolkit->campaign;
      $this->channelImages = [
        'fb'  => $this->socialMediaToolkit->fbLocalized,
        'tw'  => $this->socialMediaToolkit->twLocalized,
        'ig'  => $this->socialMediaToolkit->igLocalized
      ];

      $this->setRelativeAspectRatio();
      $this->setChannelDisplayName();

      $this->messageLocalized = $this->socialMediaToolkit->messageLocalized;

      $gs = Utils::globalSettings();
      $this->aboutUs = $gs['aboutUs'];

      $lang = Utils::lang();
      $langOrder = ['en', 'fr'];
      if($lang == 'fr') $langOrder = ['fr', 'en'];
      $this->langOrder = $langOrder;

    }

    private function setRelativeAspectRatio() {
      $maxAspectRatio = 0;
      $langs = ['en', 'fr'];
      foreach($this->channelImages as $key=>$channel) {
        foreach($langs as $lang) {
          $aspectRatio = $channel[$lang]['width'] / $channel[$lang]['height'];
          if($aspectRatio > $maxAspectRatio) $maxAspectRatio = $aspectRatio;
          $this->channelImages[$key][$lang]['aspectRatio'] = $aspectRatio;
        }
      }
      foreach($this->channelImages as $key=>$channel) {
        foreach($langs as $lang) {
          $this->channelImages[$key][$lang]['relativeWidth'] = $this->channelImages[$key][$lang]['aspectRatio'] / $maxAspectRatio * 100;
        }
      }
    }

    private function setChannelDisplayName() {
      $channels = [
        'fb'  => t("Facebook & LinkedIn")->__toString(),
        'tw'  => t("Twitter")->__toString(),
        'ig'  => t("Instagram")->__toString()
      ];
      $langs = ['en', 'fr'];
      foreach($this->channelImages as $key=>$channel) {
        foreach($langs as $lang) {
          $this->channelImages[$key][$lang]['channelName'] = $channels[$key];
        }
      }
    }

  }

}
