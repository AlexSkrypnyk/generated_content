<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DataProvider;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\generated_content\GeneratedContentBatchService;
use Drupal\generated_content\GeneratedContentRepository;

/**
 * Tests the GeneratedContentBatchService static batch callbacks.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\GeneratedContentBatchService
 */
#[Group('generated_content')]
class GeneratedContentBatchServiceTest extends GeneratedContentKernelTestBase {

  /**
   * Tests that create() returns an instance built from the container.
   */
  public function testCreate(): void {
    $service = GeneratedContentBatchService::create($this->container);

    $this->assertInstanceOf(GeneratedContentBatchService::class, $service);
    $this->assertInstanceOf(ContainerInjectionInterface::class, $service);
  }

  /**
   * Tests that processItem() composes the progress message.
   *
   * @param int $batch_id
   *   Batch id.
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Bundle.
   * @param int $total
   *   Total.
   * @param int $current
   *   Current.
   * @param string $expected
   *   Expected message.
   *
   * @dataProvider dataProviderProcessItemMessage
   */
  #[DataProvider('dataProviderProcessItemMessage')]
  public function testProcessItemMessage(int $batch_id, string $entity_type, string $bundle, int $total, int $current, string $expected): void {
    $context = [];

    GeneratedContentBatchService::processItem($batch_id, $entity_type, $bundle, $total, $current, $context);

    $this->assertSame($expected, $context['message']);
  }

  /**
   * Data provider for testProcessItemMessage().
   *
   * @return array<string, array<mixed>>
   *   Provider data.
   */
  public static function dataProviderProcessItemMessage(): array {
    return [
      'simple progress' => [
        1, 'node', 'page', 5, 1,
        'Running batch "1" for node page (1 of 5).',
      ],
      'final iteration' => [
        7, 'user', 'user', 50, 50,
        'Running batch "7" for user user (50 of 50).',
      ],
      'large totals' => [
        100, 'taxonomy_term', 'tags', 1000, 999,
        'Running batch "100" for taxonomy_term tags (999 of 1000).',
      ],
    ];
  }

  /**
   * Tests that processItem() routes through the repository.
   */
  public function testProcessItemCallsRepository(): void {
    $repository = GeneratedContentRepository::getInstance();
    $messenger = $this->container->get('messenger');
    $messenger->deleteAll();

    $context = [];
    GeneratedContentBatchService::processItem(1, 'node', 'page', 1, 1, $context);

    // No plugins are registered for node/page in this kernel test, so
    // createEntities() iterates an empty info list. The repository still
    // emits its post-create status message - that proves it was invoked.
    $messages = $messenger->messagesByType('status');
    $messages = array_map(strval(...), $messages);
    $this->assertContains('Created all generated content.', $messages);

    // Repository tracks no entities because no plugins ran.
    $this->assertSame([], $repository->getEntities());
  }

  /**
   * Tests processItemFinished() clears caches only on success.
   *
   * @param bool $success
   *   Success flag.
   * @param bool $expect_cleared
   *   Whether the seeded cache entry should be cleared.
   *
   * @dataProvider dataProviderProcessItemFinished
   */
  #[DataProvider('dataProviderProcessItemFinished')]
  public function testProcessItemFinished(bool $success, bool $expect_cleared): void {
    $cache = $this->container->get('cache.data');
    $cache->set('generated_content_test_key', 'seeded');
    $this->assertNotFalse($cache->get('generated_content_test_key'));

    GeneratedContentBatchService::processItemFinished($success, [], []);

    if ($expect_cleared) {
      $this->assertFalse($cache->get('generated_content_test_key'));
    }
    else {
      $hit = $cache->get('generated_content_test_key');
      $this->assertNotFalse($hit);
      $this->assertSame('seeded', $hit->data);
    }
  }

  /**
   * Data provider for testProcessItemFinished().
   *
   * @return array<string, array<mixed>>
   *   Provider data.
   */
  public static function dataProviderProcessItemFinished(): array {
    return [
      'success clears caches' => [TRUE, TRUE],
      'failure leaves caches intact' => [FALSE, FALSE],
    ];
  }

}
