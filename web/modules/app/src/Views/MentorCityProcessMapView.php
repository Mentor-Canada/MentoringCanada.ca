<?php

namespace Drupal\app\Views {

  class MentorCityProcessMapView {

    public $title;
    public $steps;
    public $cta;

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->title = $node->getTitle();

      $bodies = $node->get('body')->getValue();

      $steps = [];
      for ($i = 0; $i < 21; $i++) {
        $steps[$i] = [
          'number' => str_pad(strval($i + 1),2,'0',STR_PAD_LEFT),
          'label'  => $bodies[$i * 2]['value'],
          'info'   => $bodies[$i * 2 + 1]['value']
        ];
      }
      $this->steps = $steps;

      $this->cta = $bodies[42]['value'];

    }

  }

}
