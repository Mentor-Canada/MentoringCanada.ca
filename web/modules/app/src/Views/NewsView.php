<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\DateUtils;
  use Drupal\app\Utils\ReferenceUtils;

  class NewsView {

    public $date;

    public $banner;
    public $header;

    public $sections;
    public $moreItems;

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->date = DateUtils::localizeDate($node->get("created")->getValue()[0]['value']);

      $moreItems = ReferenceUtils::getNews([
        "nidToExclude"  => $node->id(),
        "limit"         => 5,
        "imageStyle"    => "teaser_330x220"
      ]);
      $teasers = [];
      foreach($moreItems as $item) {
        $vars = [
          '#theme' => "news_teaser_more",
          '#v' => $item
        ];
        $teaser = strval(render($vars));
        $teasers[] = $teaser;
      }
      $this->moreItems = $teasers;

    }

  }

}
