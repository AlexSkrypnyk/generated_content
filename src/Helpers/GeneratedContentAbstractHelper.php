<?php

namespace Drupal\generated_content\Helpers;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\generated_content\GeneratedContentRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneratedContentHelper.
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
   * The helper singleton.
   *
   * @var \Drupal\generated_content\Helpers\GeneratedContentHelper
   */
  protected static $instance = NULL;

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
   * Get this helper instance.
   *
   * @return \Drupal\generated_content\Helpers\GeneratedContentHelper
   *   The repository.
   */
  public static function getInstance() {
    if (!self::$instance) {
      static::$instance = \Drupal::service('class_resolver')
        ->getInstanceFromDefinition(static::class);
    }

    return self::$instance;
  }

  /**
   * Log verbose progress.
   */
  public static function log() {
    if (self::$verbose) {
      if (function_exists('drush_print')) {
        // Strip all tags, but still decode some HTML entities.
        drush_print(html_entity_decode(strip_tags(call_user_func_array('sprintf', func_get_args()))));
      }
      else {
        // Support HTML, but still use plain strings for simplicity.
        self::$messenger->addMessage(new FormattableMarkup(call_user_func_array('sprintf', func_get_args()), []));
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
    self::$repository->addEntitiesNoTracking($entities);
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
   * Intersect arrays by column.
   *
   * @param string $column
   *   Column name.
   * @param ...
   *   Variable number of arrays.
   *
   * @return array
   *   Array of intersected values.
   */
  protected static function arrayIntersectColumn($column) {
    $arrays = func_get_args();
    array_shift($arrays);

    if (count($arrays) < 1) {
      throw new \Exception('At least one array argument is required');
    }

    foreach ($arrays as $k => $array) {
      if (!is_array($array)) {
        throw new \Exception(sprintf('Argument %s is not an array', $k + 1));
      }
    }

    $carry = array_shift($arrays);
    foreach ($arrays as $k => $array) {
      $carry_column = self::arrayColumn($carry, $column);
      $array_column = self::arrayColumn($array, $column);
      $column_values = array_intersect($carry_column, $array_column);

      $carry = array_filter($array, function ($item) use ($column, $column_values) {
        $value = self::extractProperty($item, $column);

        return $value && in_array($value, $column_values);
      });
    }

    return $carry;
  }

  /**
   * Portable array_column with support for methods.
   */
  protected static function arrayColumn(array $array, $key) {
    if (!is_scalar($key)) {
      throw new \Exception('Specified key is not scalar');
    }

    return array_map(function ($item) use ($key) {
      return self::extractProperty($item, $key);
    }, $array);
  }

  /**
   * Helper to extract property.
   *
   * Note that this helper supports extracting values from simple methods.
   *
   * @param mixed $item
   *   Array or object.
   * @param string $key
   *   Array key or object property or method.
   *
   * @return mixed|null
   *   For arrays - value at specified key.
   *   For objects - value of the specified property or returned value of the
   *   method.
   *
   * @throws \Exception
   *   If key is not scalar.
   *   If item is not an array or an object.
   *   If item is an object, but does not have a property or a method with
   *   specified name.
   *   If item is an array and does not have an element with specified key.
   */
  protected static function extractProperty($item, $key) {
    if (!is_scalar($key)) {
      throw new \Exception('Specified key is not scalar');
    }

    if (!is_object($item) && !is_array($item)) {
      throw new \Exception(sprintf('Item with key "%s" must be an object or an array', $key));
    }

    if (is_object($item)) {
      if (method_exists($item, $key)) {
        return $item->{$key}();
      }
      elseif (property_exists($item, $key)) {
        return $item->{$key};
      }
      throw new \Exception(sprintf('Key "%s" is not a property or a method of an object', $key));
    }
    elseif (is_array($item)) {
      if (isset($item[$key])) {
        return $item[$key];
      }
      throw new \Exception(sprintf('Key "%s" does not exist in array', $key));
    }

    return NULL;
  }

}
