<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Functional;

use Drush\TestTraits\DrushTestTrait;

/**
 * Test Drush command.
 *
 * @group generated_content
 */
class GeneratedContentDrushCommandTest extends GeneratedContentFunctionalTestBase {

  use DrushTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'generated_content_example2',
    'generated_content',
  ];

  /**
   * Test generated content drush command.
   */
  public function testGeneratedContentDrushCommand(): void {
    $this->drush('generated-content:create-content', ['node', 'page', '3']);
    $output = $this->getSimplifiedErrorOutput();
    $this->assertStringContainsString('Created generated content entities "node" with bundle "page"', $output);
  }

}
