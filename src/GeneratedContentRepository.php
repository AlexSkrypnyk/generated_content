<?php

namespace Drupal\generated_content;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Database\Database;
use Drupal\Core\Entity\EntityInterface;

/**
 * Class GeneratedContentRepository.
 *
 * Repository class to manage generated content items.
 *
 * @package \Drupal\generated_content
 */
class GeneratedContentRepository {

  const CONTENT_DIRECTORY = 'generated_content';

  /**
   * The repository singleton.
   *
   * @var \Drupal\generated_content\GeneratedContentRepository
   */
  protected static $instance = NULL;

  /**
   * Array of discovered information about entities.
   *
   * @var array
   */
  protected $info = [];

  /**
   * The entities.
   *
   * @var array
   */
  protected $entities = [];

  /**
   * Path to content directory.
   *
   * @var string
   */
  protected $contentBasePath;

  /**
   * GeneratedContentRepository constructor.
   */
  public function __construct() {
    $this->entities = $this->loadEntities();
  }

  /**
   * Get the repository instance.
   *
   * @return \Drupal\generated_content\GeneratedContentRepository
   *   The repository.
   */
  public static function getInstance() {
    if (!static::$instance) {
      static::$instance = \Drupal::service('class_resolver')
        ->getInstanceFromDefinition(static::class);
    }

    return static::$instance;
  }

  /**
   * Reset singleton instance.
   *
   * @return \Drupal\generated_content\GeneratedContentRepository
   *   A new singleton instance.
   */
  public function reset() {
    static::$instance = NULL;

    return static::getInstance();
  }

  /**
   * Get information about entities.
   *
   * @param bool $reset
   *   Flag to reset previously collected information.
   *
   * @return array
   *   Array of information about entities.
   */
  public function getInfo($reset = FALSE) {
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
   * @param null|string $bundle
   *   Bundle.
   *
   * @return bool|mixed
   *   Array of info about an entity or FALSE if no such entity was found.
   */
  public function findInfo($entity_type, $bundle = NULL) {
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
   * @param array $filter
   *   (optional) Multi-dimensional array of filtered items to process.
   *   The First level key is an entity type and the second is a bundle. Value
   *   is a boolean TRUE.
   * @param bool $clear_caches
   *   Flag to clear caches after all items were created.
   *
   * @return int
   *   Number of created items.
   */
  public function create(array $filter = [], $clear_caches = TRUE) {
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

    \Drupal::messenger()->addMessage('Created all generated content.');

    return $total;
  }

  /**
   * Process creation of specified entities in a batch.
   */
  public function createBatch($info = FALSE) {
    $info = $info ? $info : $this->getInfo();
    // Every info item needs to be set only once.
    GeneratedContentBatch::set('create', $info, 1);
  }

  /**
   * Process single entity definition.
   *
   * @param array $info
   *   Entity definition information.
   */
  public function createSingle(array $info) {
    if (!empty($info['#file']) && file_exists($info['#file'])) {
      require_once $info['#file'];
    }
    $entities = $info['#callback']();
    \Drupal::messenger()->addMessage(sprintf('Created generated content entities "%s" with bundle "%s"', $info['entity_type'], $info['bundle']));
    $this->addEntities($entities, $info['#tracking']);
    $total = count($entities);
    unset($entities);

    return $total;
  }

  /**
   * Process removal of specified entities.
   */
  public function remove($info = FALSE) {
    $info = $info ? $info : $this->getInfo();

    foreach ($info as $item) {
      $this->removeSingle($item);
    }

    // Reload entities.
    $this->entities = $this->loadEntities();

    $this->clearCaches();
    \Drupal::messenger()->addMessage('Removed all generated content.');
  }

  /**
   * Remove specified generated content entities.
   */
  public function removeBatch($info = FALSE) {
    $info = $info ? $info : $this->getInfo();
    GeneratedContentBatch::set('remove', $info, count($info));
  }

  /**
   * Cleanup content.
   */
  public function removeSingle($info) {
    $this->removeTrackedEntities($info['entity_type'], $info['bundle']);
    \Drupal::messenger()->addMessage(sprintf('Removed all generated content entities "%s" in bundle "%s"', $info['entity_type'], $info['bundle']));
  }

  /**
   * Check if the repository is empty.
   *
   * @return bool
   *   TRUE if there are no entities in the repository.
   */
  public function isEmpty() {
    return Database::getConnection()->select('generated_content')->countQuery()->execute()->fetchField() == 0;
  }

  /**
   * Clear all required caches.
   */
  public function clearCaches() {
    $caches = [
      'data',
      'dynamic_page_cache',
      'entity',
      'page',
      'render',
    ];

    foreach ($caches as $cache) {
      if (\Drupal::hasService('cache.' . $cache)) {
        \Drupal::cache($cache)->deleteAll();
      }
    }
  }

  /**
   * Return an array of default weights.
   */
  protected function getDefaultWeights() {
    return [
      'user' => -100,
      'menu' => -90,
      'taxonomy_term' => -80,
      'media' => -50,
      'node' => 0,
      'block_content' => -10,
    ];
  }

  /**
   * Collect information about entities to process.
   *
   * If multiple modules implement the same hook - the last implementation
   * wins. This is by design - we do not support cross-module content generation
   * as it involves potentially resolving weight-related dependencies issues.
   *
   * @return array
   *   Array of information records about each entity type and bundle.
   */
  protected function collectInfo() {
    $paths = $this->collectImplementationPaths();

    if (empty($paths)) {
      return [];
    }

    $default_weights = $this->getDefaultWeights();

    $info = \Drupal::service('entity_type.bundle.info')->getAllBundleInfo();
    $available = [];

    foreach ($paths as $module_name => $path) {
      foreach ($info as $entity_type => $bundles) {
        foreach (array_keys($bundles) as $bundle) {
          $inc = $path . '/' . $entity_type . '/' . $bundle . '.inc';

          if (!file_exists($inc)) {
            continue;
          }
          require_once $inc;

          $func = $module_name . '_generated_content_create_' . $entity_type . '_' . $bundle;

          if (function_exists($func)) {
            $key = $entity_type . '__' . $bundle;
            $available[$key] = [
              'entity_type' => $entity_type,
              'bundle' => $bundle,
              '#callback' => $func,
              '#tracking' => TRUE,
              '#weight' => $default_weights[$entity_type] ?? 0,
              '#file' => $inc,
              '#module' => $module_name,
            ];

            $weight_function = $module_name . '_generated_content_create_' . $entity_type . '_' . $bundle . '_weight';
            if (function_exists($weight_function)) {
              $available[$key]['#weight'] = $weight_function();
            }

            $tracking_function = $module_name . '_generated_content_create_' . $entity_type . '_' . $bundle . '_tracking';
            if (function_exists($tracking_function)) {
              $available[$key]['#tracking'] = $tracking_function();
            }
          }
        }
      }
    }

    uasort($available, [SortArray::class, 'sortByWeightProperty']);

    return $available;
  }

  /**
   * Collect hook implementation paths.
   *
   * @return array
   *   Array of paths keyed by module name.
   */
  protected function collectImplementationPaths() {
    $paths = [];

    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
    $module_handler = \Drupal::getContainer()->get('module_handler');
    foreach ($module_handler->getModuleDirectories() as $name => $directory) {
      $candidate_dir = $directory . DIRECTORY_SEPARATOR . self::CONTENT_DIRECTORY;
      if (file_exists($candidate_dir)) {
        $paths[$name] = $candidate_dir;
      }
    }

    return $paths;
  }

  /**
   * Load all entities from the database.
   */
  protected function loadEntities($load_entities = TRUE) {
    $result = [];
    $data = Database::getConnection()
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
    $entity_type_manager = \Drupal::entityTypeManager();
    foreach ($result as $entity_type_id => $bundles) {
      foreach ($bundles as $bundle_id => $entity_ids) {
        $loaded_entities = $entity_type_manager
          ->getStorage($entity_type_id)
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
   * @param string $entity_type
   *   Override entity type with a custom value.
   * @param string $bundle
   *   Override bundle with a custom value.
   * @param bool $tracking
   *   Whether to track the entities.
   */
  protected function addEntity(EntityInterface $entity, $entity_type = NULL, $bundle = NULL, $tracking = TRUE) {
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
   * @param array $entities
   *   The array of entities.
   * @param bool $tracking
   *   Whether to track the entities.
   */
  public function addEntities(array $entities, $tracking = TRUE) {
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    foreach ($entities as $entity) {
      $this->addEntity($entity, NULL, NULL, $tracking);
    }
  }

  /**
   * Add multiple generated content entities to the repository without tracking.
   *
   * Used to update in-memory entities without writing them to DB.
   *
   * @param array $entities
   *   The array of entities.
   */
  public function addEntitiesNoTracking(array $entities) {
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    foreach ($entities as $entity) {
      $this->addEntity($entity, NULL, NULL, FALSE);
    }
  }

  /**
   * Ger generated content entities.
   *
   * @param string $entity_type
   *   Entity type ID, eg. node or taxonomy_term.
   * @param string $bundle
   *   Bundle, eg. Page, lading_page.
   * @param bool $reset
   *   Flag to reset internal cache.
   *
   * @return array
   *   The list of entities.
   */
  public function getEntities($entity_type = NULL, $bundle = NULL, $reset = FALSE) {
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
  protected function trackEntity(EntityInterface $entity) {
    try {
      $data = [
        'entity_type' => $entity->getEntityTypeId(),
        'bundle' => $entity->bundle(),
        'entity_id' => $entity->id(),
      ];
      Database::getConnection()->merge('generated_content')
        ->keys($data)
        ->updateFields($data)
        ->execute();
    }
    catch (\Exception $exception) {
      watchdog_exception('generated_content', $exception);
    }
  }

  /**
   * Remove all tracked entities.
   */
  protected function removeTrackedEntities($entity_type = NULL, $bundle = NULL, $entity_id = NULL) {
    $bundle = $bundle ?: $entity_type;

    try {
      if (!Database::getConnection()->schema()->tableExists('generated_content')) {
        return;
      }

      $query = Database::getConnection()->select('generated_content', 'gc')
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
          $entity = \Drupal::entityTypeManager()->getStorage($result['entity_type'])
            ->load($result['entity_id']);
          if ($entity) {
            $entity->delete();
            unset($this->entities[$entity_type][$bundle][$entity_id]);
          }
        }
        catch (\Exception $exception) {
          watchdog_exception('generated_content', $exception);
        }
      }
    }
    catch (\Exception $exception) {
      watchdog_exception('generated_content', $exception);
    }
  }

}
