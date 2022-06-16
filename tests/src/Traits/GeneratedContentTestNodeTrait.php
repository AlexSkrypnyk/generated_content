<?php

namespace Drupal\Tests\generated_content\Traits;

use Drupal\node\Entity\NodeType;

/**
 * Trait GeneratedContentTestNodeTrait.
 *
 * Trait with node-related helpers.
 *
 * @package Drupal\generated_content\Tests
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
   */
  public function nodeSetUp() {
    $this->installEntitySchema('node');

    for ($i = 0; $i < 3; $i++) {
      $node_type = NodeType::create([
        'type' => $this->randomMachineName(),
        'name' => $this->randomString(),
      ]);
      $node_type->save();
      $this->nodeTypes[] = $node_type->id();
    }
  }

  /**
   * Prepare nodes to be used in tests.
   */
  protected function prepareNodes($count, $bundles = NULL, $single_bundle = FALSE) {
    $bundles = $bundles ?? $this->nodeTypes;
    $nodes = [];
    foreach ($bundles as $bundle) {
      for ($i = 0; $i < $count; $i++) {
        $node = $this->container->get('entity_type.manager')->getStorage('node')->create([
          'type' => $bundle,
          'title' => sprintf('Node %s of bundle %s.', $i + 1, $bundle),
        ]);
        $node->save();
        $nodes[$bundle][$node->id()] = $node;
      }
    }

    if ($single_bundle) {
      $nodes = reset($nodes);
    }

    return $nodes;
  }

}
