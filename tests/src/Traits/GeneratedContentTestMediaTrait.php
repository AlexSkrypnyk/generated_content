<?php

namespace Drupal\Tests\generated_content\Traits;

use Drupal\file\Entity\File;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Trait GeneratedContentTestMediaTrait.
 *
 * Trait with media-related helpers.
 *
 * @package Drupal\generated_content\Tests
 */
trait GeneratedContentTestMediaTrait {

  use TestFileCreationTrait;
  use MediaTypeCreationTrait;

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
    $this->installEntitySchema('media');
    $this->installEntitySchema('file');
    $this->installSchema('file', 'file_usage');
    $this->installConfig('image');
    $this->installConfig('media');

    $this->mediaTypes[] = 'image';
    $this->mediaTypes[] = 'document';
  }

  /**
   * Prepare medias to be used in tests.
   */
  protected function prepareMediaItems($count, $bundles = NULL, $single_bundle = FALSE) {
    $bundles = $bundles ?? $this->mediaTypes;

    $medias = [];

    $bundle = $bundles[0];
    $image = File::create([
      'uri' => $this->getTestFiles('image')[0]->uri,
    ]);
    $image->save();
    $this->createMediaType('image', ['id' => $bundle]);
    for ($i = 0; $i < $count; $i++) {
      $media = $this->container->get('entity_type.manager')->getStorage('media')->create([
        'bundle' => $bundle,
        'name' => sprintf('Media %s of bundle %s.', $i + 1, $bundle),
        'field_media_image' => [
          [
            'target_id' => $image->id(),
            'alt' => 'default alt',
            'title' => 'default title',
          ],
        ],
      ]);
      $media->save();
      $medias[$bundle][$media->id()] = $media;
    }

    $bundle = $bundles[1];
    $file = File::create([
      'uri' => $this->getTestFiles('binary')[0]->uri,
    ]);
    $file->save();
    $this->createMediaType('file', ['id' => $bundle]);
    for ($i = 0; $i < $count; $i++) {
      $media = $this->container->get('entity_type.manager')->getStorage('media')->create([
        'bundle' => $bundle,
        'name' => sprintf('Media %s of bundle %s.', $i + 1, $bundle),
        'field_media_file' => [
          [
            'target_id' => $file->id(),
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
