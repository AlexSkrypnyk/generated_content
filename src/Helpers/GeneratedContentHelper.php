<?php

namespace Drupal\generated_content\Helpers;

use Drupal\Core\Url;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\taxonomy\Entity\Term;

/**
 * Class GeneratedContentHelper.
 *
 * Helper to interact with generated content items.
 *
 * @package \Drupal\generated_content
 */
class GeneratedContentHelper extends GeneratedContentAbstractHelper {

  /**
   * Select a random user.
   *
   * @return \Drupal\user\Entity\User
   *   The user object.
   */
  public static function randomUser() {
    $users = [1 => 1];
    $users += static::$repository->getEntities('user', 'user');

    return static::randomArrayItem($users);
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
    $nodes = static::$repository->getEntities('node', $type);

    if (!$type) {
      shuffle($nodes);
      $nodes = array_shift($nodes);
    }

    return static::randomArrayItem($nodes);
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
    $nodes = static::$repository->getEntities('node');

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

    return $count ? static::randomArrayItems($nodes, $count) : $nodes;
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
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of terms.
   */
  public static function randomRealTerms($vid, $count = NULL) {
    $terms = static::$entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => $vid]);

    return $count ? static::randomArrayItems($terms, $count) : $terms;
  }

  /**
   * Get random real term from the specified vocabulary.
   *
   * @param string $vid
   *   Vocabulary machine name.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|false
   *   The term.
   */
  public static function randomRealTerm($vid) {
    $terms = static::randomRealTerms($vid, 1);

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
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of terms.
   */
  public static function randomTerms($vid, $count = NULL) {
    $terms = static::$repository->getEntities('taxonomy_term', $vid);

    return $count ? static::randomArrayItems($terms, $count) : $terms;
  }

  /**
   * Get random generated term from the specified vocabulary.
   *
   * @param string $vid
   *   Vocabulary machine name.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|false
   *   The term.
   */
  public static function randomTerm($vid) {
    $terms = static::randomTerms($vid, 1);

    return !empty($terms) ? reset($terms) : NULL;
  }

  /**
   * Get static demo terms from the specified vocabulary.
   *
   * @param string $vid
   *   Vocabulary machine name.
   * @param int $count
   *   Optional term count to return.
   * @param int $offset
   *   Optional offset of the number of terms from the beginning.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of terms.
   */
  public static function staticTerms($vid, $count = NULL, $offset = 0) {
    $terms = self::$repository->getEntities('taxonomy_term', $vid);
    $offset = min(count($terms), $offset);

    return !is_null($count) ? array_slice($terms, $offset, $count) : $terms;
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

    $field_info = static::$entityTypeManager->getStorage('field_config')->load($entity_type . '.' . $bundle . '.' . $field_name);
    if ($field_info) {
      $allowed_values = $field_info->getFieldStorageDefinition()->getSetting('allowed_values');
    }

    $allowed_values = array_keys($allowed_values);

    return $count ? static::randomArrayItems($allowed_values, $count) : $allowed_values;
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
    $allowed_values = static::randomFieldAllowedValues($entity_type, $bundle, $field_name, 1);

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

    $field_info = static::$entityTypeManager->getStorage('field_config')->load($entity_type . '.' . $bundle . '.' . $field_name);
    if ($field_info) {
      if ($field_info->getType() == 'entity_reference_revisions' || $field_info->getType() == 'entity_reference') {
        $allowed_values = $field_info->getSetting('handler_settings')['target_bundles'];
      }
    }

    $allowed_values = array_keys($allowed_values);

    return $count ? static::randomArrayItems($allowed_values, $count) : $allowed_values;
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
    $allowed_values = static::randomFieldAllowedBundles($entity_type, $bundle, $field_name, 1);

    return !empty($allowed_values) ? reset($allowed_values) : NULL;
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
    $tree = static::$entityTypeManager->getStorage('taxonomy_term')->loadTree($vid, 0, $depth + 1, $load_entities);

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
        $terms += static::saveTermTree($vid, $subtree, $term->id());
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
        $created_mlids = array_merge($created_mlids, static::saveMenuTree($menu_name, $children, $menu_link));
      }
    }

    return $created_mlids;
  }

  /**
   * Create an image and store it as a managed file.
   */
  public static function createImage($options = []) {
    return static::$assetGenerator->createImage($options);
  }

  /**
   * Generate static file from existing file assets.
   */
  public static function staticFile($options) {
    return self::$assetGenerator->createFromDummyFile($options);
  }

  /**
   * Get static demo media of the specified bundle.
   *
   * @param string $bundle
   *   Bundle machine name.
   * @param int $count
   *   Optional media count to return.
   * @param int $offset
   *   Optional offset of the number of media from the beginning.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of media.
   */
  public static function staticMedia($bundle, $count = NULL, $offset = 0) {
    $items = self::$repository->getEntities('media', $bundle);
    $offset = min(count($items), $offset);

    return !is_null($count) ? array_slice($items, $offset, $count) : $items;
  }

}
