<?php

/**
 * @file
 * Create generated Page nodes.
 */

use Drupal\Core\Link;
use Drupal\generated_content_example2\GeneratedContentExample2Helper;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example2_generated_content_create_node_page_weight() {
  return 35;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example2_generated_content_create_node_page_tracking() {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example2_generated_content_create_node_page() {
  $nodes = [];

  /** @var \Drupal\generated_content_example2\GeneratedContentExample2Helper $helper */
  $helper = GeneratedContentExample2Helper::getInstance();

  $variations = $helper::variationFetchAll(
    '_generated_content_example2_create_node_page__variations__',
    __DIR__ . DIRECTORY_SEPARATOR . 'page_variations'
  );

  foreach ($variations as $i => $variation) {
    /** @var \Drupal\node\Entity\Node $node */
    $node = $helper::variationCreateNode('page', $variation, $i, '_generated_content_example2_create_node_page__variation_to_fields');

    $variation_info = $helper::variationFormatInfo($variation);
    $node->revision_log = $variation_info;

    $node->save();

    $helper::log(
      'Created "%s" node "%s" [ID: %s] %s',
      $node->bundle(),
      $node->toLink()->toString(),
      $node->id(),
      Link::createFromRoute('Edit', 'entity.node.edit_form', ['node' => $node->id()])->toString()
    );

    $nodes[] = $node;
  }

  return $nodes;
}

/**
 * Processor to convert variation values to 'page' node fields.
 */
function _generated_content_example2_create_node_page__variation_to_fields($node, $variation) {
  /** @var \Drupal\generated_content_example2\GeneratedContentExample2Helper $helper */
  $helper = GeneratedContentExample2Helper::getInstance();

  if (!empty($variation['title'])) {
    $node->setTitle($variation['title']);
  }

  if (!empty($variation['alias'])) {
    // Replace tokens in an alias, using a callback to "clean" value.
    $variation['alias'] = $helper::replaceTokens($variation['alias'], $variation, function ($value) {
      return is_string($value) ? preg_replace('[^a-zA-Z0-9-]', '-', $value) : $value;
    });

    $node->set('path', [
      'pathauto' => FALSE,
      'alias' => $variation['alias'],
    ]);
  }

  if (!empty($variation['body']) && $node->hasField('body')) {
    $node->body = $variation['body'];
  }
}
