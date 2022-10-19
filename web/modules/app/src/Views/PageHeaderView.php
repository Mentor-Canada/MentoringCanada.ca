<?php

namespace Drupal\app\Views {

  use Drupal\Core\Url;

  class PageHeaderView {

    public $url;
    public $title;
    public $eyebrow;
    public $heading;
    public $subheading;
    public $linkUrl;
    public $linkTitle;
    public $hasLink;
    public $width = "standard";

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->url = $node->toUrl()->setAbsolute()->toString();
      $this->title = $node->getTitle();

      if($node->hasField("field_banner_eyebrow")) {
        $this->eyebrow = $node->get("field_banner_eyebrow")->getValue()[0]['value'];
      }

      if($node->hasField("field_banner_heading")) {
        $this->heading = $node->get("field_banner_heading")->getValue()[0]['value'];
      }

      if($node->hasField("field_banner_subheading")) {
        $this->subheading = $node->get("field_banner_subheading")->getValue()[0]['value'];
      }

      if($node->hasField("field_banner_link")) {
        $link = $node->get("field_banner_link")->getValue()[0];
        if($link["uri"]) {
          $this->linkUrl = Url::fromUri($link["uri"])->toString();
          $this->linkTitle = $link["title"];
          $this->hasLink = true;
        }
      }

    }

    public function __toString() {
      $vars = [
        '#theme' => "page_header",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
