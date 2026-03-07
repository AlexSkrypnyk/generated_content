<?php

declare(strict_types=1);

namespace Drupal\generated_content_example2\Plugin\GeneratedContent;

use Drupal\generated_content_example2\GeneratedContentExample2Helper;
use Drupal\Core\Link;
use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;
use Drupal\node\NodeInterface;

/**
 * Generates page node entities.
 */
#[GeneratedContent(
  id: 'example2_node_page',
  entity_type: 'node',
  bundle: 'page',
  weight: 35,
  helper: \Drupal\generated_content_example2\GeneratedContentExample2Helper::class,
)]
class NodePage extends GeneratedContentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function generate(): array {
    $nodes = [];

    $variations = static::postProcessVariations(static::variations());

    foreach ($variations as $i => $variation) {
      $node = $this->helper::variationCreateNode('page', $variation, $i, [$this, 'variationToFields']);

      $variation_info = $this->helper::variationFormatInfo($variation);
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

  /**
   * Provide page variations.
   *
   * @return array<mixed>
   *   Variations.
   */
  public static function variations(): array {
    /** @var \Drupal\generated_content_example2\GeneratedContentExample2Helper $helper */
    $helper = GeneratedContentExample2Helper::getInstance();

    return [
      ['title' => 'Demo Page, default values'],
      ['title' => 'Demo Page, Body', 'body' => $helper::staticPlainParagraph()],
      ['title' => 'Demo Page, Body, Unpublished', 'body' => $helper::staticPlainParagraph(), 'status' => FALSE],
    ];
  }

  /**
   * Post-process variations with defaults.
   *
   * @param array<mixed> $variations
   *   Variations.
   *
   * @return array<mixed>
   *   Post-processed variations.
   */
  public static function postProcessVariations(array $variations): array {
    foreach ($variations as &$variation) {
      $variation += [
        'status' => TRUE,
        'alias' => '/generated-content/page/{title}',
      ];
    }

    return $variations;
  }

  /**
   * Convert variation values to node fields.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node.
   * @param array<mixed> $variation
   *   Variation.
   */
  public function variationToFields(NodeInterface $node, array $variation): void {
    if (!empty($variation['title'])) {
      $node->setTitle($variation['title']);
    }

    if (!empty($variation['alias'])) {
      $variation['alias'] = $this->helper::replaceTokens($variation['alias'], $variation, function ($value) {
        return is_string($value) ? preg_replace('[^a-zA-Z0-9-]', '-', $value) : $value;
      });

      $node->set('path', [
        'pathauto' => FALSE,
        'alias' => $variation['alias'],
      ]);
    }

    if (!empty($variation['body']) && $node->hasField('body')) {
      $node->set('body', $variation['body']);
    }
  }

}
