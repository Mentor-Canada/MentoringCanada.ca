<?php

namespace Drupal\app\Views {

  use Drupal\app\Models\SocialMediaCampaign;
  use Drupal\app\Utils\Utils;
  use Drupal\node\Entity\Node;

  class PageSocialMediaCampaignsView {

    public $title;
    public $campaigns;
    public $teasers;

    public function __construct(\Drupal\node\Entity\Node $node) {

      $this->title = $node->getTitle();
      $this->campaigns = $this->getCampaigns();
      $teasers = [];
      foreach($this->campaigns as $campaign) {
        $teasers[] = new SocialMediaCampaignTeaserView($campaign);
      }
      $this->teasers = $teasers;

    }

    private function getCampaigns() {
      $lang = Utils::lang();
      $campaigns = [];

      /* @var $query \Drupal\Core\Entity\Query\Sql\Query */
      $query = \Drupal::entityQuery('node');

      $query->condition('langcode', $lang);
      $query->condition('status', 1);
      $query->condition('type', 'social_media_campaign');
      $query->sort('created', 'DESC');

      $nids = $query->execute();
      $nodes = Node::loadMultiple($nids);

      foreach($nodes as $node) {
        $languages = $node->getTranslationLanguages();
        if(isset($languages[$lang])) {
          $node = $node->getTranslation($lang);
        }
        $campaigns[] = new SocialMediaCampaign($node);
      }

      return $campaigns;
    }

  }

}
