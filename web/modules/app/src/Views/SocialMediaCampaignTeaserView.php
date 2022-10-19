<?php

namespace Drupal\app\Views {

  use Drupal\app\Models\SocialMediaCampaign ;

  class SocialMediaCampaignTeaserView {

    public $socialMediaCampaign;
    public $url;
    public $title;
    public $count;
    public $image;


    public function __construct(SocialMediaCampaign $socialMediaCampaign) {

      $this->socialMediaCampaign = $socialMediaCampaign;
      $this->url = $socialMediaCampaign->url;
      $this->title = $socialMediaCampaign->title;
      $this->image = $socialMediaCampaign->teaserImage;

      $count = $socialMediaCampaign->toolkitCount;
      $toolkitString = t("Toolkits");
      if($count == 1) $toolkitString = t("Toolkit");
      $this->count = "$count $toolkitString";

    }

    public function __toString() {
      $vars = [
        '#theme' => "social_media_campaign_teaser",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
