<?php

declare(strict_types = 1);

namespace Drupal\generated_content\Helpers;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\file\FileInterface;
use Drupal\generated_content\GeneratedContentRepository;
use Drupal\media\MediaInterface;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneratedContentHelper.
 *
 * Helper to interact with generated content items.
 *
 * @package \Drupal\generated_content
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class GeneratedContentHelper implements ContainerInjectionInterface {

  use GeneratedContentVariationTrait;
  use GeneratedContentRandomTrait;
  use GeneratedContentStaticTrait;

  /**
   * Instances of descendant classes.
   *
   * @var \Drupal\generated_content\Helpers\GeneratedContentHelper[]
   */
  protected static $instances = [];

  /**
   * The repository singleton.
   *
   * @var \Drupal\generated_content\GeneratedContentRepository
   */
  protected static $repository;

  /**
   * Asset generator.
   *
   * @var \Drupal\generated_content\Helpers\GeneratedContentAssetGenerator
   */
  protected static $assetGenerator;

  /**
   * Asset generator.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected static $messenger;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected static $entityTypeManager;

  /**
   * Use verbose mode.
   *
   * @var bool
   */
  protected static $verbose = TRUE;

  /**
   * Array of static offsets used to track calls to retrieve static items.
   *
   * @var array<mixed>
   */
  protected static $staticOffsets;

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
  public static function create(ContainerInterface $container): GeneratedContentHelper {
    // @phpstan-ignore-next-line
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
   * @return \Drupal\generated_content\Helpers\GeneratedContentHelper
   *   Helper class instance.
   */
  public static function getInstance(): GeneratedContentHelper {
    if (empty(self::$instances[static::class])) {
      // @phpstan-ignore-next-line
      self::$instances[static::class] = \Drupal::service('class_resolver')
        ->getInstanceFromDefinition(static::class);
    }
    // @phpstan-ignore-next-line
    return self::$instances[static::class];
  }

  /**
   * Reset singleton instance.
   *
   * @return \Drupal\generated_content\Helpers\GeneratedContentHelper
   *   A new singleton instance.
   */
  public function reset(): GeneratedContentHelper {
    static::$instances = [];
    static::$staticOffset = 0;
    static::$staticOffsets = [];

    return static::getInstance();
  }

  /**
   * Log verbose progress.
   */
  public static function log(): void {
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
  public static function addToRepository($entities): void {
    $entities = is_array($entities) ? $entities : [$entities];
    static::$repository->addEntitiesNoTracking($entities);
  }

  /**
   * Select a random generated user.
   *
   * @return \Drupal\user\UserInterface|null
   *   The user objector NULL if no entities were found.
   */
  public static function randomUser(): ?UserInterface {
    /** @var \Drupal\user\UserInterface[] $users */
    $users = static::randomEntities('user', 'user', 1);
    $user = reset($users);
    if ($user) {
      return $user;
    }
    return NULL;

  }

  /**
   * Select random generated users.
   *
   * @param int|null $count
   *   Number of users to return. If none provided - all users will be returned.
   *
   * @return \Drupal\user\UserInterface[]
   *   Array of user objects.
   */
  public static function randomUsers(int $count = NULL): array {
    /** @var \Drupal\user\UserInterface[] $users */
    $users = static::randomEntities('user', 'user', $count);

    return $users;
  }

  /**
   * Select a random real user.
   *
   * @return \Drupal\user\UserInterface|null
   *   The user object or NULL if no entities were found.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealUser(): ?UserInterface {
    /** @var \Drupal\user\UserInterface[] $users */
    $users = static::randomRealEntities('user', 'user', 1);
    $user = reset($users);

    if ($user) {
      return $user;
    }

    return NULL;
  }

  /**
   * Select random real users.
   *
   * @param int|null $count
   *   Number of users to return. If none provided - all users will be returned.
   *
   * @return \Drupal\user\UserInterface[]
   *   Array of user objects.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealUsers(int $count = NULL): array {
    /** @var \Drupal\user\UserInterface[] $users */
    $users = static::randomRealEntities('user', 'user', $count);

    return $users;
  }

  /**
   * Select a static user.
   *
   * @return \Drupal\user\UserInterface|null
   *   The user object or NULL if no entities were found.
   *
   * @throws \Exception
   */
  public static function staticUser(): ?UserInterface {
    /** @var \Drupal\user\UserInterface[] $users */
    $users = static::staticEntities('user', 'user', 1);
    $user = reset($users);

    if ($user) {
      return $user;
    }

    return NULL;
  }

  /**
   * Select static users.
   *
   * @param int|null $count
   *   Number of users to return. If none provided - all users will be returned.
   *
   * @return \Drupal\user\UserInterface[]
   *   Array of user objects.
   *
   * @throws \Exception
   */
  public static function staticUsers(int $count = NULL): array {
    /** @var \Drupal\user\UserInterface[] $users */
    $users = static::staticEntities('user', 'user', $count);

    return $users;
  }

  /**
   * Select a random node.
   *
   * @param string|null $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\node\NodeInterface|null
   *   Node entity object or NULL if no entities were found.
   */
  public static function randomNode(string $bundle = NULL): ?NodeInterface {
    /** @var \Drupal\node\NodeInterface[] $nodes */
    $nodes = static::randomEntities('node', $bundle, 1);
    $node = reset($nodes);

    if ($node) {
      return $node;
    }

    return NULL;
  }

  /**
   * Select random nodes.
   *
   * @param string|null $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   * @param int|null $count
   *   Optional count of Nodes. If FALSE, 20 Nodes will be returned.
   *
   * @return \Drupal\node\NodeInterface[]
   *   Array of node entities.
   */
  public static function randomNodes(string $bundle = NULL, ?int $count = 5): array {
    /** @var \Drupal\node\NodeInterface[] $nodes */
    $nodes = static::randomEntities('node', $bundle, $count);

    return $nodes;
  }

  /**
   * Select a random real node.
   *
   * @param string|null $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\node\NodeInterface|null
   *   Node entity object or NULL if no entities were found.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealNode(string $bundle = NULL): ?NodeInterface {
    /** @var \Drupal\node\NodeInterface[] $nodes */
    $nodes = static::randomRealEntities('node', $bundle, 1);
    $node = reset($nodes);

    if ($node) {
      return $node;
    }

    return NULL;
  }

  /**
   * Select random nodes.
   *
   * @param string|null $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   * @param int|null $count
   *   Optional count of Nodes. If FALSE, 5 Nodes will be returned.
   *
   * @return \Drupal\node\NodeInterface[]
   *   Array of node entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealNodes(string $bundle = NULL, ?int $count = 5): array {
    /** @var \Drupal\node\NodeInterface[] $nodes */
    $nodes = static::randomRealEntities('node', $bundle, $count);

    return $nodes;
  }

  /**
   * Select a static node.
   *
   * @param string|null $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The node object or NULL if no entities were found.
   *
   * @throws \Exception
   */
  public static function staticNode(string $bundle = NULL): ?NodeInterface {
    /** @var \Drupal\node\NodeInterface[] $nodes */
    $nodes = static::staticEntities('node', $bundle, 1);
    $node = reset($nodes);

    if ($node) {
      return $node;
    }

    return NULL;
  }

  /**
   * Select static nodes.
   *
   * @param string|null $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   * @param int|null $count
   *   Number of nodes to return. If none provided - all nodes will be returned.
   *
   * @return \Drupal\node\NodeInterface[]
   *   Array of node objects.
   *
   * @throws \Exception
   */
  public static function staticNodes(string $bundle = NULL, int $count = NULL): array {
    /** @var \Drupal\node\NodeInterface[] $nodes */
    $nodes = static::staticEntities('node', $bundle, $count);

    return $nodes;
  }

  /**
   * Select a random term.
   *
   * @param string|null $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   *
   * @return \Drupal\taxonomy\TermInterface|null
   *   Term entity object or NULL if no entities were found.
   */
  public static function randomTerm(string $vid = NULL): ?TermInterface {
    /** @var \Drupal\taxonomy\TermInterface[] $terms */
    $terms = static::randomEntities('taxonomy_term', $vid, 1);
    $term = reset($terms);

    if ($term) {
      return $term;
    }

    return NULL;
  }

  /**
   * Select random terms.
   *
   * @param string|null $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   * @param null|int $count
   *   Optional count of Terms. If FALSE, 20 Terms will be returned.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   Array of term entities.
   */
  public static function randomTerms(string $vid = NULL, $count = 5): array {
    /** @var \Drupal\taxonomy\TermInterface[] $terms */
    $terms = static::randomEntities('taxonomy_term', $vid, $count);

    return $terms;
  }

  /**
   * Select a random real term.
   *
   * @param string|null $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   *
   * @return \Drupal\taxonomy\TermInterface|null
   *   Term entity object or NULL if no entities were found.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealTerm(string $vid = NULL): ?TermInterface {
    /** @var \Drupal\taxonomy\TermInterface[] $terms */
    $terms = static::randomRealEntities('taxonomy_term', $vid, 1);
    $term = reset($terms);

    if ($term) {
      return $term;
    }

    return NULL;
  }

  /**
   * Select random terms.
   *
   * @param string|null $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   * @param null|int $count
   *   Optional count of Terms. If FALSE, 5 Terms will be returned.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   Array of term entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealTerms(string $vid = NULL, $count = 5): array {
    /** @var \Drupal\taxonomy\TermInterface[] $terms */
    $terms = static::randomRealEntities('taxonomy_term', $vid, $count);

    return $terms;
  }

  /**
   * Select a static term.
   *
   * @param string|null $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   *
   * @return \Drupal\taxonomy\TermInterface|null
   *   The term object or NULL if no entities were found.
   *
   * @throws \Exception
   */
  public static function staticTerm(string $vid = NULL): ?TermInterface {
    /** @var \Drupal\taxonomy\TermInterface[] $terms */
    $terms = static::staticEntities('taxonomy_term', $vid, 1);
    $term = reset($terms);
    if ($term) {
      return $term;
    }

    return NULL;
  }

  /**
   * Select static terms.
   *
   * @param string|null $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   * @param int|null $count
   *   Number of terms to return. If none provided - all terms will be returned.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   Array of term objects.
   *
   * @throws \Exception
   */
  public static function staticTerms(string $vid = NULL, int $count = NULL): array {
    /** @var \Drupal\taxonomy\TermInterface[] $terms */
    $terms = static::staticEntities('taxonomy_term', $vid, $count);

    return $terms;
  }

  /**
   * Select a random media item.
   *
   * @param string|null $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\media\MediaInterface|null
   *   Media entity object or NULL if no entities were found.
   */
  public static function randomMediaItem(string $bundle = NULL): ?MediaInterface {
    /** @var \Drupal\media\MediaInterface[] $medias */
    $medias = static::randomEntities('media', $bundle, 1);
    $media = reset($medias);

    if ($media) {
      return $media;
    }

    return NULL;
  }

  /**
   * Select random media items.
   *
   * @param string|null $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   * @param int|null $count
   *   Optional count of Medias. If FALSE, 20 Medias will be returned.
   *
   * @return \Drupal\media\MediaInterface[]
   *   Array of media entities.
   */
  public static function randomMediaItems(string $bundle = NULL, ?int $count = 5): array {
    /** @var \Drupal\media\MediaInterface[] $medias */
    $medias = static::randomEntities('media', $bundle, $count);

    return $medias;
  }

  /**
   * Select a random real media item.
   *
   * @param string|null $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\media\MediaInterface|null
   *   Media entity object or NULL if no entities were found.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealMediaItem(string $bundle = NULL): ?MediaInterface {
    /** @var \Drupal\media\MediaInterface[] $medias */
    $medias = static::randomRealEntities('media', $bundle, 1);
    $media = reset($medias);

    if ($media) {
      return $media;
    }

    return NULL;
  }

  /**
   * Select random real media items.
   *
   * @param string|null $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   * @param int|null $count
   *   Optional count of Medias. If FALSE, 5 Medias will be returned.
   *
   * @return \Drupal\media\MediaInterface[]
   *   Array of media entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealMediaItems(string $bundle = NULL, ?int $count = 5): array {
    /** @var \Drupal\media\MediaInterface[] $medias */
    $medias = static::randomRealEntities('media', $bundle, $count);

    return $medias;
  }

  /**
   * Select a static media item.
   *
   * @param string|null $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\media\MediaInterface|null
   *   The media object or NULL if no entities were found.
   *
   * @throws \Exception
   */
  public static function staticMediaItem(string $bundle = NULL): ?MediaInterface {
    /** @var \Drupal\media\MediaInterface[] $medias */
    $medias = static::staticEntities('media', $bundle, 1);
    $media = reset($medias);

    if ($media) {
      return $media;
    }

    return NULL;
  }

  /**
   * Select static media items.
   *
   * @param string|null $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   * @param int|null $count
   *   Number of medias to return. If none provided - all medias will be
   *   returned.
   *
   * @return \Drupal\media\MediaInterface[]
   *   Array of media objects.
   *
   * @throws \Exception
   */
  public static function staticMediaItems(string $bundle = NULL, int $count = NULL): array {
    /** @var \Drupal\media\MediaInterface[] $medias */
    $medias = static::staticEntities('media', $bundle, $count);

    return $medias;
  }

  /**
   * Select a random file.
   *
   * @param string|null $extension
   *   The extension of the file to return. If not provided - a file with a
   *   random available extension will be returned.
   *
   * @return \Drupal\file\FileInterface|null
   *   File entity object or NULL if no entities were found.
   */
  public static function randomFile(string $extension = NULL): ?FileInterface {
    /** @var \Drupal\file\FileInterface[] $files */
    $files = static::randomFiles($extension, 1);
    $file = reset($files);

    if ($file) {
      return $file;
    }

    return NULL;
  }

  /**
   * Select random files.
   *
   * @param string|null $extension
   *   The extension of the file to return. If not provided - a file with a
   *   random available extension will be returned.
   * @param int|null $count
   *   Optional count of Files. If FALSE, 20 Files will be returned.
   *
   * @return \Drupal\file\FileInterface[]
   *   Array of file entities.
   */
  public static function randomFiles(string $extension = NULL, ?int $count = 5): array {
    /** @var \Drupal\file\FileInterface[] $files */
    $files = static::randomEntities('file');

    return static::filterFilesByExtension($files, $extension, $count);
  }

  /**
   * Select a random real file.
   *
   * @param string|null $extension
   *   The extension of the file to return. If not provided - a file with a
   *   random available extension will be returned.
   *
   * @return \Drupal\file\FileInterface|null
   *   File entity object or NULL if no entities were found.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealFile(string $extension = NULL): ?FileInterface {
    $files = static::randomRealFiles($extension, 1);
    $file = reset($files);

    if ($file) {
      return $file;
    }

    return NULL;
  }

  /**
   * Select random real files.
   *
   * @param string|null $extension
   *   The extension of the file to return. If not provided - a file with a
   *   random available extension will be returned.
   * @param int|null $count
   *   Optional count of Files. If FALSE, 5 Files will be returned.
   *
   * @return \Drupal\file\FileInterface[]
   *   Array of file entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomRealFiles(string $extension = NULL, ?int $count = 5): array {
    /** @var \Drupal\file\FileInterface[] $files */
    $files = static::randomRealEntities('file');

    return static::filterFilesByExtension($files, $extension, $count);
  }

  /**
   * Select a static file.
   *
   * @param string|null $extension
   *   The extension of the file to return. If not provided - a file with a
   *   random available extension will be returned.
   *
   * @return \Drupal\file\FileInterface|null
   *   The file object or NULL if no entities were found.
   *
   * @throws \Exception
   */
  public static function staticFile(string $extension = NULL): ?FileInterface {
    $files = static::staticFiles($extension, 1);
    $file = reset($files);

    if ($file) {
      return $file;
    }

    return NULL;
  }

  /**
   * Select static files.
   *
   * @param string|null $extension
   *   The extension of the file to return. If not provided - a file with a
   *   random available extension will be returned.
   * @param int|null $count
   *   Number of files to return. If none provided - all files will be returned.
   *
   * @return \Drupal\file\FileInterface[]
   *   Array of file objects.
   *
   * @throws \Exception
   */
  public static function staticFiles(string $extension = NULL, int $count = NULL): array {
    // Because extension is not an entity bundle, filtering files by extension
    // requires working with a full set of entities _before_ they can be
    // filtered by extension and filtered further by static index and count.
    /** @var \Drupal\file\FileInterface[] $files */
    $files = static::$repository->getEntities('file', 'file');

    $files = static::filterFilesByExtension($files, $extension);

    return static::filterStaticItems($files, 'file', $extension, $count);
  }

  /**
   * Filter files by extension.
   *
   * @param \Drupal\file\FileInterface[] $files
   *   Array of File objects.
   * @param string|null $extension
   *   Extension without a leading dot to filter by.
   * @param int|null $count
   *   Optional number of items to return after filtering. If NULL - all
   *   filtered-out items will be returned.
   *
   * @return \Drupal\file\FileInterface[]
   *   Array of File objects filtered by the extension.
   */
  protected static function filterFilesByExtension(array $files, ?string $extension, int $count = NULL): array {
    if (!is_null($extension)) {
      foreach ($files as $k => $file) {
        $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        if ($ext != $extension) {
          unset($files[$k]);
        }
      }
    }

    if (!is_null($count)) {
      $count = max(0, min($count, count($files)));
    }

    return is_null($count) ? $files : array_slice($files, 0, $count);
  }

  /**
   * Get random allowed value from the field.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $field_name
   *   The field name.
   *
   * @return string|null
   *   A single allowed value.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomFieldAllowedValue(string $entity_type, string $bundle, string $field_name): ?string {
    $allowed_values = static::randomFieldAllowedValues($entity_type, $bundle, $field_name, 1);

    return !empty($allowed_values) ? reset($allowed_values) : NULL;
  }

  /**
   * Get random allowed values from the field.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $field_name
   *   The field name.
   * @param int|null $count
   *   Optional values count to return. If NULL - all values will be returned.
   *   If specified - this count of already randomised values will be returned.
   *
   * @return array<mixed>
   *   Array of allowed values.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function randomFieldAllowedValues(string $entity_type, string $bundle, string $field_name, int $count = NULL): array {
    $allowed_values = static::fieldAllowedValues($entity_type, $bundle, $field_name);

    return $count ? static::randomArrayItems($allowed_values, $count) : $allowed_values;
  }

  /**
   * Get static allowed value from the field.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $field_name
   *   The field name.
   *
   * @return string|null
   *   A single allowed value.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function staticFieldAllowedValue(string $entity_type, string $bundle, string $field_name): ?string {
    $allowed_values = static::staticFieldAllowedValues($entity_type, $bundle, $field_name, 1);

    return !empty($allowed_values) ? reset($allowed_values) : NULL;
  }

  /**
   * Get static allowed values from the field.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $field_name
   *   The field name.
   * @param int|null $count
   *   Optional values count to return. If NULL - all values will be returned.
   *   If specified - this count of already randomised values will be returned.
   *
   * @return array<mixed>
   *   Array of allowed values.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function staticFieldAllowedValues(string $entity_type, string $bundle, string $field_name, int $count = NULL): array {
    $allowed_values = static::fieldAllowedValues($entity_type, $bundle, $field_name);

    $idx = static::getStaticOffset($entity_type, $bundle, $field_name);

    if (is_null($count)) {
      $return = self::arraySliceCircular($allowed_values, count($allowed_values), $idx);
    }
    else {
      $return = self::arraySliceCircular($allowed_values, $count, $idx);
    }

    static::setStaticOffset(count($return), $entity_type, $bundle, $field_name);

    return $return;
  }

  /**
   * Create a file and store it as a managed file.
   *
   * @param string $type
   *   File type as per GeneratedContentAssetGenerator::ASSET_TYPE_* constants.
   * @param array<mixed> $options
   *   Array of options to pass to the asset generator.
   * @param string $generation_type
   *   Generation type as
   *   per GeneratedContentAssetGenerator::GENERATE_TYPE_* constants.
   *
   * @return \Drupal\file\FileInterface
   *   Created managed file.
   *
   * @throws \Exception
   */
  public static function createFile(string $type, array $options = [], string $generation_type = GeneratedContentAssetGenerator::GENERATE_TYPE_RANDOM): FileInterface {
    $return = [];

    // Track the asset offset of statically generated files.
    if ($generation_type == GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC) {
      $assets = static::$assetGenerator->getAssets($type);
      $asset_indices = array_keys($assets);

      $idx = static::getStaticOffset('file_asset', $type);

      $return = self::arraySliceCircular($asset_indices, 1, $idx);

      $options += [
        'index' => $return[0] ?? 0,
      ];
    }

    $file = static::$assetGenerator->generate($type, $options, $generation_type);

    // Track the asset offset of statically generated files.
    if ($generation_type == GeneratedContentAssetGenerator::GENERATE_TYPE_STATIC) {
      static::setStaticOffset(count($return), 'file_asset', $type);
    }

    return $file;
  }

  /**
   * Replace string tokens.
   *
   * @param string $string
   *   String to process.
   * @param array<string, string> $replacements
   *   Array of replacements with keys as tokens and values as replacements.
   * @param callable|null $cb
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
  public static function replaceTokens(string $string, array $replacements, callable $cb = NULL, string $beginToken = '{', string $endToken = '}'): string {
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
   * Save terms, specified as simplified term tree.
   *
   * @param string $vid
   *   Vocabulary machine name.
   * @param array<mixed> $tree
   *   Array of tree items, where keys with array values are considered parent
   *   terms.
   * @param bool|int|string $parent_tid
   *   Internal parameter used for recursive calls.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   Array of saved terms, keyed by term id.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function saveTermTree(string $vid, array $tree, $parent_tid = FALSE): array {
    $terms = [];
    $weight = 0;

    foreach ($tree as $parent => $subtree) {
      $term = static::$entityTypeManager->getStorage('taxonomy_term')->create([
        'name' => is_array($subtree) ? $parent : $subtree,
        'vid' => $vid,
        'weight' => $weight,
        'parent' => $parent_tid !== FALSE ? $parent_tid : 0,
      ]);

      $term->save();

      if ($term->id() === NULL) {
        continue;
      }

      $terms[$term->id()] = $term;

      if (is_array($subtree)) {
        $terms += static::saveTermTree($vid, $subtree, $term->id());
      }

      $weight++;
    }

    return $terms;
  }

  /**
   * Get terms at the specific depth.
   *
   * @param string $vid
   *   Vocabulary machine name.
   * @param int $depth
   *   Terms depth.
   * @param bool $load_entities
   *   If TRUE, a full entity load will occur on the term objects. Otherwise
   *   they are partial objects queried directly from the {taxonomy_term_data}
   *   table to save execution time and memory consumption when listing large
   *   numbers of terms. Defaults to FALSE.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of terms at depth.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTermsAtDepth(string $vid, int $depth, bool $load_entities = FALSE): array {
    $depth = max($depth, 0);

    // Note that we are asking for an item 1 level deeper because this is
    // how loadTree() calculates max depth.
    /** @var \Drupal\taxonomy\Entity\Term[] $tree */
    $tree = static::$entityTypeManager->getStorage('taxonomy_term')->loadTree($vid, 0, $depth + 1, $load_entities);

    foreach ($tree as $k => $leaf) {
      // @phpstan-ignore-next-line
      if ($leaf->depth != $depth) {
        unset($tree[$k]);
      }
    }

    return $tree;
  }

  /**
   * Create links from the provided tree.
   *
   * @code
   * $tree = [
   *   'Item1' => [
   *     'link' => '/path-to-item1',
   *     'children' => [
   *       'Subitem 1' => '/path-to-subitem1',
   *       'Subitem 2' => '/path-to-subitem2',
   *     ],
   *   'Item2' => '/path-to-item2',
   * ];
   * Menu::import('main-menu', $tree);
   * @endcode
   *
   * @param string $menu_name
   *   String machine menu name.
   * @param array<mixed> $tree
   *   Array of links with keys as titles and values as paths or full link
   *   item array definitions. 'children' key is used to specify children menu
   *   levels.
   * @param \Drupal\menu_link_content\Entity\MenuLinkContent|null $parent_menu_link
   *   Internal. Parent menu link item.
   *
   * @return array<mixed>
   *   Array of created mlids.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function saveMenuTree(string $menu_name, array $tree, MenuLinkContent $parent_menu_link = NULL): array {
    $created_mlids = [];
    $weight = 0;
    foreach ($tree as $title => $leaf) {
      $leaf = is_array($leaf) ? $leaf : ['link' => $leaf];

      if (!isset($leaf['link'])) {
        throw new \InvalidArgumentException('Menu item does not contain "link" element');
      }

      if (is_array($leaf['link']) && !isset($leaf['link']['uri'])) {
        throw new \InvalidArgumentException('Menu item contains "link" element which does not contain "uri" value');
      }

      // @phpstan-ignore-next-line
      if (!is_array($leaf['link'])) {
        $leaf['link'] = ['uri' => $leaf['link']];
      }

      // Try to convert scalar link to \Drupal Url object.
      if (is_string($leaf['link']['uri'])) {
        $leaf['link']['uri'] = Url::fromUserInput($leaf['link']['uri'])->toUriString();
      }

      $leaf_defaults = [
        'menu_name' => $menu_name,
        'title' => $title,
        'weight' => $weight,
      ];
      if ($parent_menu_link) {
        $leaf_defaults['parent'] = 'menu_link_content:' . $parent_menu_link->uuid();
      }

      $leaf += $leaf_defaults;

      $children = $leaf['children'] ?? [];
      unset($leaf['children']);
      if ($children) {
        $leaf += ['expanded' => TRUE];
      }

      /** @var \Drupal\menu_link_content\Entity\MenuLinkContent $menu_link */
      $menu_link = static::$entityTypeManager->getStorage('menu_link_content')->create($leaf);
      $menu_link->save();
      $mlid = $menu_link->id();
      if (!$mlid) {
        continue;
      }
      $created_mlids[] = $mlid;
      $weight++;
      if ($children) {
        $created_mlids = array_merge($created_mlids, static::saveMenuTree($menu_name, $children, $menu_link));
      }
    }

    return $created_mlids;
  }

  /**
   * Get allowed values from  field.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Entity bundle.
   * @param string $field_name
   *   Field name.
   *
   * @return array<mixed>
   *   Array of allowed values.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected static function fieldAllowedValues(string $entity_type, string $bundle, string $field_name): array {
    $allowed_values = [];

    $field_info = static::$entityTypeManager->getStorage('field_config')->load($entity_type . '.' . $bundle . '.' . $field_name);
    if ($field_info) {
      $allowed_values = $field_info->getFieldStorageDefinition()->getSetting('allowed_values');
    }

    return array_keys($allowed_values);
  }

  /**
   * Get random generated entities.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string|null $bundle
   *   Entity bundle. If none provided - all entities of this type will be
   *   returned (further limited by $count).
   * @param int|null $count
   *   Number of entities to return. If none provided - all entities will be
   *   returned.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of entities.
   */
  protected static function randomEntities(string $entity_type, string $bundle = NULL, int $count = NULL): array {
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
   * @param string|null $bundle
   *   Entity bundle. If none provided - all entities of this type will be
   *   returned.
   * @param int|null $count
   *   Number of entities to return. If none provided - all entities will be
   *   returned.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of entities.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected static function randomRealEntities(string $entity_type, string $bundle = NULL, int $count = NULL): array {
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
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   Entities.
   * @param string $entity_type
   *   Type.
   * @param string|null $bundle
   *   Bundle.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Generated entities.
   */
  protected static function filterOutGeneratedContentEntities(array $entities, string $entity_type, string $bundle = NULL): array {
    $generated_entities = static::$repository->getEntities($entity_type, $bundle);

    if (!empty($generated_entities) && !$bundle) {
      $entities_all = [];
      foreach ($generated_entities as $bundled_entities) {
        $entities_all = array_merge($entities_all, $bundled_entities);
      }
      $generated_entities = $entities_all;
    }

    $generated_entities_ids = array_filter(array_map(function ($value) {
      return $value instanceof EntityInterface ? $value->id() : NULL;
    }, $generated_entities));

    $entities_ids = array_filter(array_map(function ($value) {
      // @phpstan-ignore-next-line
      return $value instanceof EntityInterface ? $value->id() : NULL;
    }, $entities));

    $non_generated_ids = array_diff($entities_ids, $generated_entities_ids);

    return array_intersect_key($entities, array_flip($non_generated_ids));
  }

  /**
   * Get static entities.
   *
   * Static entities are entities within a repository, returned in a predictable
   * order based on the called argument list. So, calls with and without $bundle
   * value are tracked separately.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string|null $bundle
   *   Entity bundle. If none provided - all entities of this type will be
   *   returned.
   * @param int|null $count
   *   Number of entities to return. If NULL- all entities will be
   *   returned with previously used offsets.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of entities.
   *
   * @throws \Exception
   */
  protected static function staticEntities(string $entity_type, string $bundle = NULL, int $count = NULL): array {
    $entities = static::$repository->getEntities($entity_type, $bundle);

    return static::filterStaticItems($entities, $entity_type, $bundle, $count);
  }

  /**
   * Filter static items.
   *
   * Static items returned in a predictable order based on the called argument
   * list. So, calls with and without $subtype value are tracked separately.
   *
   * @param array<mixed> $items
   *   Array of items to filter.
   * @param string $type
   *   Type to filter by.
   * @param string|null $subtype
   *   An optional sub-type to filter by.
   * @param int|null $count
   *   Number of items to return. If NULL - all entities will be
   *   returned with previously used offsets.
   *
   * @return array<mixed>
   *   Array of items.
   *
   * @throws \Exception
   */
  protected static function filterStaticItems(array $items, string $type, string $subtype = NULL, int $count = NULL): array {
    // Merge all items if subtype was not provided.
    if (!empty($items) && !$subtype) {
      $items_all = [];
      $items_merged = 0;
      foreach ($items as $typed_items) {
        // There may be a case when subtype was not provided abd $items is
        // already flattened - need to discover this case and check if every
        // element of the array is another array that needs to be merged.
        if (is_array($typed_items)) {
          $items_all = array_merge($items_all, $typed_items);
          $items_merged++;
        }
      }

      if ($items_merged > 0 && count($items) != $items_merged) {
        throw new \Exception(sprintf('Mixed data provided when trying to merge %s static items.', $type));
      }
      elseif ($items_merged > 0) {
        $items = $items_all;
      }
    }

    $idx = static::getStaticOffset($type, $subtype);

    if (is_null($count)) {
      $return = self::arraySliceCircular($items, count($items), $idx);
    }
    else {
      $return = self::arraySliceCircular($items, $count, $idx);
    }

    static::setStaticOffset(count($return), $type, $subtype);

    return $return;
  }

  /**
   * Set static entity offset.
   *
   * Note that offsets with different number of arguments are tracked
   * separately.
   *
   * @param mixed ...$arguments
   *   A list of properties to track.
   *
   * @return int
   *   Offset value.
   */
  protected static function getStaticOffset(...$arguments): int {
    $key = implode('__', func_get_args());
    self::$staticOffsets[$key] = self::$staticOffsets[$key] ?? 0;

    return self::$staticOffsets[$key];
  }

  /**
   * Set static offset to a value.
   *
   * Note that this will further offset any existing offsets by an
   * $offset value.
   *
   * @param mixed ...$arguments
   *   A list of properties to track. First argument being the offset.
   */
  protected static function setStaticOffset(...$arguments): void {
    $args = func_get_args();
    $offset = array_shift($args);
    $key = implode('__', $args);
    self::$staticOffsets[$key] = self::$staticOffsets[$key] ?? 0;
    self::$staticOffsets[$key] += $offset;
  }

  /**
   * Slice array as a circular array.
   *
   * @param array<mixed> $array
   *   Array to slice.
   * @param int $count
   *   Number of items to return.
   * @param int $offset
   *   Optional offset to start slicing.
   *
   * @return array<mixed>
   *   Sliced array with reset keys starting from 0.
   */
  protected static function arraySliceCircular(array $array, int $count, int $offset = 0): array {
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
