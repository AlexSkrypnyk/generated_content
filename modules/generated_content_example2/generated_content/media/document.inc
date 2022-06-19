<?php

/**
 * @file
 * Create generated images.
 */

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content_example2\GeneratedContentExample2AssetGenerator;
use Drupal\generated_content_example2\GeneratedContentExample2Helper;
use Drupal\media\Entity\Media;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example2_generated_content_create_media_document_weight() {
  return 2;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example2_generated_content_create_media_document_tracking() {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example2_generated_content_create_media_document() {
  /** @var \Drupal\generated_content_example2\GeneratedContentExample2Helper $helper */
  $helper = GeneratedContentExample2Helper::getInstance();

  $total_files_count = 10;

  $entities = [];
  for ($i = 0; $i < $total_files_count; $i++) {
    $filename = sprintf('Demo Document media %s %s', $i + 1, $helper::randomName());
    if ($i % 3) {
      $file_type = $helper::randomArrayItem([
        GeneratedContentAssetGenerator::ASSET_TYPE_PDF,
        GeneratedContentAssetGenerator::ASSET_TYPE_DOCX,
      ]);
      $file = $helper->createFile($file_type, [
        'filename' => str_replace(' ', '_', $filename),
      ], GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC);
    }
    elseif ($i % 3 == 1) {
      $file = $helper->createFile(GeneratedContentAssetGenerator::ASSET_TYPE_TXT, [
        'filename' => str_replace(' ', '_', $filename),
        'content' => 'Contents of ' . $filename,
      ]);
    }
    else {
      $file = $helper->createFile(GeneratedContentExample2AssetGenerator::ASSET_TYPE_RTF, [
        'filename' => str_replace(' ', '_', $filename),
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
