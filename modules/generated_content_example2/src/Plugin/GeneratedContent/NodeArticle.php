<?php

declare(strict_types=1);

namespace Drupal\generated_content_example2\Plugin\GeneratedContent;

use Drupal\Core\Link;
use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;
use Drupal\node\Entity\Node;

/**
 * Generates article node entities.
 */
#[GeneratedContent(id: 'example2_node_article', entity_type: 'node', bundle: 'article', weight: 36, helper: \Drupal\generated_content_example2\GeneratedContentExample2Helper::class)]
class NodeArticle extends GeneratedContentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function generate(): array {
    $total_nodes_count = 10;

    $nodes = [];

    for ($i = 0; $i < $total_nodes_count; $i++) {
      $variation = $this->helper::variationRandomValue([
        'status' => NULL,
        'content' => NULL,
        'tags' => [1, 3],
      ]);
      $variation_info = $this->helper::variationFormatInfo($variation);

      $node = Node::create([
        'type' => 'article',
        'title' => sprintf('Generated article %s', $variation_info),
        'status' => $variation['status'],
      ]);

      if ($variation['content']) {
        // @phpstan-ignore-next-line
        $node->body = [
          'value' => $this->helper::randomRichText(),
          'format' => 'full_html',
        ];
      }

      if ($variation['tags']) {
        // @phpstan-ignore-next-line
        $node->field_tags = $this->helper::randomTags($variation['tags']);
      }

      // @phpstan-ignore-next-line
      $node->revision_log = $variation_info;

      $node->save();

      $this->helper::log(
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

}
