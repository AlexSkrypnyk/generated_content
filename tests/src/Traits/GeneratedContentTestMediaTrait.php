<?php

namespace Drupal\Tests\generated_content\Traits;

use Drupal\Tests\media\Traits\MediaTypeCreationTrait;

/**
 * Trait GeneratedContentTestMediaTrait.
 *
 * Trait with media-related helpers.
 *
 * @package Drupal\generated_content\Tests
 */
trait GeneratedContentTestMediaTrait {

  use MediaTypeCreationTrait;
  use GeneratedContentTestFileTrait;

  /**
   * Media types.
   *
   * @var string[]
   */
  protected $mediaTypes = [];

  /**
   * Test setup for media.
   */
  public function mediaSetUp() {
    $this->fileSetUp();

    $this->installEntitySchema('media');
    $this->installConfig('media');

    $this->mediaTypes[] = 'image';
    $this->mediaTypes[] = 'document';
  }

  /**
   * Prepare medias to be used in tests.
   */
  protected function prepareMediaItems($count, $bundles = NULL, $single_bundle = FALSE) {
    $bundles = $bundles ?? $this->mediaTypes;

    $files = $this->prepareFiles(2);

    $medias = [];

    $bundle = $bundles[0];
    $this->createMediaType('image', ['id' => $bundle]);
    for ($i = 0; $i < $count; $i++) {
      $media = $this->container->get('entity_type.manager')->getStorage('media')->create([
        'bundle' => $bundle,
        'name' => sprintf('Media %s of bundle %s.', $i + 1, $bundle),
        'field_media_image' => [
          [
            'target_id' => $files[0]->id(),
            'alt' => 'default alt',
            'title' => 'default title',
          ],
        ],
      ]);
      $media->save();
      $medias[$bundle][$media->id()] = $media;
    }

    $bundle = $bundles[1];
    $this->createMediaType('file', ['id' => $bundle]);
    for ($i = 0; $i < $count; $i++) {
      $media = $this->container->get('entity_type.manager')->getStorage('media')->create([
        'bundle' => $bundle,
        'name' => sprintf('Media %s of bundle %s.', $i + 1, $bundle),
        'field_media_file' => [
          [
            'target_id' => $files[1]->id(),
          ],
        ],
      ]);
      $media->save();
      $medias[$bundle][$media->id()] = $media;
    }

    if ($single_bundle) {
      $medias = reset($medias);
    }

    return $medias;
  }

}
