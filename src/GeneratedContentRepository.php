<?php

declare(strict_types=1);

namespace Drupal\generated_content;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginManager;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Utility\Error;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneratedContentRepository.
 *
 * Repository class to manage generated content items.
 *
 * @package \Drupal\generated_content
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class GeneratedContentRepository implements ContainerInjectionInterface {

  use DependencySerializationTrait;

  /**
   * The repository singleton instances keyed by class name.
   *
   * @var array<string, \Drupal\generated_content\GeneratedContentRepository>
   */
  protected static array $instances = [];

  /**
   * Array of discovered information about entities.
   *
   * @var array<mixed>
   */
  protected array $info = [];

  /**
   * The entities.
   *
   * @var array<mixed>
   */
  protected array $entities = [];

  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected MessengerInterface $messenger;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected LoggerChannelInterface $logger;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $database;

  /**
   * Container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected ContainerInterface $container;

  /**
   * Plugin manager.
   *
   * @var \Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginManager
   */
  protected GeneratedContentPluginManager $pluginManager;

  /**
   * GeneratedContentRepository constructor.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    MessengerInterface $messenger,
    EntityTypeManagerInterface $entityTypeManager,
    LoggerChannelFactoryInterface $loggerChannelFactory,
    Connection $database,
    ContainerInterface $container,
    GeneratedContentPluginManager $plugin_manager,
  ) {
    $this->messenger = $messenger;
    $this->entityTypeManager = $entityTypeManager;
    $this->logger = $loggerChannelFactory->get('generated_content');
    $this->database = $database;
    $this->container = $container;
    $this->pluginManager = $plugin_manager;

    $this->entities = $this->loadEntities();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    // @phpstan-ignore new.static
    return new static(
      $container->get('messenger'),
      $container->get('entity_type.manager'),
      $container->get('logger.factory'),
      $container->get('database'),
      $container,
      $container->get('plugin.manager.generated_content'),
    );
  }

  /**
   * Get the repository instance.
   *
   * @return static
   *   The repository.
   */
  public static function getInstance(): static {
    if (empty(self::$instances[static::class])) {
      /** @var static $instance */
      $instance = \Drupal::service('class_resolver')
        ->getInstanceFromDefinition(static::class);
      self::$instances[static::class] = $instance;
    }

    /** @var static */
    return self::$instances[static::class];
  }

  /**
   * Reset singleton instance.
   *
   * @return static
   *   A new singleton instance.
   */
  public function reset(): static {
    self::$instances = [];

    return static::getInstance();
  }

  /**
   * Get information about entities.
   *
   * @param bool $reset
   *   Flag to reset previously collected information.
   *
   * @return array<mixed>
   *   Array of information about entities.
   */
  public function getInfo(bool $reset = FALSE): array {
    if (empty($this->info) || $reset) {
      $this->info = $this->collectInfo();
    }

    return $this->info;
  }

  /**
   * Find info for provided entity type and bundle.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string|null $bundle
   *   Bundle.
   *
   * @return bool|mixed
   *   Array of info about an entity or FALSE if no such entity was found.
   */
  public function findInfo(string $entity_type, ?string $bundle = NULL) {
    $bundle = $bundle ?: $entity_type;

    foreach ($this->getInfo() as $item) {
      if ($item['entity_type'] == $entity_type && $item['bundle'] == $bundle) {
        return $item;
      }
    }

    return FALSE;
  }

  /**
   * Process creation of specified entities.
   *
   * @param array<mixed> $filter
   *   (optional) Multi-dimensional array of filtered items to process.
   *   The First level key is an entity type and the second is a bundle. Value
   *   is a boolean TRUE.
   * @param bool $clear_caches
   *   Flag to clear caches after all items were created.
   *
   * @return int
   *   Number of created items.
   */
  public function createEntities(array $filter = [], bool $clear_caches = TRUE): int {
    $info = $this->getInfo();

    $total = 0;
    foreach ($info as $item) {
      // Filter-out any items that have not been provided in the filter.
      if (!empty($filter) && empty($filter[$item['entity_type']][$item['bundle']])) {
        continue;
      }
      $total += $this->createSingle($item);
    }

    if ($clear_caches) {
      $this->clearCaches();
    }

    $this->messenger->addMessage('Created all generated content.');

    return $total;
  }

  /**
   * Process creation of specified entities in a batch.
   *
   * @param array<mixed>|null $info
   *   Info.
   */
  public function createBatch(?array $info = NULL): void {
    $info = $info ?: $this->getInfo();
    // Every info item needs to be set only once.
    GeneratedContentBatch::set('create', $info, 1);
  }

  /**
   * Process single entity definition.
   *
   * @param array<mixed> $info
   *   Entity definition information.
   */
  public function createSingle(array $info): ?int {
    /** @var \Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginInterface $plugin */
    $plugin = $this->pluginManager->createInstance($info['#plugin_id']);
    $entities = $plugin->generate();
    $this->messenger->addMessage(sprintf('Created generated content entities "%s" with bundle "%s"', $info['entity_type'], $info['bundle']));
    $this->addEntities($entities, $info['#tracking']);
    $total = count($entities);
    unset($entities);

    return $total;
  }

  /**
   * Process removal of specified entities.
   *
   * @param array<mixed>|null $info
   *   Info.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function remove(?array $info = NULL): void {
    $info = $info ?: $this->getInfo();

    foreach ($info as $item) {
      $this->removeSingle($item);
    }

    // Reload entities.
    $this->entities = $this->loadEntities();

    $this->clearCaches();
    $this->messenger->addMessage('Removed all generated content.');
  }

  /**
   * Remove specified generated content entities.
   *
   * @param array<mixed>|null $info
   *   Info.
   */
  public function removeBatch(?array $info = NULL): void {
    $info = $info ?: $this->getInfo();
    GeneratedContentBatch::set('remove', $info, count($info));
  }

  /**
   * Process regeneration of specified entities in a batch.
   *
   * @param array<mixed>|null $info
   *   Info.
   */
  public function regenerateBatch(?array $info = NULL): void {
    $info = $info ?: $this->getInfo();
    GeneratedContentBatch::set('regenerate', $info, 1);
  }

  /**
   * Cleanup content.
   *
   * @param array<mixed> $info
   *   Info.
   */
  public function removeSingle(array $info): void {
    $this->removeTrackedEntities($info['entity_type'], $info['bundle']);
    $this->messenger->addMessage(sprintf('Removed all generated content entities "%s" in bundle "%s"', $info['entity_type'], $info['bundle']));
  }

  /**
   * Check if the repository is empty.
   *
   * @return bool
   *   TRUE if there are no entities in the repository.
   */
  public function isEmpty(): bool {
    return $this->database->select('generated_content')->countQuery()->execute()->fetchField() == 0;
  }

  /**
   * Clear all required caches.
   */
  public function clearCaches(): void {
    $caches = [
      'data',
      'dynamic_page_cache',
      'entity',
      'page',
      'render',
    ];

    foreach ($caches as $cache_id) {
      try {
        /** @var \Drupal\Core\Cache\CacheBackendInterface $cache */
        $cache = $this->container->get('cache.' . $cache_id);
        $cache->deleteAll();
      }
      catch (\Exception $exception) {
        // Noop.
      }
    }
  }

  /**
   * Collect information about entities to process.
   *
   * @return array<mixed>
   *   Array of information records about each entity type and bundle.
   */
  protected function collectInfo(): array {
    $definitions = $this->pluginManager->getDefinitions();
    $available = [];

    foreach ($definitions as $id => $definition) {
      $entity_type = $definition['entity_type'];
      $bundle = $definition['bundle'];
      $key = $entity_type . '__' . $bundle;
      $available[$key] = [
        'entity_type' => $entity_type,
        'bundle' => $bundle,
        '#plugin_id' => $id,
        '#tracking' => $definition['tracking'] ?? TRUE,
        '#weight' => $definition['weight'] ?? 0,
        '#module' => $definition['provider'],
      ];
    }

    uasort($available, [SortArray::class, 'sortByWeightProperty']);

    return $available;
  }

  /**
   * Load all entities from the database.
   *
   * @param bool $load_entities
   *   Load full entities or not.
   *
   * @return array<mixed>
   *   Entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function loadEntities(bool $load_entities = TRUE): array {
    $result = [];
    $data = $this->database
      ->select('generated_content', 'gc')
      ->fields('gc')
      ->execute()
      ->fetchAll(2);

    // Collect all entity ids.
    foreach ($data as $item) {
      $result[$item['entity_type']][$item['bundle']][$item['entity_id']] = $item['entity_id'];
    }

    if (!$load_entities) {
      return $result;
    }

    // Traverse trough results and load entities.
    $entity_type_manager = $this->entityTypeManager;
    foreach ($result as $entity_type_id => $bundles) {
      foreach ($bundles as $bundle_id => $entity_ids) {
        $loaded_entities = $entity_type_manager
          ->getStorage((string) $entity_type_id)
          ->loadMultiple($entity_ids);
        if (!empty($loaded_entities)) {
          $result[$entity_type_id][$bundle_id] = $loaded_entities;
        }
      }
    }

    return $result;
  }

  /**
   * Add a generated content entity to the repository.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string|null $entity_type
   *   Override entity type with a custom value.
   * @param string|null $bundle
   *   Override bundle with a custom value.
   * @param bool $tracking
   *   Whether to track the entities.
   */
  protected function addEntity(EntityInterface $entity, ?string $entity_type = NULL, ?string $bundle = NULL, bool $tracking = TRUE): void {
    $entity_type = $entity_type ?: $entity->getEntityTypeId();
    $bundle = $bundle ?: $entity->bundle();

    $this->entities[$entity_type][$bundle][$entity->id()] = $entity;
    if ($tracking) {
      $this->trackEntity($entity);
    }
  }

  /**
   * Add multiple generated content entities to the repository.
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   The array of entities.
   * @param bool $tracking
   *   Whether to track the entities.
   */
  public function addEntities(array $entities, bool $tracking = TRUE): void {
    foreach ($entities as $entity) {
      $this->addEntity($entity, NULL, NULL, $tracking);
    }
  }

  /**
   * Add multiple generated content entities to the repository without tracking.
   *
   * Used to update in-memory entities without writing them to DB.
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   The array of entities.
   */
  public function addEntitiesNoTracking(array $entities): void {
    foreach ($entities as $entity) {
      $this->addEntity($entity, NULL, NULL, FALSE);
    }
  }

  /**
   * Ger generated content entities.
   *
   * @param string|null $entity_type
   *   Entity type ID, eg. node or taxonomy_term.
   * @param string|null $bundle
   *   Bundle, eg. Page, lading_page.
   * @param bool $reset
   *   Flag to reset internal cache.
   *
   * @return array<mixed>
   *   The list of entities.
   */
  public function getEntities(?string $entity_type = NULL, ?string $bundle = NULL, bool $reset = FALSE): array {
    if (empty($this->entities) || $reset) {
      $this->entities = $this->loadEntities();
    }

    if ($entity_type) {
      if (isset($this->entities[$entity_type])) {
        if ($bundle) {
          if (isset($this->entities[$entity_type][$bundle])) {
            return $this->entities[$entity_type][$bundle];
          }

          return [];
        }

        return $this->entities[$entity_type];
      }

      return [];
    }

    return $this->entities;
  }

  /**
   * Track the entity permanently in the generated content table.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   */
  protected function trackEntity(EntityInterface $entity): void {
    try {
      $data = [
        'entity_type' => $entity->getEntityTypeId(),
        'bundle' => $entity->bundle(),
        'entity_id' => $entity->id(),
      ];
      $this->database->merge('generated_content')
        ->keys($data)
        ->updateFields($data)
        ->execute();
    }
    catch (\Exception $exception) {
      $this->logger->log(LogLevel::ERROR, ERROR::DEFAULT_ERROR_MESSAGE, Error::decodeException($exception));
    }
  }

  /**
   * Remove all tracked entities.
   *
   * @param string|null $entity_type
   *   Entity type.
   * @param string|null $bundle
   *   Bundle.
   * @param string|int|null $entity_id
   *   Entity id.
   */
  protected function removeTrackedEntities(?string $entity_type = NULL, ?string $bundle = NULL, $entity_id = NULL): void {
    $bundle = $bundle ?: $entity_type;

    try {
      if (!$this->database->schema()->tableExists('generated_content')) {
        return;
      }

      $query = $this->database->select('generated_content', 'gc')
        ->fields('gc');

      if ($entity_type) {
        $query->condition('entity_type', $entity_type);
      }
      if ($bundle) {
        $query->condition('bundle', $bundle);
      }
      if ($entity_id) {
        $query->condition('entity_id', $entity_id);
      }

      $query = $query->execute();

      $results = $query->fetchAll(2);
      foreach ($results as $result) {
        try {
          $entity = $this->entityTypeManager->getStorage($result['entity_type'])
            ->load($result['entity_id']);
          if ($entity) {
            $entity->delete();
            unset($this->entities[$entity_type][$bundle][$entity_id]);
          }
        }
        catch (\Exception $exception) {
          $this->logger->log(LogLevel::ERROR, ERROR::DEFAULT_ERROR_MESSAGE, Error::decodeException($exception));
        }
      }
    }
    catch (\Exception $exception) {
      $this->logger->log(LogLevel::ERROR, ERROR::DEFAULT_ERROR_MESSAGE, Error::decodeException($exception));
    }
  }

}
