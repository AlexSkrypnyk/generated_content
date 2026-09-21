<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\generated_content\GeneratedContentLogger;
use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that module services build against a decorated entity type manager.
 *
 * Contrib modules replace 'entity_type.manager' with a class that implements
 * EntityTypeManagerInterface without extending EntityTypeManager. A service
 * whose constructor names the class instead of the interface cannot be built
 * on such a site.
 *
 * @group generated_content
 */
#[Group('generated_content')]
class GeneratedContentServiceDecorationTest extends GeneratedContentKernelTestBase {

  /**
   * Test that a module service builds with a decorated entity type manager.
   *
   * @param string $service_id
   *   The service to build.
   * @param string $expected_class
   *   The class the service is expected to resolve to.
   *
   * @dataProvider dataProviderModuleServices
   */
  #[DataProvider('dataProviderModuleServices')]
  public function testServiceBuildsWithDecoratedEntityTypeManager(string $service_id, string $expected_class): void {
    $this->decorateEntityTypeManager();

    // Discard the instance built during module install so that the container
    // resolves the service again against the decorated manager.
    $this->container->set($service_id, NULL);

    $this->assertInstanceOf($expected_class, $this->container->get($service_id));
  }

  /**
   * Data provider for testServiceBuildsWithDecoratedEntityTypeManager().
   *
   * @return array<string, array{string, class-string}>
   *   Service id and expected class, keyed by service id.
   */
  public static function dataProviderModuleServices(): array {
    return [
      'generated_content.asset_generator' => ['generated_content.asset_generator', GeneratedContentAssetGenerator::class],
      'generated_content.logger' => ['generated_content.logger', GeneratedContentLogger::class],
      'plugin.manager.generated_content' => ['plugin.manager.generated_content', GeneratedContentPluginManager::class],
    ];
  }

  /**
   * Test that the repository builds with a decorated entity type manager.
   */
  public function testRepositoryBuildsWithDecoratedEntityTypeManager(): void {
    $this->decorateEntityTypeManager();

    $this->assertInstanceOf(GeneratedContentRepository::class, GeneratedContentRepository::getInstance()->reset());
  }

  /**
   * Replace 'entity_type.manager' with an interface-only implementation.
   *
   * The double delegates every call to the real manager, so only the type of
   * the injected object differs from an undecorated site.
   */
  protected function decorateEntityTypeManager(): void {
    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $inner */
    $inner = $this->container->get('entity_type.manager');

    $decorated = $this->createMock(EntityTypeManagerInterface::class);
    foreach (get_class_methods(EntityTypeManagerInterface::class) as $method) {
      $decorated->method($method)->willReturnCallback(static fn(mixed ...$arguments): mixed => $inner->{$method}(...$arguments));
    }

    $this->assertNotInstanceOf(EntityTypeManager::class, $decorated);

    $this->container->set('entity_type.manager', $decorated);
  }

}
