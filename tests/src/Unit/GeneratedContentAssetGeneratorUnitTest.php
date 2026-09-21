<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Unit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileRepositoryInterface;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the GeneratedContentAssetGenerator dependency contract.
 *
 * @group generated_content
 */
#[Group('generated_content')]
class GeneratedContentAssetGeneratorUnitTest extends GeneratedContentUnitTestBase {

  /**
   * Test that the constructor accepts interface-only service implementations.
   *
   * A module that decorates 'entity_type.manager' or 'file.repository'
   * supplies an object that implements the interface without extending the
   * core class, so a constructor that names the class rejects it.
   */
  public function testConstructorAcceptsInterfaceImplementations(): void {
    $module_extension_list = $this->createMock(ModuleExtensionList::class);
    $module_extension_list->method('getPath')->with('generated_content')->willReturn(dirname(__DIR__, 3));

    $generator = new GeneratedContentAssetGenerator(
      $this->createMock(FileSystemInterface::class),
      $this->createMock(EntityTypeManagerInterface::class),
      $this->createMock(FileRepositoryInterface::class),
      $module_extension_list,
    );

    $this->assertInstanceOf(GeneratedContentAssetGenerator::class, $generator);
  }

}
