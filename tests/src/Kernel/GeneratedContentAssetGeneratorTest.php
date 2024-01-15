<?php

declare(strict_types = 1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestFileTrait;

/**
 * Tests GeneratedContentAssetGenerator class.
 *
 * @group generated_content
 */
class GeneratedContentAssetGeneratorTest extends GeneratedContentKernelTestBase {

  use GeneratedContentTestFileTrait;

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  protected static $modules = [
    'file',
    'image',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->fileSetUp();
  }

  /**
   * Tests generate() method with different parameters.
   *
   * @param string $type
   *   Type.
   * @param array<mixed> $options
   *   Options.
   * @param string $generation_type
   *   Type.
   * @param string|null $expected_uri
   *   Expected uri.
   * @param string|null $expected_exception_message
   *   Expected exception message.
   * @param bool $expected_exception_is_notice
   *   Expected exception is notice.
   *
   * @throws \Exception
   *
   * @dataProvider dataProviderGenerate
   */
  public function testGenerate(string $type, array $options = [], string $generation_type = GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM, string $expected_uri = NULL, string $expected_exception_message = NULL, bool $expected_exception_is_notice = FALSE): void {
    if ($expected_exception_message) {
      if ($expected_exception_is_notice) {
        set_error_handler(
          static function ($errno, $errstr) {
            restore_error_handler();
            throw new \Exception($errstr, $errno);
          },
          E_ALL
        );
      }
      $this->expectException(\Exception::class);
      $this->expectExceptionMessage($expected_exception_message);
    }

    /** @var \Drupal\generated_content\Helpers\GeneratedContentAssetGenerator $generator */
    $generator = $this->container->get('generated_content.asset_generator');

    $file = $generator->generate($type, $options, $generation_type);

    if (!is_null($expected_uri)) {
      $this->assertSame($expected_uri, $file->getFileUri());
    }
  }

  /**
   * Data provider for testGenerate().
   *
   * @return array<mixed>
   *   Test data.
   */
  public function dataProviderGenerate(): array {
    return [
      [GeneratedContentAssetGenerator::ASSET_TYPE_TXT],
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
        [
          'directory' => 'public://generated_content2',
        ],
      ],
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
        [
          'directory' => 'public://generated_content2',
          'filename' => 'testfile',
        ],
        GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM,
        'public://generated_content2/testfile.txt',
      ],
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
        [
          'directory' => 'public://generated_content2',
          'filename' => 'testfile',
          'extension' => 'log',
        ],
        GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM,
        'public://generated_content2/testfile.log',
      ],
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
        [
          'directory' => 'public://generated_content2',
          'filename' => 'testfile',
          'extension' => 'log',
          'use_existing' => FALSE,
        ],
        GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM,
        'public://generated_content2/testfile.log',
      ],

      // Empty paths.
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
        [
          'directory' => 'public://generated_content2',
          'filename' => '',
          'extension' => 'log',
        ],
        GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM,
        'public://generated_content2/.log',
      ],

      [
        GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
        [
          'directory' => '',
          'filename' => 'testfile',
          'extension' => 'log',
        ],
        GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM,
        NULL,
        'Provided destination directory is empty.',
      ],

      // Incompatible generation type and asset type.
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_TXT,
        [
          'directory' => 'public://generated_content2',
          'filename' => 'testfile',
          'extension' => 'log',
        ],
        GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC,
        'public://generated_content2/testfile.log',
        'Generator is not defined for "static" generation of "txt" type in Drupal\generated_content\Helpers\GeneratedContentAssetGenerator::generatorMap(). Using default generator Drupal\generated_content\Helpers\GeneratedContentAssetGenerator::generatorContentFile().',
        TRUE,
      ],
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_DOCX,
        [
          'directory' => 'public://generated_content2',
          'filename' => 'testfile',
          'extension' => 'log',
        ],
        GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM,
        'public://generated_content2/testfile.log',
        'Generator is not defined for "random" generation of "docx" type in Drupal\generated_content\Helpers\GeneratedContentAssetGenerator::generatorMap(). Using default generator Drupal\generated_content\Helpers\GeneratedContentAssetGenerator::generatorContentFile().',
        TRUE,
      ],

      // Custom type.
      [
        'custom',
        [
          'directory' => 'public://generated_content2',
          'filename' => 'testfile',
          'extension' => 'log',
        ],
        GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM,
        'public://generated_content2/testfile.log',
        'Generator is not defined for "random" generation of "custom" type in Drupal\generated_content\Helpers\GeneratedContentAssetGenerator::generatorMap(). Using default generator Drupal\generated_content\Helpers\GeneratedContentAssetGenerator::generatorContentFile().',
        TRUE,
      ],

      // Custom generation type.
      [
        'custom',
        [
          'directory' => 'public://generated_content2',
          'filename' => 'testfile',
          'extension' => 'log',
        ],
        'custom_generation',
        'public://generated_content2/testfile.log',
        'Generator is not defined for "custom_generation" generation of "custom" type in Drupal\generated_content\Helpers\GeneratedContentAssetGenerator::generatorMap(). Using default generator Drupal\generated_content\Helpers\GeneratedContentAssetGenerator::generatorContentFile().',
        TRUE,
      ],
    ];
  }

  /**
   * Tests generate() method with all types.
   *
   * @param string $generation_type
   *   Generation type.
   * @param string $type
   *   Type.
   * @param bool $expected_identical_content
   *   Expected identical content.
   *
   * @throws \Exception
   *
   * @dataProvider dataProviderGenerateTypes
   */
  public function testGenerateTypes(string $generation_type, string $type, bool $expected_identical_content = TRUE): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentAssetGenerator $generator */
    $generator = $this->container->get('generated_content.asset_generator');

    $file1 = $generator->generate($type, [], $generation_type);
    $uri1 = $file1->getFileUri();
    $this->assertStringStartsWith('public://generated_content', $uri1, 'URI starts with pre-set directory.');

    $file2 = $generator->generate($type, [], $generation_type);
    $uri2 = $file2->getFileUri();
    $this->assertStringStartsWith('public://generated_content', $uri2);

    if ($expected_identical_content) {
      $this->assertFileEquals($uri1, $uri2, '2 files with different filenames have the same content.');
    }
    else {
      $this->assertFileNotEquals($uri1, $uri2, '2 files with different filenames have the same content.');
    }

    // Since $options['filename'] was not provided - a random one will be
    // generated which will lead to a new file instance.
    // Re-using assets is tested in another test.
    $this->assertNotSame($file1->id(), $file2->id(), 'Re-generated files have different IDs.');
  }

  /**
   * Data provider for testGenerateTypes().
   *
   * @return array<mixed>
   *   Test data.
   */
  public function dataProviderGenerateTypes(): array {
    // phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
    return [
      [GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM, GeneratedContentAssetGenerator::ASSET_TYPE_TXT],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM, GeneratedContentAssetGenerator::ASSET_TYPE_PNG, FALSE],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM, GeneratedContentAssetGenerator::ASSET_TYPE_JPG, FALSE],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM, GeneratedContentAssetGenerator::ASSET_TYPE_JPEG, FALSE],

      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_DOC],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_DOCX],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_GIF],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_JPEG],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_JPG],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_MP3],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_MP4],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_PDF],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_PNG],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_SVG],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_XLS],
      [GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC, GeneratedContentAssetGenerator::ASSET_TYPE_XLSX],
    ];
    // phpcs:enable Drupal.Arrays.Array.LongLineDeclaration
  }

  /**
   * Tests generatorRandomImage().
   *
   * @param string $type
   *   Type.
   * @param array<mixed> $options
   *   Options.
   * @param int $expected_width
   *   Width.
   * @param int $expected_height
   *   Height.
   * @param string $expected_mime_type
   *   Mine type.
   *
   * @throws \Exception
   *
   * @dataProvider dataProviderGeneratorRandomImage
   */
  public function testGeneratorRandomImage(string $type, array $options, int $expected_width, int $expected_height, string $expected_mime_type): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentAssetGenerator $generator */
    $generator = $this->container->get('generated_content.asset_generator');

    $file = $generator->generate($type, $options, $generator::GENERATE_TYPE_RANDOM);
    $uri = $file->getFileUri();
    $this->assertImageWidth($uri, $expected_width);
    $this->assertImageHeight($uri, $expected_height);
    $this->assertFileMimeType($uri, $expected_mime_type);
  }

  /**
   * Data provider for testGeneratorRandomImage().
   *
   * @return array<mixed>
   *   Test data.
   */
  public function dataProviderGeneratorRandomImage(): array {
    return [
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
        [],
        350,
        200,
        'image/png',
      ],
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
        [
          'width' => 100,
          'height' => 300,
        ],
        100,
        300,
        'image/png',
      ],
      [
        GeneratedContentAssetGenerator::ASSET_TYPE_PNG,
        [
          'width' => 0,
          'height' => '',
        ],
        350,
        200,
        'image/png',
      ],

      [
        GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
        [],
        350,
        200,
        'image/jpeg',
      ],

      [
        GeneratedContentAssetGenerator::ASSET_TYPE_JPEG,
        [],
        350,
        200,
        'image/jpeg',
      ],
    ];
  }

  /**
   * Tests generatorRandomImage().
   *
   * @param string $type
   *   Type.
   * @param string $expected_mime_type
   *   Mine type.
   *
   * @throws \Exception
   *
   * @dataProvider dataProviderGeneratorStaticFile
   */
  public function testGeneratorStaticFile(string $type, string $expected_mime_type): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentAssetGenerator $generator */
    $generator = $this->container->get('generated_content.asset_generator');

    $file = $generator->generate($type, [], $generator::GENERATE_TYPE_STATIC);
    $uri = $file->getFileUri();
    $this->assertFileMimeType($uri, $expected_mime_type);
  }

  /**
   * Data provider for testGeneratorRandomImage().
   *
   * @return array<mixed>
   *   Test data.
   */
  public function dataProviderGeneratorStaticFile(): array {
    // phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
    return [
      [GeneratedContentAssetGenerator::ASSET_TYPE_DOC, 'application/msword'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_DOCX, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_GIF, 'image/gif'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_JPEG, 'image/jpeg'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_JPG, 'image/jpeg'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_MP3, 'audio/mpeg'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_MP4, 'video/mp4'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_PDF, 'application/pdf'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_PNG, 'image/png'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_SVG, 'image/svg+xml'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_XLS, 'application/vnd.ms-excel'],
      [GeneratedContentAssetGenerator::ASSET_TYPE_XLSX, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
    ];
    // phpcs:enable Drupal.Arrays.Array.LongLineDeclaration
  }

}
