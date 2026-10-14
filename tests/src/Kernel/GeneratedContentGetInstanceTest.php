<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Group;
use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Helpers\GeneratedContentHelper;

/**
 * Tests getInstance() returns correct subclass instances.
 *
 * @group generated_content
 */
#[Group('generated_content')]
#[RunTestsInSeparateProcesses]
class GeneratedContentGetInstanceTest extends GeneratedContentKernelTestBase {

  /**
   * Test that Helper::getInstance() returns instance of the called class.
   */
  public function testHelperGetInstanceReturnsSubclass(): void {
    $parent = GeneratedContentHelper::getInstance();
    $this->assertInstanceOf(GeneratedContentHelper::class, $parent);

    $child = GeneratedContentHelperTestSubclass::getInstance();
    $this->assertInstanceOf(GeneratedContentHelperTestSubclass::class, $child);
    $this->assertNotSame($parent, $child);

    // Calling parent again should still return parent instance.
    $parent2 = GeneratedContentHelper::getInstance();
    $this->assertSame($parent, $parent2);
    $this->assertNotInstanceOf(GeneratedContentHelperTestSubclass::class, $parent2);
  }

  /**
   * Test that Repository::getInstance() returns instance of the called class.
   */
  public function testRepositoryGetInstanceReturnsSubclass(): void {
    $parent = GeneratedContentRepository::getInstance();
    $this->assertInstanceOf(GeneratedContentRepository::class, $parent);

    $child = GeneratedContentRepositoryTestSubclass::getInstance();
    $this->assertInstanceOf(GeneratedContentRepositoryTestSubclass::class, $child);
  }

}

/**
 * Test subclass of GeneratedContentHelper.
 */
class GeneratedContentHelperTestSubclass extends GeneratedContentHelper {

  /**
   * Custom method to verify the subclass is used.
   */
  public static function customMethod(): string {
    return 'subclass';
  }

}

/**
 * Test subclass of GeneratedContentRepository.
 */
class GeneratedContentRepositoryTestSubclass extends GeneratedContentRepository {

  /**
   * Custom method to verify the subclass is used.
   */
  public function customMethod(): string {
    return 'subclass';
  }

}
