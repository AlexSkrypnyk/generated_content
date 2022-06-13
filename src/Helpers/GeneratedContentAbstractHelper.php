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
    static::$staticParagraphIdx = 0;

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

}
