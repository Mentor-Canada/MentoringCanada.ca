<?php

namespace Drupal\app\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\app\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $adminRoutes = ["/^user\..*$/", "/^entity\.user\.canonical$/"];
    foreach ($collection->all() as $name => $route) {
      foreach($adminRoutes as $pattern) {
        if(preg_match($pattern, $name)) {
          $route->setOption('_admin_route', TRUE);
        }
      }
    }
  }
}
