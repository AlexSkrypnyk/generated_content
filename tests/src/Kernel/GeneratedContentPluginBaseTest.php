<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;
use Drupal\generated_content_example2\GeneratedContentExample2Helper;

/**
 * Tests GeneratedContentPluginBase via the example1 and example2 plugins.
 *
 * The base class methods (getEntityType, getBundle, getWeight,
 * getTracking, resolveHelper) are exercised through real plugin
 * instances obtained from the plugin manager. example1 plugins do
 * not declare a `helper:` attribute - they should resolve the base
 * helper; example2 plugins declare a custom helper class.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase
 */
class GeneratedContentPluginBaseTest extends GeneratedContentKernelTestBase {

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
    'generated_content_example2',
  ];

  /**
   * Tests the default-helper branch of resolveHelper().
   */
  public function testResolveHelperDefault(): void {
    $plugin = $this->container->get('plugin.manager.generated_content')
      ->createInstance('example1_taxonomy_term_tags');

    $helper = self::callProtectedMethod($plugin, 'resolveHelper');

    $this->assertInstanceOf(GeneratedContentHelper::class, $helper);
    $this->assertNotInstanceOf(GeneratedContentExample2Helper::class, $helper);
  }

  /**
   * Tests the custom-helper branch of resolveHelper().
   */
  public function testResolveHelperCustom(): void {
    $plugin = $this->container->get('plugin.manager.generated_content')
      ->createInstance('example2_node_page');

    $helper = self::callProtectedMethod($plugin, 'resolveHelper');

    $this->assertInstanceOf(GeneratedContentExample2Helper::class, $helper);
  }

  /**
   * Tests getWeight() with default and explicit values.
   */
  public function testGetWeight(): void {
    // example1 user plugin declares weight: -100.
    $with_explicit = $this->container->get('plugin.manager.generated_content')
      ->createInstance('example1_user_user');
    $this->assertInstanceOf(GeneratedContentPluginBase::class, $with_explicit);
    $this->assertSame(-100, $with_explicit->getWeight());

    // example2 node page plugin declares weight: 35.
    $other_weight = $this->container->get('plugin.manager.generated_content')
      ->createInstance('example2_node_page');
    $this->assertInstanceOf(GeneratedContentPluginBase::class, $other_weight);
    $this->assertSame(35, $other_weight->getWeight());
  }

  /**
   * Tests getTracking() default (TRUE) and explicit (FALSE) values.
   */
  public function testGetTracking(): void {
    // example1 user plugin declares tracking: FALSE.
    $no_tracking = $this->container->get('plugin.manager.generated_content')
      ->createInstance('example1_user_user');
    $this->assertInstanceOf(GeneratedContentPluginBase::class, $no_tracking);
    $this->assertFalse($no_tracking->getTracking());

    // example1 tags plugin does not declare tracking - defaults to TRUE.
    $defaults_to_true = $this->container->get('plugin.manager.generated_content')
      ->createInstance('example1_taxonomy_term_tags');
    $this->assertInstanceOf(GeneratedContentPluginBase::class, $defaults_to_true);
    $this->assertTrue($defaults_to_true->getTracking());
  }

  /**
   * Tests getEntityType() / getBundle() expose plugin definition values.
   */
  public function testGetEntityTypeAndBundle(): void {
    $plugin = $this->container->get('plugin.manager.generated_content')
      ->createInstance('example2_node_article');

    $this->assertInstanceOf(GeneratedContentPluginBase::class, $plugin);
    $this->assertSame('node', $plugin->getEntityType());
    $this->assertSame('article', $plugin->getBundle());
  }

}
