<?php

namespace Drupal\app\Views {

  use Drupal\app\Models\EventSubmissionEntity;
  use Drupal\app\Models\MentorConnectorKit;
  use Drupal\app\Utils\EventUtils;
  use Drupal\app\Utils\Utils;
  use Drupal\Core\Locale\CountryManager;

  class EventFormView {

    /* @var $entity EventSubmissionEntity */
    public $entity;
    public $eventId;
    public $eventOptions;
    public $eventOptionsAvailable;
    public $eventOptionsGroupedMonthly;
    public $eventOptionsGroupedByTraining;
    public $paypalClientId;

    public $hasRole;
    public $hasTitle;
    public $hasPhoneNumber;
    public $hasPostalCode;
    public $hasCountry;
    public $hasAge;
    public $hasBirthdate;
    public $hasIndigenous;
    public $hasLinkedinProfile;
    public $hasComments;
    public $hasPhotoVideoConsent;
    public $hasSharingConsent;
    public $hasPayment;

    public $commentsIntro;
    public $commentsLabel;

    public $provinceSelectOptions;
    public $countrySelectOptions;
    public $indigenousSelectOptions;

    public $oneOnOneOptions;

    public $groupLabel;
    public $oneOnOneLabel;

    public $eventType;

    public $paymentAmount;

    public $mentorConnectorPrograms = [];
    public $mentorConnectorDropdownLabel = "";

    public function __construct(\Drupal\node\Entity\Node $node, $eventOptions) {

      $entity = new EventSubmissionEntity();
      $this->entity = $entity;
      $this->eventId = $node->id();
      $this->paypalClientId = $_ENV['PAYPAL_CLIENT_ID'] ?: "ARU2Hzq8E8w0fwxLjUYkpE9f6q6Q_ebLdAtyElwVKXya71TamAJKVo3B25HwnM8sYhef1nEIR5xusVXC";

      /** @var $eventOptions \Drupal\App\Models\EventOptionEntity[] */
      $this->eventOptions = $eventOptions;
      $this->eventOptionsAvailable = EventUtils::getAvailableEventOptions($this->eventOptions);
      $this->eventOptionsGroupedMonthly = EventUtils::groupEventOptionsByMonth($this->eventOptionsAvailable);
      $this->eventOptionsGroupedByTraining = EventUtils::groupEventOptionsByTraining($this->eventOptionsAvailable);

      $typesTermLookup = [
        "1" =>  "event",
        "2" =>  "event_pom",
        "3" =>  "event_training"
      ];
      $this->eventType = $typesTermLookup[$node->get('field_event_type')->getValue()[0]['target_id']];


      $this->hasRole = $node->get("field_has_form_role")->getValue()[0]["value"];
      $this->hasTitle = $node->get("field_has_form_title")->getValue()[0]["value"];
      $this->hasPhoneNumber = $node->get("field_has_form_phone_number")->getValue()[0]["value"];
      $this->hasPostalCode = $node->get("field_has_form_postal_code")->getValue()[0]["value"];
      $this->hasCountry = $node->get("field_has_form_country")->getValue()[0]["value"];
      $this->hasAge = $node->get("field_has_form_age")->getValue()[0]["value"];
      $this->hasBirthdate = $node->get("field_has_form_birthdate")->getValue()[0]["value"];
      $this->hasIndigenous = $node->get("field_has_form_indigenous")->getValue()[0]["value"];
      $this->hasLinkedinProfile = $node->get("field_has_form_linkedin_profile")->getValue()[0]["value"];
      $this->hasComments = $node->get("field_has_form_comments")->getValue()[0]["value"];
      $this->hasPhotoVideoConsent = $node->get("field_has_form_pho_vid_consent")->getValue()[0]["value"];
      $this->hasSharingConsent = $node->get("field_has_sharing_consent")->getValue()[0]["value"];

      $this->commentsIntro = $node->get("field_comments_intro")->getValue()[0]["value"] ?: null;
      $this->commentsLabel = $node->get("field_comments_label")->getValue()[0]["value"] ?: t("Comments")->__toString();

      $provinces = [
        "ON" => t("Ontario")->__toString(),
        "QC" => t("Quebec")->__toString(),
        "NS" => t("Nova Scotia")->__toString(),
        "NB" => t("New Brunswick")->__toString(),
        "MB" => t("Manitoba")->__toString(),
        "BC" => t("British Columbia")->__toString(),
        "PE" => t("Prince Edward Island")->__toString(),
        "SK" => t("Saskatchewan")->__toString(),
        "AB" => t("Alberta")->__toString(),
        "NL" => t("Newfoundland and Labrador")->__toString(),
        "NT" => t("Northwest Territories")->__toString(),
        "YT" => t("Yukon")->__toString(),
        "NU" => t("Nunavut")->__toString()
      ];
      $provinces = Utils::sort($provinces);
      $this->provinceSelectOptions = $provinces;

      $countries = [];
      $countryList = CountryManager::getStandardList();
      /** @var $country \Drupal\Core\StringTranslation\TranslatableMarkup */
      foreach($countryList as $country) {
        if($country->getUntranslatedString() == "Canada") continue;
        $countries[$country->getUntranslatedString()] = $country->render();
      }
      $countries = Utils::sort($countries);
      $this->countrySelectOptions = $countries;

      $indigenousOptions = [
        "First Nation"          => t("First Nation")->__toString(),
        "Metis"                 => t("Metis")->__toString(),
        "Inuit"                 => t("Inuit")->__toString(),
        "Non-status Indian"     => t("Non-status Indian")->__toString(),
        "Don’t wish to declare" => t("Don’t wish to declare")->__toString()
      ];
      $this->indigenousSelectOptions = $indigenousOptions;

      $oneOnOneOptions = [
        t("Program Administrator Functions")->__toString(),
        t("Organization / Program Settings")->__toString(),
        t("Program Policies and / or Mentor City Terms of Service")->__toString(),
        t("Mentoring Agreement")->__toString(),
        t("Profile Sections, Fields, Drop-Down Menus, Competencies")->__toString(),
        t("Resource Centre")->__toString(),
        t("Language Filters")->__toString(),
        t("Surveys and / or Program Evaluation")->__toString(),
        t("Badge Reward System")->__toString(),
        t("Courses and / or Activities")->__toString(),
        t("Onboarding of Mentors and Mentees")->__toString(),
        t("Matching Methods")->__toString(),
        t("Announcements")->__toString(),
        t("Events")->__toString(),
        t("Monitoring Mentoring Relationships', Progress and Interactions")->__toString(),
        t("Statistics & Reports")->__toString(),
        t("Sending messages to mentors & mentees")->__toString(),
        t("Other")->__toString(),
      ];
      $this->oneOnOneOptions = $oneOnOneOptions;

      $this->groupLabel = $node->get('field_group_training_label')->getValue()[0]['value'] ?: t("Group Training")->__toString();
      $this->oneOnOneLabel = $node->get('field_one_on_one_training_label')->getValue()[0]['value'] ?: t("1:1 Training & Support")->__toString();

      $payment = $node->get('field_payment')->getValue()[0];
      $this->hasPayment = $payment['payment'] == 1;
      $this->paymentAmount = number_format($payment['payment_amount'], 2);

      if($this->eventType == "event_training") {
        $mentorConnectorKit = new MentorConnectorKit();
        $this->mentorConnectorPrograms = $mentorConnectorKit->getProgramNameOptions();
        $this->mentorConnectorDropdownLabel = strval(t("Please select your program from the drop down menu below, as it appears in the Mentor Connector."));
      }

    }

    public function __toString(): string {
      $vars = [
        '#theme' => "event_form",
        '#v' => $this
      ];
      return strval(render($vars));
    }

  }

}
