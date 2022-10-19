<?php

namespace Drupal\app;

use Drupal;
use Drupal\app\Controllers\CommonController;
use Drupal\app\Utils\NodeUtils;
use Drupal\Core\Url;

class App {

  public $pageControllers = [];
  public $activeControllers = null;
  public $commonController = null;
  public $langCode;

  /* @var $path array */
  public $path;

  public static function Instance() {
    static $inst = null;
    if ($inst === null) {
      $inst = new App();
    }
    return $inst;
  }

  private function __construct() {

    $this->langCode = Drupal::languageManager()->getCurrentLanguage()->getId();

    $fullPath = Drupal::request()->getRequestUri();
    if($GLOBALS['base_path'] != "/") {
      $pathString = str_replace($GLOBALS['base_path'], "", $fullPath);
    }
    else {
      $pathString = $fullPath;
      if($pathString[0] == "/") {
        $pathString = substr($pathString, 1);
      }
    }
    $path = explode("/", $pathString);
    array_shift($path);
    $this->path = $path;

    $pageControllers = $this->phpFilesForPath(__DIR__ . "/Controllers");
    $this->registerPageControllers($pageControllers);
    $pageControllers = $this->pageControllers;
    \Drupal::moduleHandler()->alter('pagecontrollers', $pageControllers);
    $this->pageControllers = $pageControllers;
  }

  public function registerPageControllers($rows) {
    foreach($rows as $row) {
      $components = explode("/", $row);
      $matches = [];
      preg_match("/web\/modules\/app\/src\/Controllers\/(.*)/", $row, $matches);
      $namespace = explode("/", $matches[1]);
      array_pop($namespace);
      if(count($namespace)) {
        $namespace = implode("\\", $namespace) . "\\";
      }
      else {
        $namespace = "";
      }
      $className = str_replace(".php", "", array_pop($components));
      $className = "\\Drupal\\app\\Controllers\\$namespace$className";
      array_push($this->pageControllers, $className);
    }
  }

  public function invoke($func, $cb) {

    $route = Drupal::request()->attributes->get('_route');
    if($route == "system.404") {
      $this->activeControllers = [new CommonController()];
    }

    /* @var $node \Drupal\node\Entity\Node */
    $node = NodeUtils::getNodeFromUrl();
    $template = null;
    $components = explode("/", Url::fromRoute('<current>')->toString());
    if($components[count($components) - 1] != "edit") {
      if($node && $node->hasField("field_template")) {
        $template = $node->get("field_template")->getValue()[0]['value'];
      }
    }

    if($this->activeControllers == null) {
      $this->activeControllers = [new CommonController()];
      $path = Drupal::service('path.current')->getPath();
      $currentPath = Drupal::service('path.alias_manager')->getAliasByPath($path);
      foreach($this->pageControllers as $pageController) {

        if($template && property_exists($pageController, "template")) {
          $valid = preg_match($pageController::$template, $template);
          if ($valid) {
            $this->activeControllers[] = new $pageController();
          }
        }
        else if(method_exists($pageController, "path")) {
          $validPath = preg_match($pageController::path(), $currentPath);
          if ($validPath) {
            $this->activeControllers[] = new $pageController();
          }
        }
        else if(property_exists($pageController, "path")) {
          $validPath = preg_match($pageController::$path, $currentPath);
          if ($validPath) {
            $this->activeControllers[] = new $pageController();
          }
        }
        else if(property_exists($pageController, "route")) {
          $route = Drupal::request()->attributes->get('_route');
          if(preg_match($pageController::$route, $route)) {
            $this->activeControllers[] = new $pageController();
          }
          if($route == "system.404") {
            $this->activeControllers[] = new $pageController();
          }
        }
        else if(property_exists($pageController, "type")) {
          /* @var $theme \Drupal\Core\Theme\ActiveTheme */
          $theme = Drupal::service('theme.manager')->getActiveTheme();
          $theme = $theme->getName();
          if($node && $theme != "admin") {
            $type = $node->getType();
            $validType = preg_match($pageController::$type, $type);
            if($validType) {
              $this->activeControllers[] = new $pageController();
            }
          }
        }
      }
    }

    foreach($this->activeControllers as $pageController) {
      if(method_exists($pageController, $func)) {
        $cb($pageController, $func);
      }
    }

  }

  public function phpFilesForPath($path) {
    $files = [];
    foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $filename) {
      $file_parts = pathinfo($filename);

      if(strpos($file_parts['filename'], "Base") === 0) continue;

      if($file_parts['extension'] == 'php') {
        $files[] = $filename;
      }
    }
    return $files;
  }

}
