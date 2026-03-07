<?php

declare(strict_types=1);

namespace Drupal\generated_content_example1\Plugin\GeneratedContent;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;

/**
 * Generates file entities.
 */
#[GeneratedContent(id: 'example1_file_file', entity_type: 'file', bundle: 'file', weight: -10)]
class File extends GeneratedContentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function generate(): array {
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
          $filename = sprintf('Demo %s %s file %s %s', $generation_type, $asset_type, $i + 1, $this->helper::randomName(4));
          $file = $this->helper->createFile($asset_type, [
            'filename' => str_replace(' ', '_', $filename),
          ], (string) $generation_type);

          $link_options = ['attributes' => ['target' => '_blank']];
          $this->helper::log(
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

}
