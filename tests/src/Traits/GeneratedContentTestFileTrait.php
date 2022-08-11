<?php

namespace Drupal\Tests\generated_content\Traits;

use Drupal\file\Entity\File;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Trait GeneratedContentTestImageTrait.
 *
 * Trait with file-related helpers.
 *
 * @package Drupal\generated_content\Tests
 */
trait GeneratedContentTestFileTrait {

  use TestFileCreationTrait;

  /**
   * File extensions types.
   *
   * @var string[]
   */
  protected $fileExtensions = [];

  /**
   * Test setup for file.
   */
  public function fileSetUp() {
    $this->installEntitySchema('file');
    $this->installSchema('file', 'file_usage');
    $this->installConfig('image');

    $this->fileExtensions[] = 'png';
    $this->fileExtensions[] = 'jpg';
  }

  /**
   * Assert image width.
   */
  public function assertImageWidth($file, $width) {
    $this->assertSame($width, $this->imageGetInfo($file)['width']);
  }

  /**
   * Assert image height.
   */
  public function assertImageHeight($file, $height) {
    $this->assertSame($height, $this->imageGetInfo($file)['height']);
  }

  /**
   * Assert file MIME type.
   */
  public function assertFileMimeType($file, $type) {
    /** @var \Symfony\Component\Mime\FileinfoMimeTypeGuesser $guesser */
    $guesser = $this->container->get('file.mime_type.guesser');

    $this->assertSame($type, $guesser->guessMimeType($file));
  }

  /**
   * Get image information.
   */
  protected function imageGetInfo($file) {
    $this->assertFileExists($file);

    $info = getimagesize($file);

    return [
      'width' => $info[0],
      'height' => $info[1],
      'type' => $info[2],
    ];
  }

  /**
   * Prepare files to be used in tests.
   */
  protected function prepareFiles($count, $extension = NULL, $type = 'image') {
    $files = [];

    $test_assets = $this->getTestFiles($type);

    if ($extension) {
      foreach ($test_assets as $k => $test_asset) {
        // @phpstan-ignore-next-line
        $ext = pathinfo($test_asset->uri, PATHINFO_EXTENSION);
        if ($ext != $extension) {
          unset($test_assets[$k]);
        }
      }

      if (empty($test_assets)) {
        throw new \RuntimeException(sprintf('Unable to find a fixture file with extension "%s".', $extension));
      }
    }

    $test_asset_key = array_rand($test_assets);

    for ($i = 0; $i < $count; $i++) {
      $file = File::create([
        'name' => 'Fixture file ' . ($i + 1),
        // @phpstan-ignore-next-line
        'uri' => $test_assets[$test_asset_key]->uri,
      ]);
      $file->save();
      $files[] = $file;
    }

    return $files;
  }

}
