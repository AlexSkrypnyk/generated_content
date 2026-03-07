<?php

declare(strict_types=1);

namespace Drupal\generated_content_example2\Plugin\GeneratedContent;

use Drupal\Core\Link;
use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;
use Drupal\generated_content_example2\GeneratedContentExample2AssetGenerator;
use Drupal\media\Entity\Media;

/**
 * Generates document media entities.
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
#[GeneratedContent(id: 'example2_media_document', entity_type: 'media', bundle: 'document', weight: 2, helper: \Drupal\generated_content_example2\GeneratedContentExample2Helper::class)]
class MediaDocument extends GeneratedContentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function generate(): array {
    $total_media_count = 10;

    $entities = [];
    for ($i = 0; $i < $total_media_count; $i++) {
      $name = sprintf('Demo random Document media %s %s', $i + 1, $this->helper::randomName());
      $file = NULL;
      if ($i % 2) {
        $file_type = $this->helper::randomArrayItem([
          GeneratedContentAssetGenerator::ASSET_TYPE_PDF,
          GeneratedContentAssetGenerator::ASSET_TYPE_DOCX,
          GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
        ]);
        $file = $this->helper->randomFile($file_type);
      }
      else {
        // Example of the direct file creation for the format defined in the
        // custom generator.
        $file = $this->helper->createFile(GeneratedContentExample2AssetGenerator::ASSET_TYPE_RTF, [
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

      $this->helper::log(
        'Created media Document "%s" [ID: %s] %s',
        Link::createFromRoute($media->getName(), 'entity.media.canonical', ['media' => $media->id()])->toString(),
        $media->id(),
        Link::createFromRoute('Edit', 'entity.media.edit_form', ['media' => $media->id()])->toString()
      );

      $entities[$media->id()] = $media;
    }

    return $entities;
  }

}
