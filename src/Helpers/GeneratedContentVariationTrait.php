<?php

namespace Drupal\generated_content\Helpers;

/**
 * Trait GeneratedContentVariationTrait.
 *
 * Handle all variation helpers in one place.
 *
 * Variations allow to provide sets of data to be then passed to field converter
 * for bulk content creation.
 *
 * @package Drupal\generated_content
 */
trait GeneratedContentVariationTrait {

  /**
   * Generate random variation structure.
   *
   * @return array
   *   Variation structure.
   */
  public static function variationRandom($variation) {
    foreach ($variation as $name => $value) {
      // NULL values are considered random bool.
      if (is_null($value)) {
        // Skew random results to generate more content.
        $variation[$name] = mt_rand(0, 100) > 33;
      }
      elseif (is_array($value)) {
        shuffle($value);
        $variation[$name] = reset($value);
      }
    }

    return $variation;
  }

  /**
   * Format variation as a string.
   */
  public static function variationFormatInfo($variation, $name_length = 3) {
    $output = [];

    foreach ($variation as $name => $value) {
      $value_string = 'N';

      if (is_array($value)) {
        $value_string = count($value);
      }
      elseif (is_int($value)) {
        $value_string = $value === 0 ? 'N' : $value;
      }
      elseif (is_string($value)) {
        $value = strip_tags($value);
        $value_string = trim(substr($value, 0, 3));
      }
      elseif ($value) {
        $value_string = 'Y';
      }

      $name = str_replace([' ', '-', '_'], '', $name);
      $name = substr($name, 0, $name_length);
      $name = ucfirst($name);

      $output[] = sprintf('%s: %s', $name, $value_string);
    }

    return implode(', ', $output);
  }

}
