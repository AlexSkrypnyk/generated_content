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
function generated_content_example1_generated_content_create_media_document_weight() {
  return 0;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example1_generated_content_create_media_document_tracking() {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example1_generated_content_create_media_document() {
  /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
  $helper = GeneratedContentHelper::getInstance();

  $total_files_count = 10;

  $entities = [];
  for ($i = 0; $i < $total_files_count; $i++) {
    $filename = sprintf('Demo Document media %s %s', $i + 1, $helper::randomName());
    if ($i % 2) {
      /** @var \Drupal\file\Entity\File $file */
      $file = $helper->createFile($helper::FILE_TYPE_BINARY, [
        'filename' => str_replace(' ', '_', $filename),
        'extension' => $helper::randomArrayItem(['docx', 'pdf']),
      ]);
    }
    else {
      /** @var \Drupal\file\Entity\File $file */
      $file = $helper->createFile($helper::FILE_TYPE_OTHER, [
        'filename' => str_replace(' ', '_', $filename),
        'extension' => 'txt',
        'content' => 'Contents of ' . $filename,
      ]);
    }

    $media = Media::create([
      'bundle' => 'document',
      'name' => $filename,
    ]);

    $media->field_media_document->setValue($file->id());
    $media->save();

    $helper::log(
      'Created media Document "%s" [ID: %s] %s',
      Link::createFromRoute($media->getName(), 'entity.media.canonical', ['media' => $media->id()])->toString(),
      $media->id(),
      Link::createFromRoute('Edit', 'entity.media.edit_form', ['media' => $media->id()])->toString()
    );

    $entities[$media->id()] = $media;
  }

  return $entities;
}
