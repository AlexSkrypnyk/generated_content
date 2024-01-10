<?php

declare(strict_types = 1);

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
  public function fileSetUp(): void {
    $this->installEntitySchema('file');
    $this->installSchema('file', 'file_usage');
    $this->installConfig('image');

    $this->fileExtensions[] = 'png';
    $this->fileExtensions[] = 'jpg';
  }

  /**
   * Assert image width.
   */
  public function assertImageWidth(string $file, int $width): void {
    $info = $this->imageGetInfo($file);
    $actual = !empty($info) ? $info['width'] : NULL;
    $this->assertSame($width, $actual);
  }

  /**
   * Assert image height.
   */
  public function assertImageHeight(string $file, int $height): void {
    $info = $this->imageGetInfo($file);
    $actual = !empty($info) ? $info['height'] : NULL;
    $this->assertSame($height, $actual);
  }

  /**
   * Assert file MIME type.
   */
  public function assertFileMimeType(string $file, string $type): void {
    /** @var \Symfony\Component\Mime\FileinfoMimeTypeGuesser $guesser */
    $guesser = $this->container->get('file.mime_type.guesser');

    $this->assertSame($type, $guesser->guessMimeType($file));
  }

  /**
   * Get image information.
   *
   * @param string $file
   *   File name.
   *
   * @return array{'width': int, 'height': int, 'type': int}|FALSE
   *   Image info or False.
   */
  protected function imageGetInfo(string $file) {
    $this->assertFileExists($file);

    $info = getimagesize($file);
    if (empty($info)) {
      return FALSE;
    }
    return [
      'width' => $info[0],
      'height' => $info[1],
      'type' => $info[2],
    ];
  }

  /**
   * Prepare files to be used in tests.
   *
   * @param int $count
   *   Count.
   * @param string|null $extension
   *   Extension.
   * @param string $type
   *   Type.
   *
   * @return \Drupal\file\Entity\File[]
   *   Files.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function prepareFiles(int $count, string $extension = NULL, string $type = 'image'): array {
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
      if ($file->id()) {
        $files[] = $file;
      }
    }

    return $files;
  }

}
