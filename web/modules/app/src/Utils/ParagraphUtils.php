<?php

namespace Drupal\app\Utils;

use Drupal\image\Entity\ImageStyle;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\app\Views\ParagraphContentSectionView;
use Drupal\app\Views\ParagraphParallaxSectionView;
use Drupal\app\Views\ParagraphReferenceSectionView;
use Drupal\app\Views\ParagraphMarkupSectionView;
use Drupal\app\Views\ParagraphWysiwygBlockView;
use Drupal\app\Views\ParagraphTextImageBlockView;
use Drupal\app\Views\ParagraphImagesBlockView;
use Drupal\app\Views\ParagraphReferenceBlockView;
use Drupal\app\Views\ParagraphYoutubeBlockView;
use Drupal\app\Views\ParagraphSocialBlockView;
use Drupal\app\Views\ParagraphDividerBlockView;
use Drupal\app\Views\ParagraphMentorConnectorBlockView;
use Drupal\app\Views\ParagraphMarkupBlockView;

use Drupal\app\Views\ParagraphImageCaptionFieldView;

class ParagraphUtils {

  public static function getParagraph($target_id, \Drupal\node\Entity\Node $node, $theme = null) {
    $lang = Utils::lang();
    /** @var $p \Drupal\paragraphs\Entity\Paragraph */
    $p = Paragraph::load($target_id);
    if($p->hasTranslation($lang)) $p = $p->getTranslation($lang);

    $type = $p->getType($target_id);
    $typeComponents = explode("_", $type);
    $typeFunctionName = "get";
    foreach($typeComponents as $typeComponent) {
      $typeFunctionName .= ucfirst($typeComponent);
    }
    return ParagraphUtils::$typeFunctionName($p, $node, $theme);
  }

  public static function getContentSection(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphContentSectionView($p, $node, $theme);
  }

  public static function getParallaxSection(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphParallaxSectionView($p, $node, $theme);
  }

  public static function getReferenceSection(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphReferenceSectionView($p, $node, $theme);
  }

  public static function getMarkupSection(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphMarkupSectionView($p, $node, $theme);
  }

  public static function getWysiwygBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphWysiwygBlockView($p, $node, $theme);
  }

  public static function getTextImageBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphTextImageBlockView($p, $node, $theme);
  }

  public static function getImagesBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphImagesBlockView($p, $node, $theme);
  }

  public static function getYoutubeBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphYoutubeBlockView($p, $node, $theme);
  }

  public static function getReferenceBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphReferenceBlockView($p, $node, $theme);
  }

  public static function getSocialBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphSocialBlockView($p, $node, $theme);
  }

  public static function getDividerBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphDividerBlockView($p, $node, $theme);
  }

  public static function getMentorConnectorBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphMentorConnectorBlockView($p, $node, $theme);
  }

  public static function getMarkupBlock(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphMarkupBlockView($p, $node, $theme);
  }


  public static function getImageCaptionField(\Drupal\paragraphs\Entity\Paragraph $p, \Drupal\node\Entity\Node $node, $theme) {
    return new ParagraphImageCaptionFieldView($p, $node, $theme);
  }

  public static function zebraSections($sections) {
    /** @var $section \Drupal\app\Views\ParagraphSectionView */
    $i = 0;
    foreach($sections as $section) {
      $sectionClass = get_class($section);
      if($sectionClass == "Drupal\app\Views\ParagraphParallaxSectionView") {
        $i = 0;
        continue;
      }
      if($sectionClass == "Drupal\app\Views\ParagraphMarkupSectionView") {
        if($section->ignoreDefaultZebraStriping) {
          $i = 0;
          continue;
        }
      }
      $section->zebra = $i % 2 == 0 ? "odd" : "even";
      $i++;
    }
    return $sections;
  }

  public static function getSectionAttributes($sections, $position = "first") {
    /** @var $section \Drupal\app\Views\ParagraphSectionView */
    if($position == "first") {
      $section = $sections[0];
    } else {
      $section = $sections[count($sections) - 1];
    }
    $sectionClass = get_class($section);
    $width = "standard";
    if($sectionClass == "Drupal\app\Views\ParagraphContentSectionView") {
      if($position == "first") {
        $block = $section->blocks[0];
      } else {
        $block = $section->blocks[count($section->blocks) - 1];
      }
      if($block) $width = $block->blockWidth;
    } elseif(
      $sectionClass == "Drupal\app\Views\ParagraphReferenceSectionView" ||
      $sectionClass == "Drupal\app\Views\ParagraphParallaxSectionView"  ||
      $sectionClass == "Drupal\app\Views\ParagraphMarkupSectionView"
    ) {
      $width = $section->sectionWidth;
    }
    if(
      $sectionClass == "Drupal\app\Views\ParagraphContentSectionView"   ||
      $sectionClass == "Drupal\app\Views\ParagraphReferenceSectionView" ||
      $sectionClass == "Drupal\app\Views\ParagraphMarkupSectionView"
    ) {
      if(is_null($section->zebra)){
        ParagraphUtils::zebraSections($sections);
      }
      $zebra = $section->zebra;
    } else {
      $zebra = "even";
    }
    return [
      "zebra" => $zebra,
      "width" => $width
    ];
  }

  public static function setParagraphFormStates(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {

    $type = $element['#paragraph_type'];

    $typeComponents = explode("_", $type);
    $typeFunctionName = "set";
    foreach($typeComponents as $typeComponent) {
      $typeFunctionName .= ucfirst($typeComponent);
    }
    $typeFunctionName .= "FormState";

    if(method_exists("Drupal\app\Utils\ParagraphUtils", $typeFunctionName)) {
      ParagraphUtils::$typeFunctionName($element, $form_state, $context);
    }

  }

  public static function setMenuTitleFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    $selector = 'input[name="' . ParagraphUtils::getSubformName($element, "field_display_in_menus") . '[value]"]';
    $element['subform']['field_section_menu_title']['#states'] = [
      'visible' => [
        $selector => ['checked' => true],
      ],
    ];
  }

  public static function setReferenceCommonFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    $selectorEvents = 'input[name="' . ParagraphUtils::getSubformName($element, "field_ref_events") . '[value]"]';
    $selectorNews = 'input[name="' . ParagraphUtils::getSubformName($element, "field_ref_news") . '[value]"]';
    $selectorTeam = 'input[name="' . ParagraphUtils::getSubformName($element, "field_ref_team_members") . '[value]"]';
    $selectorEventsDynamic = 'input[name="' . ParagraphUtils::getSubformName($element, "field_refd_events") . '[value]"]';
    $selectorNewsDynamic = 'input[name="' . ParagraphUtils::getSubformName($element, "field_refd_news") . '[value]"]';
    $selectorTeamDynamic = 'input[name="' . ParagraphUtils::getSubformName($element, "field_refd_team_members") . '[value]"]';

    // Disable team members if events or news are selected
    $element['subform']['field_ref_team_members']['#states'] = [
      'disabled' => [
        [$selectorEvents => ['checked' => true]],
        'OR',
        [$selectorNews => ['checked' => true]],
      ],
    ];

    // Disable events and news if team members is selected
    $element['subform']['field_ref_events']['#states'] = [
      'disabled' => [
        $selectorTeam => ['checked' => true],
      ],
    ];
    $element['subform']['field_ref_news']['#states'] = [
      'disabled' => [
        $selectorTeam => ['checked' => true],
      ],
    ];


    // EVENTS

    // Show common event options (dynamic and label) when events are selected
    $element['subform']['field_refd_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
      ],
    ];
    $element['subform']['field_refl_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
      ],
    ];

    // Show events promoted and type options if events is shown AND dynamic is true
    $element['subform']['field_refp_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
        $selectorEventsDynamic => ['!checked' => false],
      ],
    ];
    $element['subform']['field_reft_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
        $selectorEventsDynamic => ['!checked' => false],
      ],
    ];

    // Show event multi-select when dynamic is false
    $element['subform']['field_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
        $selectorEventsDynamic => ['checked' => false],
      ],
    ];

    // NEWS

    // Show common news options (dynamic and label) when news are selected
    $element['subform']['field_refd_news']['#states'] = [
      'visible' => [
        $selectorNews => ['checked' => true],
      ],
    ];
    $element['subform']['field_refl_news']['#states'] = [
      'visible' => [
        $selectorNews => ['checked' => true],
      ],
    ];

    // Show news promoted option if news is shown AND dynamic is true
    $element['subform']['field_refp_news']['#states'] = [
      'visible' => [
        $selectorNews => ['checked' => true],
        $selectorNewsDynamic => ['!checked' => false],
      ],
    ];

    // Show news multi-select when dynamic is false
    $element['subform']['field_news']['#states'] = [
      'visible' => [
        $selectorNews => ['checked' => true],
        $selectorNewsDynamic => ['checked' => false],
      ],
    ];

    // TEAM MEMBERS

    // Show common team options (dynamic) when team are selected
    $element['subform']['field_refd_team_members']['#states'] = [
      'visible' => [
        $selectorTeam => ['checked' => true],
      ],
    ];

    // Show team multi-select when dynamic is false
    $element['subform']['field_team_members']['#states'] = [
      'visible' => [
        $selectorTeam => ['checked' => true],
        $selectorTeamDynamic => ['checked' => false],
      ],
    ];
  }

  public static function getReferenceItemsFromParagraph(\Drupal\paragraphs\Entity\Paragraph $p) {
    $references = [];
    $fields = $p->getFields();
    foreach($fields as $fieldName => $value) {
      if (strpos($fieldName, "field_ref_") === 0) {
        $v = $p->get($fieldName)->getValue()[0]["value"];
        if($v) {
          $itemTypeRaw = str_replace("field_ref_", "", $fieldName);

          // get function name
          $itemTypeFunctionName = "get";
          $itemType = explode("_", $itemTypeRaw);
          foreach($itemType as $fragment) {
            $itemTypeFunctionName .= ucfirst($fragment);
          }

          $args = [];

          // get nids field
          $dynamicItemsField = "field_refd_$itemTypeRaw";
          if($p->hasField($dynamicItemsField)) {
            $dynamicItemsSelected = $p->get($dynamicItemsField)->getValue()[0]["value"];
            if(!$dynamicItemsSelected) {
              $itemsField = "field_$itemTypeRaw";
              if($p->hasField($itemsField)) {
                $nidsToReturn = [];
                foreach($p->get($itemsField)->getValue() as $item) {
                  $nidsToReturn[] = $item["target_id"];
                }
                $args["nidsToReturn"] = $nidsToReturn;
              }
            }
          }

          // get promoted field
          $promotedField = "field_refp_$itemTypeRaw";
          if($p->hasField($promotedField)) {
            $promoted = $p->get($promotedField)->getValue()[0]["value"] ? true : false;
            $args["promoted"] = $promoted;
          }

          // get label field
          $labelField = "field_refl_$itemTypeRaw";
          if($p->hasField($labelField)) {
            $showTypeTip = $p->get($labelField)->getValue()[0]["value"] ? true : false;
            $args["showTypeTip"] = $showTypeTip;
          }

          // get type field
          $typeField = "field_reft_$itemTypeRaw";
          if($p->hasField($typeField)) {
            $types = [];
            foreach($p->get($typeField)->getValue() as $type) {
              $types[] = $type["target_id"];
            }
            $args["types"] = $types;
          }

          $result = ReferenceUtils::$itemTypeFunctionName($args);
          if(count($result)) $references[] = ReferenceUtils::$itemTypeFunctionName($args);

        }
      }
    }
    return $references;
  }

  public static function setContentSectionFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    ParagraphUtils::setMenuTitleFormState($element, $form_state, $context);
  }

  public static function setReferenceSectionFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    ParagraphUtils::setMenuTitleFormState($element, $form_state, $context);
    ParagraphUtils::setReferenceCommonFormState($element, $form_state, $context);
  }

  public static function setParallaxSectionFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {

    $selectorDynamicContentType = 'input[name="' . ParagraphUtils::getSubformName($element, "field_display_dynamic_content") . '"]';

    $selectorEvents = 'input[name="' . ParagraphUtils::getSubformName($element, "field_ref_events") . '[value]"]';
    $selectorNews = 'input[name="' . ParagraphUtils::getSubformName($element, "field_ref_news") . '[value]"]';
    $selectorEventsDynamic = 'input[name="' . ParagraphUtils::getSubformName($element, "field_refd_events") . '[value]"]';
    $selectorNewsDynamic = 'input[name="' . ParagraphUtils::getSubformName($element, "field_refd_news") . '[value]"]';

    // Display events and news if dynamic references are selected
    $element['subform']['field_ref_events']['#states'] = [
      'visible' => [
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];
    $element['subform']['field_ref_news']['#states'] = [
      'visible' => [
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];
    // Display width option if dynamic references or menu are selected
    $element['subform']['field_width_options_wide']['#states'] = [
      'visible' => [
        [$selectorDynamicContentType => ['value' => 'menu']],
        'OR',
        [$selectorDynamicContentType => ['value' => 'references']],
      ],
    ];
    // Display no results if dynamic references are selected
    $element['subform']['field_wysiwyg']['#states'] = [
      'visible' => [
        [$selectorDynamicContentType => ['value' => 'references']],
      ],
    ];

    // EVENTS

    // Show common event options (dynamic and label) when events are selected
    $element['subform']['field_refd_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];
    $element['subform']['field_refl_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];

    // Show events promoted and type options if events is shown AND dynamic is true
    $element['subform']['field_refp_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
        $selectorEventsDynamic => ['!checked' => false],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];
    $element['subform']['field_reft_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
        $selectorEventsDynamic => ['!checked' => false],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];

    // Show event multi-select when dynamic is false
    $element['subform']['field_events']['#states'] = [
      'visible' => [
        $selectorEvents => ['checked' => true],
        $selectorEventsDynamic => ['checked' => false],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];

    // NEWS

    // Show common news options (dynamic and label) when news are selected
    $element['subform']['field_refd_news']['#states'] = [
      'visible' => [
        $selectorNews => ['checked' => true],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];
    $element['subform']['field_refl_news']['#states'] = [
      'visible' => [
        $selectorNews => ['checked' => true],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];

    // Show news promoted option if news is shown AND dynamic is true
    $element['subform']['field_refp_news']['#states'] = [
      'visible' => [
        $selectorNews => ['checked' => true],
        $selectorNewsDynamic => ['!checked' => false],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];

    // Show news multi-select when dynamic is false
    $element['subform']['field_news']['#states'] = [
      'visible' => [
        $selectorNews => ['checked' => true],
        $selectorNewsDynamic => ['checked' => false],
        $selectorDynamicContentType => ['value' => 'references'],
      ],
    ];

  }

  public static function setMarkupSectionFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    ParagraphUtils::setMenuTitleFormState($element, $form_state, $context);
  }

  public static function setWysiwygBlockFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    $selectorColumns = 'select[name="' . ParagraphUtils::getSubformName($element, "field_number_of_columns") . '"]';
    $selectorBlockWidth = 'select[name="' . ParagraphUtils::getSubformName($element, "field_width_options_wide") . '"]';
    $selectorConstrainBlock = 'input[name="' . ParagraphUtils::getSubformName($element, "field_constrain_block_width") . '[value]"]';
    $element['subform']['field_wysiwyg_2']['#states'] = [
      'visible' => [
        $selectorColumns => ['value' => 'columns-2'],
      ],
    ];
    $element['subform']['field_column_split']['#states'] = [
      'visible' => [
        $selectorColumns => ['value' => 'columns-2'],
      ],
    ];
    $element['subform']['field_text_column_2_tip']['#states'] = [
      'visible' => [
        $selectorColumns => ['value' => 'columns-1'],
      ],
    ];
    $element['subform']['field_constrain_block_width']['#states'] = [
      'visible' => [
        $selectorColumns => ['!value' => 'columns-2'],
        $selectorBlockWidth => ['!value' => 'standard'],
      ],
    ];
    $element['subform']['field_wide_block_alignment']['#states'] = [
      'visible' => [
        $selectorColumns => ['!value' => 'columns-2'],
        $selectorBlockWidth => ['!value' => 'standard'],
        $selectorConstrainBlock => ['!checked' => false],
      ],
    ];
  }

  public static function setTextImageBlockFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    $selector = 'select[name="' . ParagraphUtils::getSubformName($element, "field_number_of_images") . '"]';
    $element['subform']['field_wysiwyg_2']['#states'] = [
      'visible' => [
        $selector => ['value' => 'images-2'],
      ],
    ];
    $element['subform']['field_image_2']['#states'] = [
      'visible' => [
        $selector => ['value' => 'images-2'],
      ],
    ];
    $element['subform']['field_text_image_group_2_tip']['#states'] = [
      'visible' => [
        $selector => ['value' => 'images-1'],
      ],
    ];

    $selector = 'select[name="' . ParagraphUtils::getSubformName($element, "field_image_position") . '"]';
    $element['subform']['field_vertical_alignment']['#states'] = [
      'visible' => [
        $selector => [
          ['value' => 'left'],
          ['value' => 'right']
        ],
      ],
    ];
    $element['subform']['field_image_width']['#states'] = [
      'visible' => [
        $selector => [
          ['value' => 'left'],
          ['value' => 'right']
        ],
      ],
    ];
    $element['subform']['field_horizontal_alignment_alt']['#states'] = [
      'visible' => [
        $selector => [
          ['value' => 'left'],
          ['value' => 'right']
        ],
      ],
    ];
    $element['subform']['field_text_alignment_alt']['#states'] = [
      'visible' => [
        $selector => [
          ['value' => 'left'],
          ['value' => 'right']
        ],
      ],
    ];
    $element['subform']['field_responsive_image_to_top']['#states'] = [
      'visible' => [
        $selector => [
          ['value' => 'right']
        ],
      ],
    ];
  }

  public static function setImagesBlockFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    $displayModeSelector = 'input[name="' . ParagraphUtils::getSubformName($element, "field_images_display_mode") . '"]';
    $carouselAutoplaySelector = 'input[name="' . ParagraphUtils::getSubformName($element, "field_carousel_autoplay") . '[value]"]';
    $carouselArrowsSelector = 'input[name="' . ParagraphUtils::getSubformName($element, "field_carousel_arrows") . '[value]"]';
    $gridLayoutSelector = 'input[name="' . ParagraphUtils::getSubformName($element, "field_grid_layout") . '"]';
    $gridFlexibleSelector = 'input[name="' . ParagraphUtils::getSubformName($element, "field_grid_flexible_rows") . '[value]"]';

    // Carousel
    $element['subform']['field_width_options_wide']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
      ],
    ];
    $element['subform']['field_carousel_transition']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
      ],
    ];
    $element['subform']['field_carousel_arrows']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
      ],
    ];
    $element['subform']['field_carousel_arrows_placement']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
        $carouselArrowsSelector => ['checked' => true]
      ],
    ];
    $element['subform']['field_carousel_arrows_persistent']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
        $carouselArrowsSelector => ['checked' => true]
      ],
    ];
    $element['subform']['field_carousel_autoplay']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
      ],
    ];
    $element['subform']['field_carousel_autoplay_speed']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
        $carouselAutoplaySelector => ['checked' => true]
      ],
    ];
    $element['subform']['field_carousel_dots']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
      ],
    ];
    $element['subform']['field_carousel_infinite']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'carousel'],
      ],
    ];

    // Grid
    $element['subform']['field_width_options_full']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'grid'],
      ],
    ];
    $element['subform']['field_horizontal_alignment']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'grid'],
      ],
    ];
    $element['subform']['field_vertical_alignment']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'grid'],
      ],
    ];
    $element['subform']['field_grid_layout']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'grid'],
      ],
    ];
    $element['subform']['field_grid_number_per_row']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'grid'],
        $gridLayoutSelector => ['value' => 'number']
      ],
    ];
    $element['subform']['field_grid_flexible_rows']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'grid'],
        $gridLayoutSelector => ['value' => 'number']
      ],
    ];
    $element['subform']['field_grid_reflow_width_percent']['#states'] = [
      'visible' => [
        $displayModeSelector => ['value' => 'grid'],
        $gridLayoutSelector => ['value' => 'number'],
        $gridFlexibleSelector => ['checked' => true]
      ],
    ];


  }

  public static function setReferenceBlockFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {
    ParagraphUtils::setReferenceCommonFormState($element, $form_state, $context);
  }

  public static function setDividerBlockFormState(&$element, \Drupal\Core\Form\FormStateInterface $form_state, $context) {

    $paddingTop = 'select[name="' . ParagraphUtils::getSubformName($element, "field_padding_top") . '"]';
    $paddingBottom = 'select[name="' . ParagraphUtils::getSubformName($element, "field_padding_bottom") . '"]';
    $divider = 'select[name="' . ParagraphUtils::getSubformName($element, "field_divider") . '"]';
    $dividerMinimal = 'select[name="' . ParagraphUtils::getSubformName($element, "field_divider_thickness_minimal") . '"]';
    $dividerVibrant = 'select[name="' . ParagraphUtils::getSubformName($element, "field_divider_thickness_vibrant") . '"]';

    $element['subform']['field_padding_top_pixels']['#states'] = [
      'visible' => [
        $paddingTop => ['value' => 'pixels'],
      ],
    ];

    $element['subform']['field_padding_bottom_pixels']['#states'] = [
      'visible' => [
        $paddingBottom => ['value' => 'pixels'],
      ],
    ];

    $element['subform']['field_divider_thickness_minimal']['#states'] = [
      'visible' => [
        $divider => ['value' => 'minimal'],
      ],
    ];

    $element['subform']['field_divider_thickness_vibrant']['#states'] = [
      'visible' => [
        $divider => ['value' => 'vibrant'],
      ],
    ];

    $element['subform']['field_divider_thickness_pixels']['#states'] = [
      'visible' => [
        [
          $divider => ['value' => 'minimal'],
          $dividerMinimal => ['value' => 'pixels'],
        ],
        'OR',
        [
          $divider => ['value' => 'vibrant'],
          $dividerVibrant => ['value' => 'pixels'],
        ],
      ],
    ];

  }


  public static function getSubformName($element, $controlField) {
    $name = "";
    foreach($element["subform"]["#parents"] as $key=>$fragment) {
      if($key) {
        $name .= "[" . strval($fragment) . "]";
      } else {
        $name .= $fragment;
      }
    }
    $name .= "[$controlField]";
    return $name;
  }

  public static function getImageDataFromFieldname($p, $fieldName) {
    /* @var $imageEntities \Drupal\Core\Field\EntityReferenceFieldItemList */
    $imageEntities = $p->get($fieldName)->referencedEntities();
    /* @var $imageEntity \Drupal\media\Entity\Media */
    $imageEntity = $imageEntities[0];
    /* @var $imageFile \Drupal\file\Entity\File */
    if($imageEntity) {
      $imageFile = $imageEntity->get("field_media_image")->entity;

      $originalUri = $imageFile->getFileUri();
      $imageStyle = ImageStyle::load('image_1200_max_width');
      $imageUri = $imageStyle->buildUri($originalUri);
      $imageUrl = $imageStyle->buildUrl($originalUri);
      $imageFactory = \Drupal::service('image.factory')->get($imageUri);
      if(!$imageFactory->isValid()) {
        $imageStyle->createDerivative($originalUri, $imageUri);
        $imageFactory = \Drupal::service('image.factory')->get($imageUri);
      }

      $imageWidth = $imageFactory->getWidth();
      $imageHeight = $imageFactory->getHeight();

      $imageData = [
        "url"     => $imageUrl,
        "width"   => $imageWidth,
        "height"  => $imageHeight
      ];
    }
    return $imageData;
  }



}
