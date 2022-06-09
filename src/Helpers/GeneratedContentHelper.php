<?php

namespace Drupal\generated_content\Helpers;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Generator\GeneratedContentRandom;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneratedContentHelper.
 *
 * Helper to interact with generated content items.
 *
 * @package \Drupal\generated_content
 */
class GeneratedContentHelper implements ContainerInjectionInterface {

  use GeneratedContentVariationTrait;

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
   * Use verbose mode.
   *
   * @var bool
   */
  protected static $verbose = TRUE;

  /**
   * GeneratedContentHelper constructor.
   */
  public function __construct(GeneratedContentRepository $repository) {
    static::$repository = $repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      GeneratedContentRepository::getInstance()
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
        \Drupal::messenger()->addMessage(new FormattableMarkup(call_user_func_array('sprintf', func_get_args()), []));
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
   * Select a random user.
   *
   * @return \Drupal\user\Entity\User
   *   The user object.
   */
  public static function randomUser() {
    $users = [1 => 1];
    $users += self::$repository->getEntities('user', 'user');

    return GeneratedContentRandom::arrayItem($users);
  }

  /**
   * Select a random node.
   *
   * @param string $type
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\node\Entity\Node
   *   Node entity.
   */
  public static function randomNode($type = NULL) {
    $nodes = self::$repository->getEntities('node', $type);

    if (!$type) {
      shuffle($nodes);
      $nodes = array_shift($nodes);
    }

    return GeneratedContentRandom::arrayItem($nodes);
  }

  /**
   * Select random nodes.
   *
   * @param bool|int $count
   *   Optional count of Nodes. If FALSE, 20 Nodes will be returned.
   * @param array $types
   *   (optional) Array of types to filter. Defaults to FALSE, meaning that
   *   returned nodes will not be filtered.
   *
   * @return \Drupal\node\Entity\Node[]
   *   Array of media entities.
   */
  public static function randomNodes($count = 20, array $types = []) {
    $nodes = self::$repository->getEntities('node');

    if (!empty($types)) {
      $filtered_nodes = [];
      foreach ($nodes as $k => $node) {
        if (!in_array($k, $types)) {
          unset($nodes[$k]);
          continue;
        }
        $filtered_nodes = array_merge($filtered_nodes, $nodes[$k]);
      }
      $nodes = $filtered_nodes;
    }

    return $count ? GeneratedContentRandom::arrayItems($nodes, $count) : $nodes;
  }

  /**
   * Get random real terms from the specified vocabulary.
   *
   * @param string $vid
   *   Vocabulary machine name.
   * @param int|null $count
   *   Optional term count to return. If NULL - all terms will be returned.
   *   If specified - this count of already randomised terms will be returned.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of terms.
   */
  public static function randomRealTerms($vid, $count = NULL) {
    $terms = \Drupal::service('entity_type.manager')
      ->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => $vid]);

    return $count ? GeneratedContentRandom::arrayItems($terms, $count) : $terms;
  }

  /**
   * Get random real term from the specified vocabulary.
   *
   * @param string $vid
   *   Vocabulary machine name.
   *
   * @return \Drupal\taxonomy\Entity\Term
   *   The term.
   */
  public static function randomRealTerm($vid) {
    $terms = self::randomRealTerms($vid, 1);

    return !empty($terms) ? reset($terms) : NULL;
  }

  /**
   * Get random generated terms from the specified vocabulary.
   *
   * @param string $vid
   *   Vocabulary machine name.
   * @param int|null $count
   *   Optional term count to return. If NULL - all terms will be returned.
   *   If specified - this count of already randomised terms will be returned.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of terms.
   */
  public static function randomTerms($vid, $count = NULL) {
    $terms = self::$repository->getEntities('taxonomy_term', $vid);

    return $count ? GeneratedContentRandom::arrayItems($terms, $count) : $terms;
  }

  /**
   * Get random generated term from the specified vocabulary.
   *
   * @param string $vid
   *   Vocabulary machine name.
   *
   * @return \Drupal\taxonomy\Entity\Term
   *   The term.
   */
  public static function randomTerm($vid) {
    $terms = self::randomTerms($vid, 1);

    return !empty($terms) ? reset($terms) : NULL;
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
   * @return array
   *   Array of allowed values.
   */
  public static function randomFieldAllowedValues($entity_type, $bundle, $field_name, $count = NULL) {
    $allowed_values = [];

    $field_info = FieldConfig::loadByName($entity_type, $bundle, $field_name);
    if ($field_info) {
      $allowed_values = $field_info->getFieldStorageDefinition()->getSetting('allowed_values');
    }

    $allowed_values = array_keys($allowed_values);

    return $count ? GeneratedContentRandom::arrayItems($allowed_values, $count) : $allowed_values;
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
   * @return array
   *   A single allowed value.
   */
  public static function randomFieldAllowedValue($entity_type, $bundle, $field_name) {
    $allowed_values = self::randomFieldAllowedValues($entity_type, $bundle, $field_name, 1);

    return !empty($allowed_values) ? reset($allowed_values) : NULL;
  }

  /**
   * Get random allowed target bundles from the field.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $field_name
   *   The field name.
   * @param null|int $count
   *   Optional values count to return. If NULL - all values will be returned.
   *   If specified - this count of already randomised values will be returned.
   *
   * @return array
   *   Array of allowed values.
   */
  public static function randomFieldAllowedBundles($entity_type, $bundle, $field_name, $count = NULL) {
    $allowed_values = [];

    $field_info = FieldConfig::loadByName($entity_type, $bundle, $field_name);
    if ($field_info) {
      if ($field_info->getType() == 'entity_reference_revisions' || $field_info->getType() == 'entity_reference') {
        $allowed_values = $field_info->getSetting('handler_settings')['target_bundles'];
      }
    }

    $allowed_values = array_keys($allowed_values);

    return $count ? GeneratedContentRandom::arrayItems($allowed_values, $count) : $allowed_values;
  }

  /**
   * Get random allowed target bundle from the field.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $field_name
   *   The field name.
   *
   * @return array
   *   A single allowed value.
   */
  public static function randomFieldAllowedBundle($entity_type, $bundle, $field_name) {
    $allowed_values = self::randomFieldAllowedBundles($entity_type, $bundle, $field_name, 1);

    return !empty($allowed_values) ? reset($allowed_values) : NULL;
  }

  /**
   * Filter only generated entities.
   */
  protected static function filterGeneratedContentEntities($entities, $entity_type, $bundle) {
    $generated_entities = self::$repository->getEntities($entity_type, $bundle);
    $generated_entities = array_map(function ($value) {
      return is_scalar($value) ? ['id' => $value] : $value;
    }, $generated_entities);

    return self::arrayIntersectColumn('id', $entities, $generated_entities);
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
   */
  public static function getTermsAtDepth($vid, $depth, $load_entities = FALSE) {
    $depth = $depth < 0 ? 0 : $depth;

    // Note that we are asking for an item 1 level deeper because this is
    // how loadTree() calculates max depth.
    /** @var \Drupal\taxonomy\Entity\Term[] $tree */
    $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0, $depth + 1, $load_entities);

    foreach ($tree as $k => $leaf) {
      if ($leaf->depth != $depth) {
        unset($tree[$k]);
      }
    }

    return $tree;
  }

  /**
   * Save terms, specified as simplified term tree.
   *
   * @param string $vid
   *   Vocabulary machine name.
   * @param array $tree
   *   Array of tree items, where keys with array values are considered parent
   *   terms.
   * @param bool|int $parent_tid
   *   Internal parameter used for recursive calls.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of saved terms, keyed by term id.
   */
  public static function saveTermTree($vid, array $tree, $parent_tid = FALSE) {
    $terms = [];
    $weight = 0;

    foreach ($tree as $parent => $subtree) {
      $term = Term::create([
        'name' => is_array($subtree) ? $parent : $subtree,
        'vid' => $vid,
        'weight' => $weight,
        'parent' => $parent_tid !== FALSE ? $parent_tid : 0,
      ]);

      $term->save();
      $terms[$term->id()] = $term;

      if (is_array($subtree)) {
        $terms += self::saveTermTree($vid, $subtree, $term->id());
      }

      $weight++;
    }

    return $terms;
  }

  /**
   * Import links from the provided tree.
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
   * @param array $tree
   *   Array of links with keys as titles and values as paths or full link
   *   item array definitions. 'children' key is used to specify children menu
   *   levels.
   * @param \Drupal\menu_link_content\Entity\MenuLinkContent $parent_menu_link
   *   Internal. Parent menu link item.
   *
   * @return array
   *   Array of created mlids.
   */
  public static function saveMenuTree($menu_name, array $tree, MenuLinkContent $parent_menu_link = NULL) {
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

      $menu_link = MenuLinkContent::create($leaf);
      $menu_link->save();
      $mlid = $menu_link->id();
      if (!$mlid) {
        continue;
      }
      $created_mlids[] = $mlid;
      $weight++;
      if ($children) {
        $created_mlids = array_merge($created_mlids, self::saveMenuTree($menu_name, $children, $menu_link));
      }
    }

    return $created_mlids;
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
