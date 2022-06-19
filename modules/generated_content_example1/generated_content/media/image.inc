<?php

/**
 * @file
 * Create generated images.
 */

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\media\Entity\Media;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example1_generated_content_create_media_image_weight() {
  return 0;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example1_generated_content_create_media_image_tracking() {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example1_generated_content_create_media_image() {
  /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
  $helper = GeneratedContentHelper::getInstance();

  $total_files_count = 10;

  $entities = [];
  for ($i = 0; $i < $total_files_count; $i++) {
    if ($i % 2) {
      $file_type = $helper::randomArrayItem([
        GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
        GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
      ]);
      $filename = sprintf('Demo Image media %s %s', $i + 1, $helper::randomName());
      $file = $helper->createFile($file_type, [
        'filename' => str_replace(' ', '_', $filename),
        'width' => 2000,
        'height' => 1000,
      ]);
    }
    else {
      $file_type = $helper::randomArrayItem([
        GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
        GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
      ]);
      $filename = sprintf('Demo Image media %s %s', $i + 1, $helper::randomName());
      $file = $helper->createFile($file_type, [
        'filename' => str_replace(' ', '_', $filename),
      ], GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC);
    }

    $media = Media::create([
      'bundle' => 'image',
      'name' => $filename,
    ]);

    $media->field_media_image->setValue($file->id());
    $media->field_media_image->alt = sprintf('Alt for %s', $filename);
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
