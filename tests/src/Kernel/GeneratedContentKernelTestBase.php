<?php

declare(strict_types = 1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\Core\Entity\EntityInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestMockTrait;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestNodeTrait;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestUserTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Class GeneratedContentKernelTestBase.
 *
 * Base class for all kernel test cases.
 */
abstract class GeneratedContentKernelTestBase extends KernelTestBase {

  use UserCreationTrait;
  use GeneratedContentTestMockTrait;
  use GeneratedContentTestNodeTrait;
  use GeneratedContentTestUserTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'file',
    'generated_content',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->userSetUp();
    $this->nodeSetUp();

    $this->installSchema('generated_content', ['generated_content']);
  }

  /**
   * Recursively replace entities with IDs.
   *
   * Useful to speed-up tests to avoid full object comparison.
   *
   * @param array<int|string, \Drupal\Core\Entity\EntityInterface[]>|\Drupal\Core\Entity\EntityInterface[] $entities
   *   Entities.
   *
   * @return array<mixed>
   *   Entity ids.
   */
  protected function replaceEntitiesWithIds(array $entities): array {
    $entities_replaced = [];
    foreach ($entities as $k => $entity) {
      if ($entity instanceof EntityInterface) {
        try {
          if ($entity->id()) {
            $entities_replaced[$k] = $entity->id();
          }
        }
        catch (\Exception $e) {
          // Leave unchanged.
        }
      }
      elseif (is_array($entity)) {
        $entities_replaced[$k] = $this->replaceEntitiesWithIds($entity);
      }
    }

    return $entities_replaced;
  }

}
