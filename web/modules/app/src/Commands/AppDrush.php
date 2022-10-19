<?php

namespace Drupal\app\Commands;

use Drupal\app\Utils\EventUtils;
use Drush\Commands\DrushCommands;

class AppDrush extends DrushCommands {

  /**
   * @command app:updatedomains
   */
  public function updatedomains() {
    $db = \Drupal::database();

    $q = "SELECT data FROM config WHERE name='language.negotiation'";
    $query = $db->query($q);
    $row = $query->fetchField();

    $data = unserialize($row);
    $domains = json_decode(getenv('BASE_URL'), true);
    $data['url']['domains']['en'] = $domains['en'];
    $data['url']['domains']['fr'] = $domains['fr'];

    $value = serialize($data);
    $q = "UPDATE config SET data = :value WHERE name = 'language.negotiation'";
    $db->query($q, ['value' => $value]);
  }

  /**
   * @command app:updateswiftmailer
   */
  public function updateswiftmailer() {
    $db = \Drupal::database();

    $q = "SELECT data FROM config WHERE name='swiftmailer.transport'";
    $query = $db->query($q);
    $row = $query->fetchField();

    $data = unserialize($row);

    if($host = getenv('SMTP_HOST')) {
      $data['smtp_host'] = $host;
    }

    if($username = getenv('SMTP_USERNAME')) {
      $data['smtp_credentials']['swiftmailer']['username'] = $username;
    }

    if($password = getenv('SMTP_PASSWORD')) {
      $data['smtp_credentials']['swiftmailer']['password'] = $password;
    }

    $value = serialize($data);
    $q = "UPDATE config SET data = :value WHERE name = 'swiftmailer.transport'";
    $db->query($q, ['value' => $value]);
  }

  /**
   * @command app:notifications
   */
  public function notifications() {
    EventUtils::notificationsUnregisteredEventOptions();
  }

}
