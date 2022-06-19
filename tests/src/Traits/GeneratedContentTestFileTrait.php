<?php

namespace Drupal\Tests\generated_content\Traits;

/**
 * Trait GeneratedContentTestImageTrait.
 *
 * Trait with file-related helpers.
 *
 * @package Drupal\generated_content\Tests
 */
trait GeneratedContentTestFileTrait {

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

}
