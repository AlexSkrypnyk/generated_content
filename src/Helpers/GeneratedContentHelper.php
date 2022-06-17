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
   * Select a random generated user.
   *
   * @return \Drupal\user\Entity\User|null
   *   The user objector NULL if no entities were found.
   */
  public static function randomUser() {
    $entities = static::randomEntities('user', 'user', 1);

    return count($entities) > 0 ? reset($entities) : NULL;

  }

  /**
   * Select random generated users.
   *
   * @param null|int $count
   *   Number of users to return. If none provided - all users will be returned.
   *
   * @return \Drupal\user\Entity\User[]
   *   Array of user objects.
   */
  public static function randomUsers($count = NULL) {
    return static::randomEntities('user', 'user', $count);
  }

  /**
   * Select a random real user.
   *
   * @return \Drupal\user\Entity\User|null
   *   The user object or NULL if no entities were found.
   */
  public static function randomRealUser() {
    $entities = static::randomRealEntities('user', 'user', 1);

    return count($entities) > 0 ? reset($entities) : NULL;

  }

  /**
   * Select random real users.
   *
   * @param null|int $count
   *   Number of users to return. If none provided - all users will be returned.
   *
   * @return \Drupal\user\Entity\User[]
   *   Array of user objects.
   */
  public static function randomRealUsers($count = NULL) {
    return static::randomRealEntities('user', 'user', $count);
  }

  /**
   * Select a static user.
   *
   * @return \Drupal\user\Entity\User|null
   *   The user object or NULL if no entities were found.
   */
  public static function staticUser() {
    $entities = static::staticEntities('user', 'user', 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select a static user.
   *
   * @param null|int $count
   *   Number of users to return. If none provided - all users will be returned.
   *
   * @return \Drupal\user\Entity\User[]
   *   Array of user objects.
   */
  public static function staticUsers($count = NULL) {
    return static::staticEntities('user', 'user', $count);
  }

  /**
   * Select a random node.
   *
   * @param string $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\node\Entity\Node|null
   *   Node entity object or NULL if no entities were found.
   */
  public static function randomNode($bundle = NULL) {
    $entities = static::randomEntities('node', $bundle, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select random nodes.
   *
   * @param string $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   * @param bool|int $count
   *   Optional count of Nodes. If FALSE, 20 Nodes will be returned.
   *
   * @return \Drupal\node\Entity\Node[]
   *   Array of node entities.
   */
  public static function randomNodes($bundle = NULL, $count = 5) {
    return static::randomEntities('node', $bundle, $count);
  }

  /**
   * Select a random real node.
   *
   * @param string $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\node\Entity\Node|null
   *   Node entity object or NULL if no entities were found.
   */
  public static function randomRealNode($bundle = NULL) {
    $entities = static::randomRealEntities('node', $bundle, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select random nodes.
   *
   * @param string $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   * @param bool|int $count
   *   Optional count of Nodes. If FALSE, 5 Nodes will be returned.
   *
   * @return \Drupal\node\Entity\Node[]
   *   Array of node entities.
   */
  public static function randomRealNodes($bundle = NULL, $count = 5) {
    return static::randomRealEntities('node', $bundle, $count);
  }

  /**
   * Select a static node.
   *
   * @param string $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\node\Entity\Node|null
   *   The node object or NULL if no entities were found.
   */
  public static function staticNode($bundle = NULL) {
    $entities = static::staticEntities('node', $bundle, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select a static node.
   *
   * @param string $bundle
   *   The type of the node to return. If not provided - random type will be
   *   returned.
   * @param null|int $count
   *   Number of nodes to return. If none provided - all nodes will be returned.
   *
   * @return \Drupal\node\Entity\Node[]
   *   Array of node objects.
   */
  public static function staticNodes($bundle = NULL, $count = NULL) {
    return static::staticEntities('node', $bundle, $count);
  }

  /**
   * Select a random term.
   *
   * @param string $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   *
   * @return \Drupal\taxonomy\Entity\Term|null
   *   Term entity object or NULL if no entities were found.
   */
  public static function randomTerm($vid = NULL) {
    $entities = static::randomEntities('taxonomy_term', $vid, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select random terms.
   *
   * @param string $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   * @param bool|int $count
   *   Optional count of Terms. If FALSE, 20 Terms will be returned.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of term entities.
   */
  public static function randomTerms($vid = NULL, $count = 5) {
    return static::randomEntities('taxonomy_term', $vid, $count);
  }

  /**
   * Select a random real term.
   *
   * @param string $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   *
   * @return \Drupal\taxonomy\Entity\Term|null
   *   Term entity object or NULL if no entities were found.
   */
  public static function randomRealTerm($vid = NULL) {
    $entities = static::randomRealEntities('taxonomy_term', $vid, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select random terms.
   *
   * @param string $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   * @param bool|int $count
   *   Optional count of Terms. If FALSE, 5 Terms will be returned.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of term entities.
   */
  public static function randomRealTerms($vid = NULL, $count = 5) {
    return static::randomRealEntities('taxonomy_term', $vid, $count);
  }

  /**
   * Select a static term.
   *
   * @param string $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   *
   * @return \Drupal\taxonomy\Entity\Term|null
   *   The term object or NULL if no entities were found.
   */
  public static function staticTerm($vid = NULL) {
    $entities = static::staticEntities('taxonomy_term', $vid, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select a random real term.
   *
   * @param string $vid
   *   The vocabulary of the term to return. If not provided - term from random
   *   vocabulary will be returned.
   * @param null|int $count
   *   Number of terms to return. If none provided - all terms will be returned.
   *
   * @return \Drupal\taxonomy\Entity\Term[]
   *   Array of term objects.
   */
  public static function staticTerms($vid = NULL, $count = NULL) {
    return static::staticEntities('taxonomy_term', $vid, $count);
  }

  /**
   * Select a random media.
   *
   * @param string $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\media\Entity\Media|null
   *   Media entity object or NULL if no entities were found.
   */
  public static function randomMediaItem($bundle = NULL) {
    $entities = static::randomEntities('media', $bundle, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select random medias.
   *
   * @param string $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   * @param bool|int $count
   *   Optional count of Medias. If FALSE, 20 Medias will be returned.
   *
   * @return \Drupal\media\Entity\Media[]
   *   Array of media entities.
   */
  public static function randomMediaItems($bundle = NULL, $count = 5) {
    return static::randomEntities('media', $bundle, $count);
  }

  /**
   * Select a random real media.
   *
   * @param string $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\media\Entity\Media|null
   *   Media entity object or NULL if no entities were found.
   */
  public static function randomRealMediaItem($bundle = NULL) {
    $entities = static::randomRealEntities('media', $bundle, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select random medias.
   *
   * @param string $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   * @param bool|int $count
   *   Optional count of Medias. If FALSE, 5 Medias will be returned.
   *
   * @return \Drupal\media\Entity\Media[]
   *   Array of media entities.
   */
  public static function randomRealMediaItems($bundle = NULL, $count = 5) {
    return static::randomRealEntities('media', $bundle, $count);
  }

  /**
   * Select a static media.
   *
   * @param string $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   *
   * @return \Drupal\media\Entity\Media|null
   *   The media object or NULL if no entities were found.
   */
  public static function staticMediaItem($bundle = NULL) {
    $entities = static::staticEntities('media', $bundle, 1);

    return count($entities) > 0 ? reset($entities) : NULL;
  }

  /**
   * Select a static media.
   *
   * @param string $bundle
   *   The type of the media to return. If not provided - random type will be
   *   returned.
   * @param null|int $count
   *   Number of medias to return. If none provided - all medias will be
   *   returned.
   *
   * @return \Drupal\media\Entity\Media[]
   *   Array of media objects.
   */
  public static function staticMediaItems($bundle = NULL, $count = NULL) {
    return static::staticEntities('media', $bundle, $count);
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

}
