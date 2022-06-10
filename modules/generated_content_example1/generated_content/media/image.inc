<?php

/**
 * @file
 * Create generated images.
 */

use Drupal\Core\Link;
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
    $filename = sprintf('Demo Image media %s %s', $i + 1, $helper::randomSentence());
    /** @var \Drupal\file\Entity\File $file */
    $file = $helper->createImage([
      'filename' => $filename,
      'width' => 2000,
      'height' => 1000,
      'format' => $helper::randomArrayItem(['png', 'jpg']),
    ]);

    $media = Media::create([
      'bundle' => 'image',
      'name' => $filename,
    ]);

    $media->field_media_image->setValue($file->id());
    $media->field_media_image->alt = sprintf('Alt for %s', $filename);
    $media->save();

    $helper::log(
      'Created media image for file "%s" [ID: %s] %s',
      Link::createFromRoute($file->getFilename(), 'entity.media.canonical', ['media' => $media->id()])->toString(),
      $media->id(),
      Link::createFromRoute('Edit', 'entity.media.edit_form', ['media' => $media->id()])->toString()
    );

    $entities[$media->id()] = $media;
  }

  return $entities;
}
