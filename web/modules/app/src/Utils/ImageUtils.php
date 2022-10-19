<?php

namespace Drupal\app\Utils;

use Drupal\image\Entity\ImageStyle;

class ImageUtils {

  public static function getImageFromFile(\Drupal\file\Entity\File $image, $width = null, $height = null) {
    $uri = $image->getFileUri();
    return ImageUtils::getImageFromUri($uri, $width, $height);
  }

  public static function getImage(\Drupal\file\Entity\File $image, $width = null, $height = null) {
    return ImageUtils::getImageFromFile($image, $width, $height);
  }

  public static function getImageFromUri($uri, $width = null, $height = null) {

    $imageFactory = \Drupal::service('image.factory');

    $originalUri = $uri;
    $originalUrl = file_create_url($originalUri);
    $originalWidth = $imageFactory->get($originalUri)->getWidth();
    $originalHeight = $imageFactory->get($originalUri)->getHeight();

    $returnOriginal = false;
    if(is_null($width) && is_null($height)) $returnOriginal = true;
    if($width >= $originalWidth && $height >= $originalHeight) $returnOriginal = true;
    if($width >= $originalWidth && is_null($height)) $returnOriginal = true;
    if($height >= $originalHeight && is_null($width)) $returnOriginal = true;
    if($returnOriginal) {
      return [
        'uri'     => $originalUri,
        'url'     => $originalUrl,
        'width'   => $originalWidth,
        'height'  => $originalHeight
      ];
    }

    $wString = $width ?: "X";
    $hString = $height ?: "Y";
    $styleName = "image_utils_scale_" . $wString . "_" . $hString;
    $imageStyle = ImageStyle::load($styleName);
    if(is_null($imageStyle)) {
      ImageUtils::createStyle($styleName, $width, $height);
      $imageStyle = ImageStyle::load($styleName);
    }

    $imageUri = $imageStyle->buildUri($originalUri);
    $imageUrl = $imageStyle->buildUrl($originalUri);
    $factory = $imageFactory->get($imageUri);
    if(!$factory->isValid()) {
      $imageStyle->createDerivative($originalUri, $imageUri);
    }
    $imageWidth = $imageFactory->get($imageUri)->getWidth();
    $imageHeight = $imageFactory->get($imageUri)->getHeight();

    return [
      'uri'     => $imageUri,
      'url'     => $imageUrl,
      'width'   => $imageWidth,
      'height'  => $imageHeight
    ];
  }

  private static function createStyle($name, $width = null, $height = null) {
    /* @var $style \Drupal\image\Entity\ImageStyle */
    /* @var $effect \Drupal\image\Plugin\ImageEffect\ScaleImageEffect */

    if(!is_null($width) && !is_null($height)) $effectLabel = "Scale " . $width . "×" . $height;
    if(is_null($width)) $effectLabel = "Scale height $height";
    if(is_null($height)) $effectLabel = "Scale width $width";
    $label = "Image Utils ($effectLabel)";
    $style = ImageStyle::create(array('name' => $name, 'label' => $label));

    $configuration = [
      'uuid' => NULL,
      'id' => 'image_scale',
      'weight' => 0,
      'data' => [
        'width' => $width,
        'height' => $height,
      ]
    ];
    $effect = \Drupal::service('plugin.manager.image.effect')->createInstance($configuration['id'], $configuration);

    $style->addImageEffect($effect->getConfiguration());
    $style->save();
  }

}
