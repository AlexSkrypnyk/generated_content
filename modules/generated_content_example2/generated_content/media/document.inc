<?php

/**
 * @file
 * Create generated images.
 */

declare(strict_types=1);

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content_example2\GeneratedContentExample2AssetGenerator;
use Drupal\generated_content_example2\GeneratedContentExample2Helper;
use Drupal\media\Entity\Media;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example2_generated_content_create_media_document_weight(): int {
  return 2;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example2_generated_content_create_media_document_tracking(): bool {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 *
 * @phpstan-ignore-next-line
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
function generated_content_example2_generated_content_create_media_document(): array {
  /** @var \Drupal\generated_content_example2\GeneratedContentExample2Helper $helper */
  $helper = GeneratedContentExample2Helper::getInstance();

  $total_media_count = 10;

  $entities = [];
  for ($i = 0; $i < $total_media_count; $i++) {
    $name = sprintf('Demo random Document media %s %s', $i + 1, $helper::randomName());
    $file = NULL;
    if ($i % 2) {
      $file_type = $helper::randomArrayItem([
        GeneratedContentAssetGenerator::ASSET_TYPE_PDF,
        GeneratedContentAssetGenerator::ASSET_TYPE_DOCX,
        GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
      ]);
      $file = $helper->randomFile($file_type);
    }
    else {
      // Example of the direct file creation for the format defined in the
      // custom generator.
      $file = $helper->createFile(GeneratedContentExample2AssetGenerator::ASSET_TYPE_RTF, [
        'filename' => str_replace(' ', '_', $name),
      ]);
    }

    if (!$file) {
      continue;
    }

    $media = Media::create([
      'bundle' => 'document',
      'name' => $name,
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
