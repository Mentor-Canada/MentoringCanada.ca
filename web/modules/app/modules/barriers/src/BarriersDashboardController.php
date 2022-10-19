<?php

namespace Drupal\barriers;

use Drupal\app\Controllers\BaseController;
use Drupal\app\Utils\NodeUtils;
use Drupal\app\Utils\ParagraphUtils;
use Drupal\app\Utils\ReferenceUtils;
use Drupal\app\Utils\Utils;
use Drupal\app\Views\BannerView;
use Drupal\barriers\Breakdown\BreakdownChartController;
use Drupal\barriers\Overview\OverviewDashboardController;

class BarriersDashboardController extends BaseController {

  public static $path = "/^\/(?:state-of-mentoring-research-initiative\/dashboard|enquete-sur-letat-du-mentorat\/tableau-de-bord)\/barriers$/";

  public function preprocess_page(&$vars) {
    $v = new BarriersPageViewModel();
    $v->banner = new BannerView();
    $node = NodeUtils::getNodeFromUrl();
    $v->banner = new BannerView($node);
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }
    $v->banner->width = "full";

    $overviewDashboardController = new OverviewDashboardController();
    $overviewChartViewModel = new ChartViewModel();
    $overviewChartViewModel->data = $overviewDashboardController->getData();
    $overviewChartViewModel->interface = $this->theme('datadashboard_chart_ui', [
      "tree" => $overviewChartViewModel->data['tree'],
      "prefix" => "overview"
    ]);
    $overviewChartViewModel->KPILabels = $overviewDashboardController->getKPILabels();
    $v->overviewChart = $overviewChartViewModel;

    $breakdownController = new BreakdownChartController();
    $breakdownChartViewModel = new ChartViewModel();
    $breakdownChartViewModel->data = $breakdownController->getData();
    $breakdownChartViewModel->interface = $this->theme('datadashboard_chart_ui', [
      "tree" => $breakdownChartViewModel->data['tree'],
      "prefix" => "breakdown"
    ]);
    $breakdownChartViewModel->KPILabels = $breakdownController->getKPILabels();
    $v->breakdownChart = $breakdownChartViewModel;

    /** @var $node \Drupal\node\Entity\Node */
    $node = $vars["node"];
    $sectionValues = $node->get("field_sections")->getValue();
    $sections = [];
    foreach($sectionValues as $section) {
      $target_id = $section["target_id"];
      $p = ParagraphUtils::getParagraph($target_id, $node);
      $sections[] = $p;
    }
    $sections = ParagraphUtils::zebraSections($sections);
    $v->sections = $sections;
    $v->banner->width = ParagraphUtils::getSectionAttributes($sections, "first")["width"];
    $v->children = ReferenceUtils::getPages(Utils::getChildrenByNid($node->id()));
    $v->title = $node->getTitle();

    $vars['v'] = $v;
  }

  public function theme_suggestions_page_alter(&$suggestions, $vars) {
    return $suggestions = ["page__barriers_data_dashboard"];
  }

}
