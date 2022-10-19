<?php

namespace Drupal\datadashboard;

use Drupal\app\Controllers\BaseController;
use Drupal\app\Utils\NodeUtils;
use Drupal\app\Utils\ParagraphUtils;
use Drupal\app\Utils\ReferenceUtils;
use Drupal\app\Utils\Utils;
use Drupal\app\Views\BannerView;

abstract class DataDashboardControllerBase extends BaseController {

  /** @var $v \Drupal\datadashboard\DataDashboardPageView */
  private $v;

  public function preprocess_html(&$vars) {
    $this->v = new DataDashboardPageView();
    $this->v->banner = new BannerView();
    $node = NodeUtils::getNodeFromUrl();
    $this->v->banner = new BannerView($node);
    if($this->v->banner->hasImage) {
      $vars["classes"] .= " page-has-banner";
    }
    $this->v->banner->width = "full";
  }

  public function preprocess_page(&$vars) {
    $data = $this->getData();
    $this->v->accessToMentoringChartData = $data;
    $this->v->interface = $this->theme('datadashboard_chart_ui', [
      "tree" => $data['tree'],
      "prefix" => ""
    ]);
    $this->v->KPILabels = $this->getKPILabels();

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
    $this->v->sections = $sections;
    $this->v->banner->width = ParagraphUtils::getSectionAttributes($sections, "first")["width"];
    $this->v->children = ReferenceUtils::getPages(Utils::getChildrenByNid($node->id()));
    $this->v->title = $node->getTitle();

    $vars['v'] = $this->v;
  }

  function getData() {
    $tree = $this->getTree($this->getRows());
    $this->assignColors($tree->tree);

    return [
      'tree' => $tree->tree,
      'elements' => $tree->getFlat()
    ];
  }

  public function theme_suggestions_page_alter(&$suggestions, $vars) {
    return $suggestions = ["page__data_dashboard"];
  }

  protected function getRow($data) {
    $row = new DataDashboardRow();
    $row->region = $data[0];
    $row->factor = $data[1];
    $row->age = $data[2];
    $row->gender = $data[3];
    $row->number = $data[4];
    $row->percentage = $data[5];
    return $row;
  }

  private function adjustBrightness($hexCode, $adjustPercent) {
    $hexCode = ltrim($hexCode, '#');

    if (strlen($hexCode) == 3) {
      $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    $hexCode = array_map('hexdec', str_split($hexCode, 2));

    foreach ($hexCode as & $color) {
      $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
      $adjustAmount = ceil($adjustableLimit * $adjustPercent);

      $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }

    return '#' . implode($hexCode);
  }

  private function assignColors(&$tree) {

    $baseColors = [
      '#0051ff',
      '#5300bf',
      '#bf009c',
      '#e62600',
      '#ffbf00',
      '#60bf00',
      '#00bf7f',
      '#007fbf'
    ];

    $lvl1Pos = 0;
    foreach($tree as $groupLvl1) {
      $lvl2Pos = 0;
      foreach($groupLvl1->groups as $groupLvl2) {
        $lvl3Pos = 0;
        foreach($groupLvl2->groups as $groupLvl3) {
          foreach($groupLvl3->elements as $element) {
            $adjust = ($lvl2Pos * 3 + $lvl3Pos) * 7 / 100;
            $color = $this->adjustBrightness($baseColors[$lvl1Pos], $adjust);
            $element->color = $color;
          }
          $lvl3Pos++;
        }
        $lvl2Pos++;
      }
      $lvl1Pos++;
    }

  }

}
