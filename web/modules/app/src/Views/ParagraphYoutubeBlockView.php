<?php

namespace Drupal\app\Views {

  use Drupal\app\Utils\Utils;

  class ParagraphYoutubeBlockView extends ParagraphBlockView {

    public $ytUrl;
    public $caption;
    public $ccPolicy;
    public $ccLang;
    public $uiLang;

    public function __construct(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {

      if(is_null($theme)) $theme = "paragraph_youtube_block";
      parent::__construct($p, $node, $theme);

      $lang = Utils::lang();
      $this->ccLang = $lang;
      $this->uiLang = "$lang-ca";
      $this->ccPolicy = $p->get("field_closed_captions")->getValue()[0]["value"];

      $ytShareUrl = "https://youtu.be/";
      $id = $p->get("field_youtube_video_id")->getValue()[0]['value'];
      if(substr($id, 0, strlen($ytShareUrl)) === $ytShareUrl) {
        $id = str_replace($ytShareUrl, "", $id);
      }
      $url = "https://youtube.com/embed/$id";
      $this->ytUrl = $url;

      $this->caption = $p->get("field_caption")->getValue()[0]['value'];
      $this->blockWidth = $p->get("field_width_options_wide")->getValue()[0]["value"];

    }

    public function __toString() {
      $vars = [
        '#theme' => $this->theme,
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
