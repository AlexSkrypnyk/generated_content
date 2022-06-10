<?php

/**
 * @file
 * Create generated page nodes.
 */

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\node\Entity\Node;

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
  $total_nodes_count = 10;

  /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
  $helper = GeneratedContentHelper::getInstance();

  $nodes = [];

  for ($i = 0; $i < $total_nodes_count; $i++) {
    $variation = $helper::variationRandom([
      'status' => NULL,
      'content' => NULL,
    ]);
    $variation_info = $helper::variationFormatInfo($variation);

    $node = Node::create([
      'type' => 'page',
      'title' => sprintf('Generated page %s', $variation_info),
      'status' => $variation['status'],
    ]);

    if ($variation['content']) {
      $node->body = [
        'value' => $helper::randomRichText(),
        'format' => 'full_html',
      ];
    }

    $node->revision_log = $variation_info;

    $node->save();

    $helper::log(
      'Created "%s" node "%s" [ID: %s] %s',
      $node->bundle(),
      $node->toLink()->toString(),
      $node->id(),
      Link::createFromRoute('Edit', 'entity.node.edit_form', ['node' => $node->id()])->toString(),
    );

    $nodes[] = $node;
  }

  return $nodes;
}
