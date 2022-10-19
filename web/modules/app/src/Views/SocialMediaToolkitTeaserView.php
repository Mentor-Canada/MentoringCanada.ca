<?php

namespace Drupal\app\Views {

  use Drupal\app\Models\SocialMediaToolkit;

  class SocialMediaToolkitTeaserView {

    public $socialMediaToolkit;
    public $url;
    public $title;
    public $date;
    public $image;


    public function __construct(SocialMediaToolkit $socialMediaToolkit) {

      $this->socialMediaToolkit = $socialMediaToolkit;
      $this->url = $socialMediaToolkit->url;
      $this->title = $socialMediaToolkit->title;
      $this->date = $socialMediaToolkit->date;
      $this->image = $socialMediaToolkit->teaserImage;

    }

    public function __toString() {
      $vars = [
        '#theme' => "social_media_toolkit_teaser",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
