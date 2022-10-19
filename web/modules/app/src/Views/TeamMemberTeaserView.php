<?php

namespace Drupal\app\Views {

  use Drupal\image\Entity\ImageStyle;

  class TeamMemberTeaserView {

    public $name;
    public $title;
    public $phone;
    public $email;
    public $bio;
    public $image;


    public function __construct(\Drupal\node\Entity\Node $node) {
      $this->name = $node->getTitle();
      $this->title = $node->get("field_team_member_title")->getValue()[0]['value'];
      $this->phone = $node->get("field_team_member_phone")->getValue()[0]['value'];
      $this->email = $node->get("field_team_member_email")->getValue()[0]['value'];
      $this->bio = $node->get("field_team_member_bio")->getValue()[0]['value'];

      /* @var $mediaEntity \Drupal\media\Entity\Media */
      $mediaEntity = $node->get("field_team_member_image")->entity;
      if($mediaEntity) {
        $imageEntity = $mediaEntity->get("field_media_image")->entity;
        $fileUri = $imageEntity->getFileUri();
        $imageUrl = ImageStyle::load('teaser_960x640')->buildUrl($fileUri);
        $this->image = $imageUrl;
      }
    }

    public function __toString() {
      $vars = [
        '#theme' => "team_member_teaser",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
