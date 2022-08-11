<?php

/**
 * @file
 * Create generated files.
 */

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Helpers\GeneratedContentHelper;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example1_generated_content_create_file_file_weight() {
  return -10;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example1_generated_content_create_file_file_tracking() {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example1_generated_content_create_file_file() {
  /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
  $helper = GeneratedContentHelper::getInstance();

  $total_files_count_per_type = 10;

  $entities = [];

  $file_generation_types = [
    GeneratedContentAssetGenerator:: GENERATE_TYPE_STATIC => 'static',
  ];

  $file_types = [
    GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
    GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
  ];

  foreach ($file_generation_types as $file_generation_type => $file_generation_type_name) {
    foreach ($file_types as $file_type) {
      for ($i = 0; $i < $total_files_count_per_type; $i++) {
        $filename = sprintf('Demo %s %s image file %s %s', $file_generation_type_name, $file_type, $i + 1, $helper::randomName(4));
        $file = $helper->createFile($file_type, [
          'filename' => str_replace(' ', '_', $filename),
        ], $file_generation_type);

        $link_options = ['attributes' => ['target' => '_blank']];
        $helper::log(
          'Created file "%s" [ID: %s]',
          Link::fromTextAndUrl($file->getFilename(), Url::fromUri($file->createFileUrl(FALSE), $link_options))->toString(),
          $file->id(),
        );
        $entities[$file->id()] = $file;
      }
    }
  }

  return $entities;
}
