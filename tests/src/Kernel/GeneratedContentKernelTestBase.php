<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestMockTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Class GeneratedContentKernelTestBase.
 *
 * Base class for all kernel test cases.
 */
abstract class GeneratedContentKernelTestBase extends KernelTestBase {

  use GeneratedContentTestMockTrait;
  use UserCreationTrait;

  /**
   * Random node type.
   *
   * @var string
   */
  protected $nodeType;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installSchema('generated_content', ['generated_content']);

    $this->nodeType = $this->randomMachineName();
    $node_type = NodeType::create([
      'type' => $this->nodeType,
      'name' => $this->randomString(),
    ]);
    $node_type->save();
  }

  /**
   * Prepare nodes to be used in tests.
   */
  protected function prepareNodes($count) {
    $nodes = [];
    for ($i = 0; $i < $count; $i++) {
      $node = $this->container->get('entity_type.manager')->getStorage('node')->create([
        'type' => $this->nodeType,
        'title' => 'Node ' . $i,
      ]);
      $node->save();
      $nodes[$node->id()] = $node;
    }

    return $nodes;
  }

  /**
   * Prepare users to be used in tests.
   */
  protected function prepareUsers($count) {
    $users = [];

    for ($i = 0; $i < $count; $i++) {
      \Drupal::currentUser()->setAccount($this->createUser(['access content']));

      $user = $this->container->get('entity_type.manager')->getStorage('user')->create([
        'name' => $this->randomString(),
      ]);
      $user->save();
      $users[$user->id()] = $user;
    }

    return $users;
  }

  /**
   * Recursively replace entities with IDs.
   *
   * Useful to speed-up tests to avoid full object comparison.
   */
  protected function replaceEntitiesWithIds($entities) {
    foreach ($entities as $k => $entity) {
      if (is_object($entity)) {
        try {
          $entities[$k] = $entity->id();
        }
        catch (\Exception $e) {
          // Leave unchanged.
        }
      }
      elseif (is_array($entity)) {
        $entities[$k] = $this->replaceEntitiesWithIds($entity);
      }
    }

    return $entities;
  }

}
