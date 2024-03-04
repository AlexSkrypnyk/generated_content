<?php

/**
 * @file
 * Create generated Article nodes.
 */

declare(strict_types=1);

use Drupal\Core\Link;
use Drupal\generated_content_example2\GeneratedContentExample2Helper;
use Drupal\node\Entity\Node;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example2_generated_content_create_node_article_weight(): int {
  return 36;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example2_generated_content_create_node_article_tracking(): bool {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 *
 * @phpstan-ignore-next-line
 */
function generated_content_example2_generated_content_create_node_article(): array {
  $total_nodes_count = 10;

  /** @var \Drupal\generated_content_example2\GeneratedContentExample2Helper $helper */
  $helper = GeneratedContentExample2Helper::getInstance();

  $nodes = [];

  for ($i = 0; $i < $total_nodes_count; $i++) {
    $variation = $helper::variationRandomValue([
      'status' => NULL,
      'content' => NULL,
      'tags' => [1, 3],
    ]);
    $variation_info = $helper::variationFormatInfo($variation);

    $node = Node::create([
      'type' => 'article',
      'title' => sprintf('Generated article %s', $variation_info),
      'status' => $variation['status'],
    ]);

    if ($variation['content']) {
      // @phpstan-ignore-next-line
      $node->body = [
        'value' => $helper::randomRichText(),
        'format' => 'full_html',
      ];
    }

    if ($variation['tags']) {
      // @phpstan-ignore-next-line
      $node->field_tags = $helper::randomTags($variation['tags']);
    }

    // @phpstan-ignore-next-line
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
