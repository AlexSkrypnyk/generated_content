<?php

namespace Drupal\generated_content\Helpers;

use Drupal\Component\Utility\Random;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileRepository;

/**
 * Class GeneratedContentAssetGenerator.
 *
 * The utility class for generating data.
 *
 * @package Drupal\generated_content
 */
class GeneratedContentAssetGenerator {

  /**
   * Defines random generation types.
   */
  const GENERATE_TYPE_RANDOM = 'random';

  /**
   * Defines static generation types.
   */
  const GENERATE_TYPE_STATIC = 'static';

  /**
   * Asset types.
   */
  const ASSET_TYPE_DOC = 'doc';

  const ASSET_TYPE_DOCX = 'docx';

  const ASSET_TYPE_GIF = 'gif';

  const ASSET_TYPE_JPEG = 'jpeg';

  const ASSET_TYPE_JPG = 'jpg';

  const ASSET_TYPE_MP3 = 'mp3';

  const ASSET_TYPE_MP4 = 'mp4';

  const ASSET_TYPE_PDF = 'pdf';

  const ASSET_TYPE_PNG = 'png';

  const ASSET_TYPE_SVG = 'svg';

  const ASSET_TYPE_XLS = 'xls';

  const ASSET_TYPE_XLSX = 'xlsx';

  const ASSET_TYPE_TXT = 'txt';

  /**
   * Defines assets directory.
   */
  const ASSETS_DIRECTORY = 'assets';

  /**
   * The utility class for creating random data.
   *
   * @var \Drupal\Component\Utility\Random
   */
  protected $random;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * The file repository.
   *
   * @var \Drupal\file\FileRepositoryInterface
   */
  protected $fileRepository;

  /**
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleExtensionList;

  /**
   * Array of available assets.
   *
   * Keys are extensions without a leading dot and values are paths to assets.
   *
   * @var array
   */
  protected $assets;

  /**
   * Constructor.
   */
  public function __construct(FileSystemInterface $file_system, EntityTypeManager $entity_type_manager, FileRepository $file_repository, ModuleExtensionList $module_extension_list) {
    $this->fileSystem = $file_system;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileRepository = $file_repository;
    $this->moduleExtensionList = $module_extension_list;
    $this->random = new Random();
    $this->assets = $this->loadAssets();
  }

  /**
   * Generate an asset of specified type with provided options.
   *
   * @param string $type
   *   Asset type. One of the pre-defined ASSET_TYPE_* constants.
   * @param array $options
   *   Array of options to pass to the generator. May differ based on the
   *   generator, but always have these options available in every generator:
   *   - filename: (optional) Optional filename without an extension.
   *     Defaults to a randomly-generated name.
   *   - extension: (optional) Extension without leading dot.
   *     Defaults to the value of $type.
   *   - directory: (optional) Destination directory of the generated asset.
   *     Defaults to 'public://generated_content'.
   *   - use_existing: (optional) Use existing asset instead of re-generating.
   *     Defaults to TRUE.
   * @param string $generation_type
   *   Generation type as defined in generatorMap(). New generation types may
   *   be used in descendant classes, if required to define a custom generation
   *   type.
   *   Defaults to GENERATE_TYPE_RANDOM.
   *
   * @return \Drupal\file\FileInterface
   *   Object of saved managed Drupal file.
   */
  public function generate($type, array $options = [], $generation_type = self::GENERATE_TYPE_RANDOM) {
    // Validate options.
    $default_options = [
      // Filename without extension.
      'filename' => $this->random->word(rand(4, 12)),
      // Extension without leading dot. Defaults to the type.
      'extension' => $type,
      // Destination directory.
      'directory' => 'public://generated_content',
      // Use existing asset instead of re-generating.
      'use_existing' => TRUE,
    ];

    $options += $default_options;

    $extension = '.' . ltrim($options['extension'], '.');
    $filename = $options['filename'] . $extension;
    $directory = ltrim($options['directory'], DIRECTORY_SEPARATOR);

    // Find existing files.
    if ($options['use_existing']) {
      if ($file = $this->findFileByName($filename)) {
        return $file;
      }
    }

    if (empty($directory)) {
      throw new \Exception('Provided destination directory is empty.');
    }

    // Route to the corresponding generator base on type.
    $generator = $this->generatorMap()[$generation_type][$type] ?? NULL;

    // Fallback to default generator and issue a notice. Custom generation types
    // are expected to extend generatorMap() with own definitions.
    //
    // Note that it is not required to extend generatorMap() to produce a file
    // of $type with a different extension (for example, .log instead .txt) -
    // the custom file extension can be defined in $options['extension'].
    if (is_null($generator)) {
      $generator = $this->getDefaultGenerator();
      trigger_error(sprintf('Generator is not defined for "%s" generation of "%s" type in %s. Using default generator %s.',
        $generation_type,
        $type,
        static::class . '::generatorMap()',
        (is_array($generator) ? $generator[0] . '::' . $generator[1] : $generator) . '()',
      ), E_USER_NOTICE);
    }

    if (is_callable($generator)) {
      $generated_filepath = call_user_func($generator, $type, $options);
    }
    elseif (count($generator) == 2 && get_class($this) == $generator[0] && method_exists($this, $generator[1])) {
      $generated_filepath = $this->{$generator[1]}($type, $options);
    }
    else {
      throw new \RuntimeException(sprintf('Error while trying to use generator "%s".', print_r($generator, TRUE)));
    }

    if (!is_readable($generated_filepath)) {
      throw new \Exception(sprintf('Unable to read generated file "%s".', $generated_filepath));
    }

    $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
    $uri = $directory . DIRECTORY_SEPARATOR . $filename;

    return $this->fileRepository->writeData(file_get_contents($generated_filepath), $uri);
  }

  /**
   * Shorthand to generate a random asset.
   */
  public function generateRandom($type, array $options = []) {
    return $this->generate($type, $options, self::GENERATE_TYPE_RANDOM);
  }

  /**
   * Shorthand to generate a static asset.
   */
  public function generateStatic($type, array $options = []) {
    return $this->generate($type, $options, self::GENERATE_TYPE_STATIC);
  }

  /**
   * Map generators to generation and asset types.
   *
   * @return array
   *   Multidimensional array of generation types as keys and an array of
   *   asset type to callable generators.
   */
  protected static function generatorMap() {
    return [
      // Randomly generated assets - content will change for each generation.
      self::GENERATE_TYPE_RANDOM => [
        self::ASSET_TYPE_PNG => [static::class, 'generatorRandomImage'],
        self::ASSET_TYPE_JPEG => [static::class, 'generatorRandomImage'],
        self::ASSET_TYPE_JPG => [static::class, 'generatorRandomImage'],
        self::ASSET_TYPE_TXT => [static::class, 'generatorContentFile'],
      ],
      // Statically generated assets - content will be the same for each
      // generation.
      self::GENERATE_TYPE_STATIC => [
        self::ASSET_TYPE_DOC => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_DOCX => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_GIF => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_JPEG => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_JPG => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_MP3 => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_MP4 => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_PDF => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_PNG => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_SVG => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_XLS => [static::class, 'generatorStaticFile'],
        self::ASSET_TYPE_XLSX => [static::class, 'generatorStaticFile'],
      ],
    ];
  }

  /**
   * Default generator for any undefined types.
   *
   * @return callable
   *   Callable generator.
   */
  protected static function getDefaultGenerator() {
    return [static::class, 'generatorContentFile'];
  }

  /**
   * Generate a file with content.
   *
   * @param string $type
   *   File type.
   * @param array $options
   *   Array of options for this generator:
   *   - content: (string) The content of the file.
   *
   * @return string
   *   Real path to generated file.
   */
  protected function generatorContentFile($type, array $options = []) {
    $options += [
      'content' => 'Placeholder text',
    ];

    $filepath = $this->createTempFile();

    file_put_contents($filepath, $options['content']);

    return $filepath;
  }

  /**
   * Generate a random image.
   *
   * @param string $type
   *   File type.
   * @param array $options
   *   Array of options for this generator:
   *   - width: (int) Image width.
   *   - height: (int) Image height.
   *
   * @return string
   *   Real path to generated file.
   */
  protected function generatorRandomImage($type, array $options = []) {
    $options += [
      'width' => 350,
      'height' => 200,
    ];

    $function = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
    if (!is_callable($function)) {
      throw new \RuntimeException(sprintf('Unable to create a random image asset: function "%s" does not exist.', $function));
    }

    $width = !empty($options['width']) ? $options['width'] : 350;
    $height = !empty($options['height']) ? $options['height'] : 200;

    $filepath = $this->createTempFile();

    // Make an image split into 4 sections with random colors.
    $image = imagecreate($width, $height);
    for ($n = 0; $n < 4; $n++) {
      $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
      $x = $width / 2 * ($n % 2);
      $y = $height / 2 * (int) ($n >= 2);
      imagefilledrectangle($image, $x, $y, $x + $width / 2, $y + $height / 2, $color);
    }

    // Make a perfect circle in the image middle.
    $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    $smaller_dimension = min($width, $height);
    $smaller_dimension = ($smaller_dimension % 2) ? $smaller_dimension : $smaller_dimension;
    imageellipse($image, $width / 2, $height / 2, $smaller_dimension, $smaller_dimension, $color);

    $function($image, $filepath);

    return $filepath;
  }

  /**
   * Generate a static file.
   *
   * @param string $type
   *   File type.
   * @param array $options
   *   Array of options for this generator.
   *
   * @return string
   *   Real path to generated file.
   */
  protected function generatorStaticFile($type, array $options = []) {
    $filepath = $this->assets[$type] ?? NULL;

    if (!$filepath) {
      throw new \RuntimeException(sprintf('Unable to create a static asset: "%s" source asset type is not available.', $type));
    }

    return $filepath;
  }

  /**
   * Find file by name.
   *
   * @param string $filename
   *   Filename to search for.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\file\Entity\File|null
   *   File object or NULL if not found.
   */
  protected function findFileByName($filename) {
    $file = NULL;

    $storage = $this->entityTypeManager->getStorage('file');
    $query = $storage->getQuery('AND')->condition('filename', $filename);
    $ids = $query->execute();

    if (!empty($ids)) {
      $id = reset($ids);
      $file = $storage->load($id);
    }

    return $file;
  }

  /**
   * Create a temporary file with an optional prefix.
   */
  protected function createTempFile($prefix = 'generated_content_asset') {
    // Create a temp file to write to.
    if (!$tmp_file = $this->fileSystem->tempnam('temporary://', $prefix)) {
      throw new \RuntimeException('Unable to create a temporary file to generate a random image.');
    }

    return $this->fileSystem->realpath($tmp_file);
  }

  /**
   * Load assets.
   *
   * @return array
   *   Array with extensions (without leading dot) as keys and paths to dummy
   *   asset files as values.
   */
  protected function loadAssets() {
    // Pre-load replacement assets.
    $extensions = [
      'jpg',
      'jpeg',
      'gif',
      'png',
      'pdf',
      'doc',
      'docx',
      'xls',
      'xlsx',
      'mp3',
      'mp4',
      'svg',
    ];

    $module_path = $this->moduleExtensionList->getPath('generated_content');
    foreach ($extensions as $extension) {
      $dummy_file = $module_path . DIRECTORY_SEPARATOR . rtrim(static::ASSETS_DIRECTORY, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'dummy.' . $extension;
      if (file_exists($dummy_file)) {
        $assets[$extension] = $dummy_file;
      }
    }

    return $assets;
  }

}
