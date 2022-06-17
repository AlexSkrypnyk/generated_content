<?php

namespace Drupal\generated_content\Helpers;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\generated_content\GeneratedContentRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneratedContentAbstractHelper.
 *
 * Helper to interact with generated content items.
 *
 * @package \Drupal\generated_content
 */
abstract class GeneratedContentAbstractHelper implements ContainerInjectionInterface {

  use GeneratedContentVariationTrait;
  use GeneratedContentRandomTrait;
  use GeneratedContentStaticTrait;

  /**
   * Instances of descendant classes.
   *
   * @var \Drupal\generated_content\Helpers\GeneratedContentAbstractHelper[]
   */
  protected static $instances = [];

  /**
   * The repository singleton.
   *
   * @var \Drupal\generated_content\GeneratedContentRepository
   */
  protected static $repository = NULL;

  /**
   * Asset generator.
   *
   * @var \Drupal\generated_content\Helpers\GeneratedContentAssetGenerator
   */
  protected static $assetGenerator = NULL;

  /**
   * Asset generator.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected static $messenger = NULL;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected static $entityTypeManager = NULL;

  /**
   * Use verbose mode.
   *
   * @var bool
   */
  protected static $verbose = TRUE;

  /**
   * Array of static entity offsets to track calls to retrieve static entities.
   *
   * @var array
   */
  protected static $staticEntityOffsets;

  /**
   * GeneratedContentHelper constructor.
   */
  public function __construct(GeneratedContentRepository $repository, GeneratedContentAssetGenerator $asset_generator, MessengerInterface $messenger, EntityTypeManagerInterface $entity_type_manager) {
    static::$repository = $repository;
    static::$assetGenerator = $asset_generator;
    static::$messenger = $messenger;
    static::$entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      GeneratedContentRepository::getInstance(),
      $container->get('generated_content.asset_generator'),
      $container->get('messenger'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Get singleton instance of one of the descendant classes.
   *
   * @return \Drupal\generated_content\Helpers\GeneratedContentAbstractHelper
   *   Helper class instance.
   */
  public static function getInstance() {
    if (empty(self::$instances[static::class])) {
      self::$instances[static::class] = \Drupal::service('class_resolver')
        ->getInstanceFromDefinition(static::class);
    }

    return self::$instances[static::class];
  }

  /**
   * Reset singleton instance.
   *
   * @return \Drupal\generated_content\Helpers\GeneratedContentAbstractHelper
   *   A new singleton instance.
   */
  public function reset() {
    static::$instances = [];
    static::$staticOffset = 0;
    static::$staticEntityOffsets = [];

    return static::getInstance();
  }

  /**
   * Log verbose progress.
   */
  public static function log() {
    if (static::$verbose) {
      if (function_exists('drush_print')) {
        // Strip all tags, but still decode some HTML entities.
        drush_print(html_entity_decode(strip_tags(call_user_func_array('sprintf', func_get_args()))));
      }
      else {
        // Support HTML, but still use plain strings for simplicity.
        static::$messenger->addMessage(new FormattableMarkup(call_user_func_array('sprintf', func_get_args()), []));
      }
    }
  }

  /**
   * Add entities to the repository.
   *
   * Useful to make entities available within the creation callback before
   * they are returned to allow referencing within the same callback.
   * For example, to create nodes and fill-in related nodes in the follow-up
   * iterations.
   *
   * @param mixed $entities
   *   Array of entities.
   */
  public static function addToRepository($entities) {
    $entities = is_array($entities) ? $entities : [$entities];
    static::$repository->addEntitiesNoTracking($entities);
  }

  /**
   * Create a managed file and store it as a managed file.
   *
   * @param string $type
   *   File type: one of FILE_TYPE_IMAGE, FILE_TYPE_BINARY, FILE_TYPE_OTHER.
   * @param array $options
   *   Array of options to pass to the asset generator.
   *
   * @return \Drupal\file\FileInterface
   *   Created managed file.
   */
  public static function createFile($type, array $options = []) {
    switch ($type) {
      case static::FILE_TYPE_IMAGE:
        return static::$assetGenerator->createImage($options);

      case static::FILE_TYPE_BINARY:
        return static::$assetGenerator->createBinaryFile($options);

      default:
        return static::$assetGenerator->createFlatFile($options);
    }
  }

  /**
   * Replace string tokens.
   *
   * @param string $string
   *   String to process.
   * @param array $replacements
   *   Array of replacements with keys as tokens and values as replacements.
   * @param callable $cb
   *   Optional callback to process values before replacement. The callback
   *   receives a value as it was passed in $replacements and must return
   *   a value. If not provided, a value from $replacements will be used as-is.
   * @param string $beginToken
   *   Optional string to define token beginning boundary. Defaults to '{'.
   * @param string $endToken
   *   Optional string to define token ending boundary. Defaults to '}'.
   *
   * @return string
   *   String with replaced tokens.
   */
  public static function replaceTokens($string, array $replacements, callable $cb = NULL, $beginToken = '{', $endToken = '}') {
    foreach ($replacements as $k => $v) {
      $token_name = $beginToken . $k . $endToken;
      if ($cb && is_callable($cb)) {
        $v = call_user_func($cb, $v);
      }
      if (is_scalar($v)) {
        $string = strtr($string, [$token_name => $v]);
      }
    }

    return $string;
  }

  /**
   * Get random generated entities.
   *
   * @param string $entity_type
   *   Entity type.
   * @param null|string $bundle
   *   Entity bundle. If none provided - all entities of this type will be
   *   returned (further limited by $count).
   * @param null|int $count
   *   Number of entities to return. If none provided - all entities will be
   *   returned.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of entities.
   */
  protected static function randomEntities($entity_type, $bundle = NULL, $count = NULL) {
    $entities = static::$repository->getEntities($entity_type, $bundle);

    if (!empty($entities) && !$bundle) {
      $entities_all = [];
      foreach ($entities as $bundled_entities) {
        $entities_all = array_merge($entities_all, $bundled_entities);
      }
      $entities = $entities_all;
      shuffle($entities);
    }

    return is_null($count) ? $entities : static::randomArrayItems($entities, $count);
  }

  /**
   * Get random real entities.
   *
   * Real entities are entities without generated entities.
   *
   * @param string $entity_type
   *   Entity type.
   * @param null|string $bundle
   *   Entity bundle. If none provided - all entities of this type will be
   *   returned.
   * @param null|int $count
   *   Number of entities to return. If none provided - all entities will be
   *   returned.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of entities.
   */
  protected static function randomRealEntities($entity_type, $bundle = NULL, $count = NULL) {
    if ($bundle) {
      $keys = static::$entityTypeManager->getStorage($entity_type)->getEntityType()->getKeys();
      $entity_type_key = $keys['bundle'];
    }

    if (!empty($entity_type_key)) {
      $entities = static::$entityTypeManager->getStorage($entity_type)->loadByProperties([$entity_type_key => $bundle]);
    }
    else {
      $entities = static::$entityTypeManager->getStorage($entity_type)->loadMultiple();
    }

    $entities = static::filterOutGeneratedContentEntities($entities, $entity_type, $bundle);

    return is_null($count) ? $entities : static::randomArrayItems($entities, $count);
  }

  /**
   * Filter-out generated entities.
   */
  protected static function filterOutGeneratedContentEntities($entities, $entity_type, $bundle) {
    $generated_entities = static::$repository->getEntities($entity_type, $bundle);

    if (!empty($generated_entities) && !$bundle) {
      $entities_all = [];
      foreach ($generated_entities as $bundled_entities) {
        $entities_all = array_merge($entities_all, $bundled_entities);
      }
      $generated_entities = $entities_all;
    }

    $generated_entities_ids = array_filter(array_map(function ($value) {
      return is_object($value) ? $value->id() : NULL;
    }, $generated_entities));

    $entities_ids = array_filter(array_map(function ($value) {
      return is_object($value) ? $value->id() : NULL;
    }, $entities));

    $non_generated_ids = array_diff($entities_ids, $generated_entities_ids);

    return array_intersect_key($entities, array_flip($non_generated_ids));
  }

  /**
   * Get random static entities.
   *
   * Static entities are entities within a repository, returned in a predictable
   * order based on the called argument list. So, calls with and without $bundle
   * value are tracked separately.
   *
   * @param string $entity_type
   *   Entity type.
   * @param null|string $bundle
   *   Entity bundle. If none provided - all entities of this type will be
   *   returned.
   * @param null|int $count
   *   Number of entities to return. If none provided - all entities will be
   *   returned with previously used offsets.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of entities.
   */
  protected static function staticEntities($entity_type, $bundle = NULL, $count = NULL) {
    $entities = static::$repository->getEntities($entity_type, $bundle);

    if (!empty($entities) && !$bundle) {
      $entities_all = [];
      foreach ($entities as $bundled_entities) {
        $entities_all = array_merge($entities_all, $bundled_entities);
      }
      $entities = $entities_all;
    }

    $idx = static::getStaticEntityOffset($entity_type, $bundle);

    if (is_null($count)) {
      $return = self::arraySliceCircular($entities, count($entities), $idx);
    }
    else {
      $return = self::arraySliceCircular($entities, $count, $idx);
    }

    static::setStaticEntityOffset(count($return), $entity_type, $bundle);

    return $return;
  }

  /**
   * Set static entity offset.
   *
   * Note that entity offsets with and without $bundle value are tracked
   * separately.
   *
   * @param ...
   *   A list of properties to track.
   *
   * @return int
   *   Offset value.
   */
  protected static function getStaticEntityOffset() {
    $key = implode('__', func_get_args());
    self::$staticEntityOffsets[$key] = self::$staticEntityOffsets[$key] ?? 0;

    return self::$staticEntityOffsets[$key];
  }

  /**
   * Set static entity offset to a value.
   *
   * Note that this will further offset any existing entity offsets by an
   * $offset value.
   *
   * @param ...
   *   A list of properties to track. First argument being the offset.
   */
  protected static function setStaticEntityOffset() {
    $args = func_get_args();
    $offset = array_shift($args);
    $key = implode('__', $args);
    self::$staticEntityOffsets[$key] = self::$staticEntityOffsets[$key] ?? 0;
    self::$staticEntityOffsets[$key] += $offset;
  }

  /**
   * Slice array as a circular array.
   *
   * @param array $array
   *   Array to slice.
   * @param int $count
   *   Number of items to return.
   * @param int $offset
   *   Optional offset to start slicing.
   *
   * @return array
   *   Sliced array with reset keys starting from 0.
   */
  protected static function arraySliceCircular(array $array, $count, $offset = 0) {
    $out = [];
    $len = count($array);
    $keys = array_keys($array);

    while ($count && $len > 0) {
      if ($offset >= $len) {
        $offset = $offset % $len;
      }
      $out[] = $array[$keys[$offset++]];
      $count--;
    }

    return $out;
  }

}
