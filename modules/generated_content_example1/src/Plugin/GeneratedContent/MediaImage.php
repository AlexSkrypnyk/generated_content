<?php

declare(strict_types=1);

namespace Drupal\generated_content_example1\Plugin\GeneratedContent;

use Drupal\Core\Link;
use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;
use Drupal\media\Entity\Media;

/**
 * Generates media image entities.
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
#[GeneratedContent(id: 'example1_media_image', entity_type: 'media', bundle: 'image', weight: 1)]
class MediaImage extends GeneratedContentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function generate(): array {
    $total_media_count = 10;

    $entities = [];
    for ($i = 0; $i < $total_media_count; $i++) {
      if ($i % 2) {
        $file_type = $this->helper::randomArrayItem([
          GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
          GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
        ]);
        $file = $this->helper->randomFile($file_type);
        $name = sprintf('Demo random Image media %s %s', $i + 1, $this->helper::randomName());
      }
      else {
        $file = $this->helper->staticFile(GeneratedContentAssetGenerator::ASSET_TYPE_PNG);
        $name = sprintf('Demo static Image media %s %s', $i + 1, $this->helper::randomName());
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

      $this->helper::log(
        'Created media Image "%s" [ID: %s] %s',
        Link::createFromRoute($media->getName(), 'entity.media.canonical', ['media' => $media->id()])->toString(),
        $media->id(),
        Link::createFromRoute('Edit', 'entity.media.edit_form', ['media' => $media->id()])->toString()
      );

      $entities[$media->id()] = $media;
    }

    return $entities;
  }

}
