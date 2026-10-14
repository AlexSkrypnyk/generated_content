<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Group;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginInterface;
use Drupal\generated_content_example1\Plugin\GeneratedContent\TaxonomyTermTags;

/**
 * Tests the example1 plugins that are not exercised elsewhere.
 *
 * The example1 TaxonomyTermTags plugin is an intentional empty
 * placeholder - the README notes that example2 takes precedence and
 * actually generates terms. This test pins the placeholder's
 * behaviour so it stays empty.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content_example1\Plugin\GeneratedContent\TaxonomyTermTags
 */
#[Group('generated_content')]
#[RunTestsInSeparateProcesses]
class GeneratedContentExample1PluginsTest extends GeneratedContentKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'file',
    'taxonomy',
    'generated_content',
    'generated_content_example1',
  ];

  /**
   * Tests that example1 TaxonomyTermTags::generate() returns an empty array.
   */
  public function testTaxonomyTermTagsGenerateIsEmpty(): void {
    /** @var \Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginManager $manager */
    $manager = $this->container->get('plugin.manager.generated_content');

    /** @var \Drupal\generated_content_example1\Plugin\GeneratedContent\TaxonomyTermTags $plugin */
    $plugin = $manager->createInstance('example1_taxonomy_term_tags');

    $this->assertInstanceOf(TaxonomyTermTags::class, $plugin);
    $this->assertInstanceOf(GeneratedContentPluginInterface::class, $plugin);
    $this->assertSame([], $plugin->generate());
  }

  /**
   * Tests that the plugin exposes the expected metadata.
   */
  public function testTaxonomyTermTagsMetadata(): void {
    /** @var \Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginManager $manager */
    $manager = $this->container->get('plugin.manager.generated_content');

    /** @var \Drupal\generated_content_example1\Plugin\GeneratedContent\TaxonomyTermTags $plugin */
    $plugin = $manager->createInstance('example1_taxonomy_term_tags');

    $this->assertSame('taxonomy_term', $plugin->getEntityType());
    $this->assertSame('tags', $plugin->getBundle());
    $this->assertSame(11, $plugin->getWeight());
    $this->assertTrue($plugin->getTracking());
  }

}
