<?php

/**
 * @file
 * Create generated images.
 */

declare(strict_types = 1);

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\media\Entity\Media;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example1_generated_content_create_media_image_weight(): int {
  return 1;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example1_generated_content_create_media_image_tracking(): bool {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 *
 * @phpstan-ignore-next-line
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
function generated_content_example1_generated_content_create_media_image(): array {
  $helper = GeneratedContentHelper::getInstance();

  $total_media_count = 10;

  $entities = [];
  for ($i = 0; $i < $total_media_count; $i++) {
    if ($i % 2) {
      $file_type = $helper::randomArrayItem([
        GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
        GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
      ]);
      $file = $helper->randomFile($file_type);
      $name = sprintf('Demo random Image media %s %s', $i + 1, $helper::randomName());
    }
    else {
      $file = $helper->staticFile(GeneratedContentAssetGenerator::ASSET_TYPE_PNG);
      $name = sprintf('Demo static Image media %s %s', $i + 1, $helper::randomName());
    }

    if (!$file) {
      continue;
    }

    $media = Media::create([
      'bundle' => 'image',
      'name' => $name,
    ]);

    $media->field_media_image->setValue([
      'target_id' => $file->id(),
      'alt' => sprintf('Alt for %s', $name),
    ]);
    $media->save();

    $helper::log(
      'Created media Image "%s" [ID: %s] %s',
      Link::createFromRoute($media->getName(), 'entity.media.canonical', ['media' => $media->id()])->toString(),
      $media->id(),
      Link::createFromRoute('Edit', 'entity.media.edit_form', ['media' => $media->id()])->toString()
    );

    $entities[$media->id()] = $media;
  }

  return $entities;
}
