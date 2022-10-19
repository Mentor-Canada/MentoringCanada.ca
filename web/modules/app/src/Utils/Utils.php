<?php

namespace Drupal\app\Utils;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Exception;

class Utils {

  public static function lang(): string {
    return \Drupal::languageManager()->getCurrentLanguage()->getId();
  }

  public static function displayLang(): string {
    $lang = self::lang();
    switch ($lang) {
      case 'en':
        return 'English';
      case 'fr':
        return 'Français';
      default:
        return $lang;
    }
  }

  public static function globalSettings($lang = null): array {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'global_settings');
    $query->range(0,1);
    $nids = $query->execute();
    $nid = current($nids);

    if(is_null($lang)) $lang = Utils::lang();
    /* @var $node \Drupal\node\Entity\Node */
    $node = Node::load($nid);
    if(!$node) {
      return [];
    }
    $languages = $node->getTranslationLanguages();
    if(isset($languages[$lang])) {
      $node = $node->getTranslation($lang);
    }

    $addressName = $node->get("field_address_name")->getValue()[0]["value"];
    $addressLine1 = $node->get("field_address_line_1")->getValue()[0]["value"];
    $addressLine2 = $node->get("field_address_line_2")->getValue()[0]["value"];
    $addressLine3 = $node->get("field_address_line_3")->getValue()[0]["value"];

    $phone = $node->get("field_phone")->getValue()[0]["value"];
    $extension = $node->get("field_extension")->getValue()[0]["value"];

    $phoneDisplay = "1 (";
    $phoneDisplay .= substr($phone, 0, 3);
    $phoneDisplay .= ") ";
    $phoneDisplay .= substr($phone, 3, 3);
    $phoneDisplay .= "-";
    $phoneDisplay .= substr($phone, 6, 4);

    $phoneDisplayWithExtension = $phoneDisplay;
    if($extension) $phoneDisplayWithExtension .= " ext. $extension";

    $phoneLink = "tel:1$phone";
    if($extension) $phoneLink .= ",$extension";

    $email = $node->get("field_email")->getValue()[0]["value"];
    $emailDisplay = $email;
    $emailLink = "mailto:$email";

    $fb = $node->get("field_social_fb")->getValue()[0]["uri"];
    $tw = $node->get("field_social_tw")->getValue()[0]["uri"];
    $li = $node->get("field_social_li")->getValue()[0]["uri"];
    $ig = $node->get("field_social_ig")->getValue()[0]["uri"];
    $yt = $node->get("field_social_yt")->getValue()[0]["uri"];
    $social = [
      "fb"  => $fb,
      "tw"  => $tw,
      "li"  => $li,
      "ig"  => $ig,
      "yt"  => $yt
    ];

    $userIds = [];
    foreach($node->get("field_contact_form_recipients")->getValue() as $user) {
      $userIds[] = $user["target_id"];
    }
    /* @var $contactFormRecipient \Drupal\user\Entity */
    $users = User::loadMultiple($userIds);
    $contactFormRecipients = [];
    foreach($users as $contactFormRecipient) {
      $userName = $contactFormRecipient->get("name")->getValue()[0]["value"];
      $userEmail = $contactFormRecipient->get("mail")->getValue()[0]["value"];
      $userPreferredLanguage = $contactFormRecipient->get("preferred_langcode")->getValue()[0]["value"];
      $contactFormRecipients[] = [
        "userName"              => $userName,
        "userEmail"             => $userEmail,
        "userPreferredLanguage" => $userPreferredLanguage
      ];
    }

    $aboutUs = $node->get("field_about_us")->getValue()[0]["value"];

    $globalSettings = [
      "addressName"               => $addressName,
      "addressLine1"              => $addressLine1,
      "addressLine2"              => $addressLine2,
      "addressLine3"              => $addressLine3,
      "phone"                     => $phone,
      "extension"                 => $extension,
      "phoneDisplay"              => $phoneDisplay,
      "phoneDisplayWithExtension" => $phoneDisplayWithExtension,
      "phoneLink"                 => $phoneLink,
      "email"                     => $email,
      "emailDisplay"              => $emailDisplay,
      "emailLink"                 => $emailLink,
      "social"                    => $social,
      "contactFormRecipients"     => $contactFormRecipients,
      "aboutUs"                   => $aboutUs
    ];
    return $globalSettings;

  }

  public static function sort($array): array {
    $collator = new \Collator(\Collator::FRENCH_COLLATION);
    $collator->asort($array);
    return $array;
  }

  public static function isValidReCaptcha() {
    if(isset($_POST['order_id'])) {
      return true;
    }

    $captcha = $_POST['g-recaptcha-response'];
    $googleRecaptchaSecret = getenv('GOOGLE_RECAPTCHA_SECRET');
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$googleRecaptchaSecret&response=$captcha&remoteip={$_SERVER['REMOTE_ADDR']}");

    $captcha = json_decode($response);

    if ($captcha->success === false) {
      \Drupal::messenger()->addError(t('reCAPTCHA Error'));
      return false;
    }
    return true;
  }

  public static function getMenuItems($menuName, $getChildren = false, $maxDepth = 1) {
    $menu = Utils::getMainMenuTree($menuName, $maxDepth);

    $menuItems = [];
    foreach ($menu['#items'] as $item) {
      if($getChildren) {
        $childItems = [];
        if (!empty($item['below'])) {
          foreach ($item['below'] as $child) {
            $childItems[] = [
              'url'   => $child['url']->toString(),
              'title' => $child['title'],
            ];
          }
        }
      }
      $menuItems[] = [
        'url'      => $item['url']->toString(),
        'title'    => $item['title'],
        'children' => $childItems ? $childItems : null
      ];
    }

    return $menuItems;
  }

  public static function getChildrenByNid($nid) {

    $menu = Utils::getMainMenuTree("main");
    $children = [];

    $walk = function($items) use(&$walk, &$children, $nid) {
      foreach($items as $item) {

        /* @var $url \Drupal\Core\Url */
        $url = $item['url'];
        try {
          $params = $url->getRouteParameters();
        }
        catch(Exception $e) {
          continue;
        }
        if(!isset($params['node'])) {
          /** this menu object isn't a node */
          continue;
        }
        $itemNid = $params['node'];
        if($itemNid == $nid) {

          foreach($item['below'] as $child) {
            try {
              $childParams = $child['url']->getRouteParameters();
            }
            catch(Exception $e) {
              continue;
            }
            if(!isset($childParams['node'])) {
              /** this child object isn't a node */
              continue;
            }
            $children[] = $childParams['node'];
          }

          return;
        }

        if($item['below']) {
          $walk($item['below']);
        }

      }

    };

    $items = $menu['#items'];
    $walk($items);

    return $children;

  }

  public static function getMainMenuTree($menuName = 'main', $maxDepth = null) {
    /* @var $menuTree \Drupal\Core\Menu\MenuLinkTree */
    $menuTree = \Drupal::menuTree();
    /* @var $menuTree \Drupal\Core\Menu\MenuTreeParameters */
    $parameters = new \Drupal\Core\Menu\MenuTreeParameters();
    $parameters->onlyEnabledLinks();
    $parameters->setMaxDepth($maxDepth);
    $tree = $menuTree->load($menuName, $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menuTree->transform($tree, $manipulators);
    $menu = $menuTree->build($tree);
    return $menu;
  }

  public static function getLanguageToggleHTML($renderButton = true) {

    global $base_url;

    $status = \Drupal::requestStack()->getCurrentRequest()->attributes->get('exception');
    $language = \Drupal::languageManager()->getCurrentLanguage();
    $current_path = \Drupal::service('path.current')->getPath();

    if($current_path == "/events/submitted" && $language->getId() == 'en') {
      $eventId = intval($_REQUEST['e']);
      $url = "{$base_url}/fr/evenements/soumis?e=$eventId";
    }
    else if($current_path == "/evenements/soumis" && $language->getId() == 'fr') {
      $eventId = intval($_REQUEST['e']);
      $url = "{$base_url}/en/events/submitted?e=$eventId";
    }

    if($current_path == "/newsletter/submitted" && $language->getId() == 'en') {
      $newsletterPageNid = intval($_REQUEST['n']);
      $subStatus = intval($_REQUEST['s']);
      $url = "{$base_url}/fr/infolettre/soumis?n=$newsletterPageNid&s=$subStatus";
    }
    else if($current_path == "/infolettre/soumis" && $language->getId() == 'fr') {
      $newsletterPageNid = intval($_REQUEST['n']);
      $subStatus = intval($_REQUEST['s']);
      $url = "{$base_url}/en/newsletter/submitted?n=$newsletterPageNid&s=$subStatus";
    }

    if($current_path == "/contact/submitted" && $language->getId() == 'en') {
      $url = "{$base_url}/fr/nous-joindre/soumis";
    }
    else if($current_path == "/nous-joindre/soumis" && $language->getId() == 'fr') {
      $url = "{$base_url}/en/contact/submitted";
    }

    if($status && $status->getStatusCode() == 404) {
      $alias = "";
    }
    else {
      $alias = \Drupal::service('path.alias_manager')->getAliasByPath($current_path);
      if($alias == "/front") {
        $alias = "";
      }
    }

    $matches = [];
    $node = null;
    if(empty($url)) {
      if(preg_match("/node\/([0-9]+)/", $current_path, $matches) && !empty($alias)) {
        $nid = $matches[1];
        $node = node_load($nid);
        $targetLanguage = $language->getId() == "en" ? "fr" : "en";
        $languages = $node->getTranslationLanguages();
        if(isset($languages[$targetLanguage])) {
          $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$node->id(), $targetLanguage);
          $url = "{$base_url}/$targetLanguage$alias";
        }
        else {
          $url = Utils::getBaseUrlTranslated($language);
        }
      }
      else {
        $url = Utils::getBaseUrlTranslated($language);
        if(!empty($alias)) {
          $url .= "$alias";
        }
      }
    }

    if($language->getId() == "en") {
      $label = "Français";
    }
    else {
      $label = "English";
    }

    if($renderButton) {
      $markup = "<a href='$url'><button class='compact'>$label</button></a>";
    } else {
      $markup = "<a href='$url'>$label</a>";
    }

    return $markup;

  }

  public static function getBaseUrlTranslated($language): string {
    global $base_url;
    if($language->getId() == "en") {
      $url = "{$base_url}/fr";
    }
    else {
      $url = "{$base_url}/en";
    }
    return $url;
  }

}
