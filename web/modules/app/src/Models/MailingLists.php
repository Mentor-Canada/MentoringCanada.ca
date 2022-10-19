<?php

namespace Drupal\app\Models;

use DrewM\MailChimp\MailChimp;

class MailingLists {

  /* @var $mailchmp \DrewM\MailChimp\MailChimp */
  private $mailchimp;

  public function __construct() {
   $apiKey = getenv("MAILCHIMP_API_KEY");
    if($apiKey) {
      $this->mailchimp = new MailChimp($apiKey);
    }
  }

  public function addToNewsletter($email, $language, $name, $firstname = null, $lastname = null, $province = null, $postalCode = null, $organization = null) {
    if(!$this->mailchimp) {
      return;
    }
    $list_id = '7d099210dd';

    $data = [
      'email_address' => $email,
      'status' => 'subscribed',
      'merge_fields' => [
        'LANGUAGE' => $language,
        'NAME' => $name
      ]
    ];
    if($firstname) $data['merge_fields']['FNAME'] = $firstname;
    if($lastname) $data['merge_fields']['LNAME'] = $lastname;
    if($province) $data['merge_fields']['PROVINCE'] = $province;
    if($postalCode) $data['merge_fields']['POSTALCODE'] = $postalCode;
    if($organization) $data['merge_fields']['ORG'] = $organization;

    $result = $this->mailchimp->post("lists/$list_id/members", $data);

    if($result['status'] == 400) {
      $hash = MailChimp::subscriberHash($email);
      $result = $this->mailchimp->patch("lists/$list_id/members/$hash", $data);
    }

    return $this->mailchimp->getLastError() == false;
  }

  public function removeFromNewsletter($email) {
    if(!$this->mailchimp) {
      return;
    }
    $list_id = '7d099210dd';

    $data = [
      'email_address' => $email,
      'status' => 'unsubscribed',
    ];

    $result = $this->mailchimp->post("lists/$list_id/members", $data);

    if($result['status'] == 400) {
      $hash = MailChimp::subscriberHash($email);
      $result = $this->mailchimp->patch("lists/$list_id/members/$hash", $data);
    }

    return $this->mailchimp->getLastError() == false;
  }

  public function isSubscribedToNewsletter($email) {
    if(!$this->mailchimp) {
      return;
    }
    $list_id = '7d099210dd';

    $hash = MailChimp::subscriberHash($email);
    $result = $this->mailchimp->get("lists/$list_id/members/$hash");
    return $result['status'] == 'subscribed';
  }

}
