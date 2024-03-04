<?php

declare(strict_types=1);

namespace Drupal\generated_content_example2;

use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;

/**
 * Class GeneratedContentExample2AssetGenerator.
 *
 * Example of the extending of the asset generator with a custom generator.
 *
 * @package Drupal\generated_content
 */
class GeneratedContentExample2AssetGenerator extends GeneratedContentAssetGenerator {

  /**
   * Defines RTF asset type.
   */
  const ASSET_TYPE_RTF = 'rtf';

  /**
   * {@inheritdoc}
   */
  protected static function generatorMap(): array {
    $map = parent::generatorMap();

    $map[static::GENERATE_TYPE_RANDOM][static::ASSET_TYPE_RTF] = [
      static::class,
      'generatorContentFileRtf',
    ];

    return $map;
  }

  /**
   * Generate an RTF file with content.
   *
   * @param string $type
   *   File type.
   * @param array<mixed> $options
   *   Array of options for this generator:
   *   - content: (string) The content of the file.
   *
   * @return string
   *   Real path to generated file.
   */
  public function generatorContentFileRtf(string $type, array $options = []): string {
    $options += [
      'content' => 'Placeholder RTF text',
    ];

    return $this->generatorContentFile($type, $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function getAssetsDirs(): array {
    $module_path = $this->moduleExtensionList->getPath('generated_content_example2');

    return array_merge(parent::getAssetsDirs(), [
      $module_path . DIRECTORY_SEPARATOR . rtrim(static::ASSETS_DIRECTORY, DIRECTORY_SEPARATOR),
    ]);
  }

}
