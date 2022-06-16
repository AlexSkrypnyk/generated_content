<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestMockTrait;

/**
 * Class GeneratedContentKernelTestBase.
 *
 * Base class for all kernel test cases.
 */
abstract class GeneratedContentKernelTestBase extends KernelTestBase {

  use GeneratedContentTestMockTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
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
