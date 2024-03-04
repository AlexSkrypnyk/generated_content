<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Traits;

use Drupal\node\Entity\NodeType;

/**
 * Trait GeneratedContentTestNodeTrait.
 *
 * Trait with node-related helpers.
 *
 * @package Drupal\generated_content\Tests
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
trait GeneratedContentTestNodeTrait {

  /**
   * Node types.
   *
   * @var string[]
   */
  protected $nodeTypes = [];

  /**
   * Test setup for node.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function nodeSetUp(): void {
    $this->installEntitySchema('node');

    for ($i = 0; $i < 3; $i++) {
      $node_type = NodeType::create([
        'type' => $this->randomMachineName(),
        'name' => $this->randomString(),
      ]);
      $node_type->save();
      if ($node_type->save() && $node_type->id()) {
        $this->nodeTypes[] = (string) $node_type->id();
      }
    }
  }

  /**
   * Prepare nodes to be used in tests.
   *
   * @param int $count
   *   Number of nodes.
   * @param string[]|null $bundles
   *   Bundles.
   * @param bool $single_bundle
   *   Is single bundle.
   *
   * @return array<mixed>
   *   Nodes grouped by bundle or single node.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function prepareNodes(int $count, array $bundles = NULL, bool $single_bundle = FALSE): array {
    $bundles = $bundles ?? $this->nodeTypes;
    $nodes = [];
    foreach ($bundles as $bundle) {
      for ($i = 0; $i < $count; $i++) {
        $node = $this->container->get('entity_type.manager')->getStorage('node')->create([
          'type' => $bundle,
          'title' => sprintf('Node %s of bundle %s.', $i + 1, $bundle),
        ]);
        if ($node->save() && $node->id()) {
          $nodes[$bundle][$node->id()] = $node;
        }
      }
    }

    if ($single_bundle) {
      $reset_nodes = reset($nodes);
      if ($reset_nodes) {
        $nodes = $reset_nodes;
      }
    }

    return $nodes;
  }

}
