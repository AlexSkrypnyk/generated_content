<?php

/**
 * @file
 * Create generated files.
 */

declare(strict_types = 1);

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Helpers\GeneratedContentHelper;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example1_generated_content_create_file_file_weight(): int {
  return -10;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example1_generated_content_create_file_file_tracking(): bool {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 *
 * @phpstan-ignore-next-line
 */
function generated_content_example1_generated_content_create_file_file(): array {
  $helper = GeneratedContentHelper::getInstance();

  $total_files_count_per_type = 10;

  $entities = [];

  $generation_types = [
    GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM => [
      GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
      GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
      GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
    ],
    GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC => [
      GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
      GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
      GeneratedContentAssetGenerator::ASSET_TYPE_PDF,
      GeneratedContentAssetGenerator::ASSET_TYPE_DOCX,
    ],
  ];

  foreach ($generation_types as $generation_type => $asset_types) {
    foreach ($asset_types as $asset_type) {
      for ($i = 0; $i < $total_files_count_per_type; $i++) {
        $filename = sprintf('Demo %s %s file %s %s', $generation_type, $asset_type, $i + 1, $helper::randomName(4));
        $file = $helper->createFile($asset_type, [
          'filename' => str_replace(' ', '_', $filename),
        ], (string) $generation_type);

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
