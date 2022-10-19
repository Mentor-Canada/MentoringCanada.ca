<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\ImageUtils;
  use Drupal\app\Utils\Utils;
  use Drupal\media\Entity\Media;

  class EmploymentOpportunitiesView {

    public $lang;
    public $url;
    public $title;
    public $quotes = [];

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->lang = Utils::lang();
      $this->url = $node->toUrl()->setAbsolute()->toString();
      $this->title = $node->getTitle();

      $this->quotes[] = [
        'name'    => 'Tracy Luca-Huger',
        'title'   => t("Interim Executive Director, Mentor Canada")->__toString()
      ];
      $this->quotes[] = [
        'name'    => 'Ipellie Foo',
        'title'   => t("Indigenous Youth")->__toString()
      ];

    }

  }

}
