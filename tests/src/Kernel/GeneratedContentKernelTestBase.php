<?php

namespace Drupal\Tests\generated_content\Kernel;

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
