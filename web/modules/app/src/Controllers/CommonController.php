<?php

namespace Drupal\app\Controllers;

use Drupal\app\Utils\NodeUtils;
use Drupal\app\Utils\Utils;
use Drupal\app\Views\CompactMenuView;
use Drupal\app\Views\FooterView;
use Drupal\app\Views\HeaderView;
use Drupal\app\Views\HtmlView;

class CommonController extends BaseController {

  function preprocess_html(&$vars) {
    global $base_url;

    $v = new HtmlView();
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $v->lang = $lang;

    $themeName = \Drupal::service('theme.manager')->getActiveTheme()->getName();
    if($themeName == "claro") $this->applyAdminCss($vars);

    $current_path = \Drupal::service('path.current')->getPath();
    $nid = str_replace("/node/", "", $current_path);
    $vars['classes'] = $vars['classes'] ?? [];
    if(intval($nid)) {
      $node = node_load($nid);
      if($node) {
        $this->setOGMetaTags($node,$vars);
        $type = $node->getType();
        $type = str_replace("_", "-", $type);
        $vars['classes'][] = "page-type-{$type}";
        $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$nid);
        $alias = str_replace("/", "-", $alias);
        $vars['classes'][] = "page-alias-{$alias}";
      }
    }
    else {
      $pathClass = substr($current_path, 1);
      $pathClass = str_replace("/", "-", $pathClass);
      $pathClass = "path-$pathClass";
      $vars['classes'][] = $pathClass;
    }

    if(!isset($vars['attributes']['class'])) {
      $vars['attributes']['class'] = [];
    }

    $vars['attributes']['class'] = array_merge($vars['attributes']['class'], $vars['classes']);

    $route_name = \Drupal::request()->attributes->get('_route');
    if ('system.404' === $route_name) {
      $vars['classes'][] = 'page-not-found';
    }

    $cssFile = realpath(DRUPAL_ROOT . "/themes/pub/main.css");
    $cssMd5 = md5_file($cssFile);
    $v->cssUrl = "{$GLOBALS['base_url']}/themes/pub/main.css?$cssMd5";

    $jsFile = realpath(DRUPAL_ROOT . "/themes/pub/main.js");
    $jsMd5 = md5_file($jsFile);
    $v->tsUrl = "{$GLOBALS['base_url']}/themes/pub/main.js?$jsMd5";


    $gs = Utils::globalSettings();
    $logo = $this->theme('logo', ["lang_code" => $v->lang_code]);
    $menu = Utils::getMenuItems("main", true, 2);

    $v->header = new HeaderView([
      'baseUrl' => $base_url,
      'lang'    => $lang,
      'gs'      => $gs,
      'logo'    => $logo,
      'menu'    => $menu
    ]);
    if(!$v->header->display) {
      $vars['classes'][] = 'hidden-header';
    }

    $v->footer = new FooterView([
      'baseUrl' => $base_url,
      'lang'    => $lang,
      'gs'      => $gs,
      'menu'    => $menu
    ]);

    $v->compactMenu = new CompactMenuView([
      'baseUrl' => $base_url,
      'lang'    => $lang,
      'gs'      => $gs,
      'logo'    => $logo,
      'menu'    => $menu
    ]);

    $vars['classes'] = implode(" ", $vars['classes']);

    $vars['v'] = $v;
  }

  function theme_suggestions_page_alter(&$suggestions, $vars) {
    /* @var $node \Drupal\node\Entity\Node */
    $node = NodeUtils::getNodeFromUrl();
    if($node) {
      $type = $node->getType();
      $suggestions[] = "page__type__{$type}";
    }
  }

  function preprocess_page(&$vars) {}

  private function setOGMetaTags(\Drupal\node\Entity\Node $node, &$vars) {
    global $base_url;
    $lang = Utils::lang();

    $head = &$vars['page']['#attached']['html_head'];
    /** use regular meta tags for open graph */
    foreach($head as $key => $row) {
      if($row[1] == "title") {
        $ogTitle = $row;
        $ogTitle[0]['#attributes']['name'] = 'og:title';
        $ogTitle[1] = "og:title";
      }
      if($row[1] == "description") {
        $ogDescription = $row;
        $ogDescription[0]['#attributes']['name'] = 'og:description';
        $ogDescription[1] = "og:description";
      }
    }
    if($ogTitle) $head[] = $ogTitle;
    if($ogDescription) $head[] = $ogDescription;

    $ogType = [];
    $ogType[0]['#tag'] = 'meta';
    $ogType[0]['#attributes']['name'] = 'og:type';
    $ogType[0]['#attributes']['content'] = 'website';
    $ogType[1] = "og:type";
    $head[] = $ogType;

    if($node->hasField("field_banner_image")) {
      /* @var $mediaEntity \Drupal\media\Entity\Media */
      $mediaEntity = $node->get("field_banner_image")->entity;
      if($mediaEntity) {
        $imageEntity = $mediaEntity->get("field_media_image")->entity;
        $imageUrl = file_create_url($imageEntity->getFileUri());
        $imageWidth = $mediaEntity->get("field_media_image")->getValue()[0]["width"];
        $imageHeight = $mediaEntity->get("field_media_image")->getValue()[0]["height"];
      }
    }
    if(!$imageUrl) {
      if($node->hasField("field_teaser_image")) {
        /* @var $mediaEntity \Drupal\media\Entity\Media */
        $mediaEntity = $node->get("field_teaser_image")->entity;
        if ($mediaEntity) {
          $imageEntity = $mediaEntity->get("field_media_image")->entity;
          $imageUrl = file_create_url($imageEntity->getFileUri());
          $imageWidth = $mediaEntity->get("field_media_image")->getValue()[0]["width"];
          $imageHeight = $mediaEntity->get("field_media_image")->getValue()[0]["height"];
        }
      }
    }
    if(!$imageUrl) {
      $imageUrl = "$base_url/assets/default-og-images/MENTOR-Canada-OG-Image-$lang.jpg";
      $imageWidth = "1920";
      $imageHeight = "1280";
    }

    $ogImage = [];
    $ogImage[0]['#tag'] = 'meta';
    $ogImage[0]['#attributes']['name'] = 'og:image';
    $ogImage[0]['#attributes']['content'] = $imageUrl;
    $ogImage[1] = "og:image";
    $head[] = $ogImage;

    $ogImageWidth = [];
    $ogImageWidth[0]['#tag'] = 'meta';
    $ogImageWidth[0]['#attributes']['name'] = 'og:image:width';
    $ogImageWidth[0]['#attributes']['content'] = $imageWidth;
    $ogImageWidth[1] = "og:image:width";
    $head[] = $ogImageWidth;

    $ogImageHeight = [];
    $ogImageHeight[0]['#tag'] = 'meta';
    $ogImageHeight[0]['#attributes']['name'] = 'og:image:height';
    $ogImageHeight[0]['#attributes']['content'] = $imageHeight;
    $ogImageHeight[1] = "og:image:height";
    $head[] = $ogImageHeight;

  }

  private function applyAdminCss(&$vars) {
    global $base_url;
    $head = &$vars['page']['#attached']['html_head'];
    $link = [];
    $link[0]['#tag'] = 'link';
    $link[0]['#attributes']['rel'] = "stylesheet";
    $link[0]['#attributes']['type'] = "text/css";
    $link[0]['#attributes']['href'] = "$base_url/themes/admin/style.css";
    $head[] = $link;
  }

}
