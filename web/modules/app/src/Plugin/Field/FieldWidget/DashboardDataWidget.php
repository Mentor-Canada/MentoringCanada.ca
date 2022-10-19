<?php

namespace Drupal\app\Plugin\Field\FieldWidget;

use Drupal\app\Plugin\Field\FieldType\DashboardDataItem;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "dashboard_data_widget",
 *   label = @Translation("Dashboard Data Widget"),
 *   field_types = {
 *     "dashboard_data_item"
 *   },
 * )
 */
class DashboardDataWidget extends WidgetBase implements WidgetInterface {

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['#prefix'] = "<div class='dashboard-data-widget'>";
    foreach(DashboardDataItem::$columns as $name) {
      $element[$name] = [
        '#type' => 'textfield',
        '#title' => $name,
        '#default_value' => $items[$delta]->$name
      ];
    }
    return $element;
  }
}
