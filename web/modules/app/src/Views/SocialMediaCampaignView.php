<?php

namespace Drupal\app\Views {

  use Drupal\app\Models\SocialMediaCampaign;
  use Drupal\app\Utils\Utils;

  class SocialMediaCampaignView {

    public $socialMediaCampaign;

    public $title;
    public $teasers;
    public $langOrder;

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->socialMediaCampaign = new SocialMediaCampaign($node);

      $this->title = $this->socialMediaCampaign->title;
      $this->teasers = $this->socialMediaCampaign->toolkitTeasers;

      $lang = Utils::lang();
      $langOrder = ['en', 'fr'];
      if($lang == 'fr') $langOrder = ['fr', 'en'];
      $this->langOrder = $langOrder;

    }

  }

}
