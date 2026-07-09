<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use PHPUnit\Framework\Attributes\Group;
use Drupal\generated_content\GeneratedContentBatch;

/**
 * Tests the GeneratedContentBatch static batch helpers.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\GeneratedContentBatch
 */
#[Group('generated_content')]
class GeneratedContentBatchTest extends GeneratedContentKernelTestBase {

  /**
   * Tests that set('create') queues only createSingle operations.
   */
  public function testSetCreate(): void {
    $batch = $this->captureBatchAfter('create');

    $this->assertCount(2, $batch['operations']);
    foreach ($batch['operations'] as $op) {
      $this->assertSame(GeneratedContentBatch::class . '::createSingle', $op[0]);
    }
  }

  /**
   * Tests that set('remove') queues only removeSingle operations.
   */
  public function testSetRemove(): void {
    $batch = $this->captureBatchAfter('remove');

    $this->assertCount(2, $batch['operations']);
    foreach ($batch['operations'] as $op) {
      $this->assertSame(GeneratedContentBatch::class . '::removeSingle', $op[0]);
    }
  }

  /**
   * Tests that set('regenerate') queues remove followed by create operations.
   */
  public function testSetRegenerate(): void {
    $batch = $this->captureBatchAfter('regenerate');

    // 2 info items each get a remove and a create.
    $this->assertCount(4, $batch['operations']);

    // First half are removes, second half are creates.
    $this->assertSame(GeneratedContentBatch::class . '::removeSingle', $batch['operations'][0][0]);
    $this->assertSame(GeneratedContentBatch::class . '::removeSingle', $batch['operations'][1][0]);
    $this->assertSame(GeneratedContentBatch::class . '::createSingle', $batch['operations'][2][0]);
    $this->assertSame(GeneratedContentBatch::class . '::createSingle', $batch['operations'][3][0]);
  }

  /**
   * Tests createSingle() initialises sandbox/results when missing.
   *
   * Repository::createSingle() requires a real plugin and will throw
   * for our placeholder plugin id - the throw lands after the init
   * block so the assertion below pins the init contract only. Full
   * increment behaviour is tested via removeSingle(), which has no
   * plugin dependency.
   */
  public function testCreateSingleInitialisesContext(): void {
    $info = ['entity_type' => 'node', 'bundle' => 'page', '#plugin_id' => 'missing_plugin', '#tracking' => TRUE];
    $context = [];

    try {
      GeneratedContentBatch::createSingle($info, 4, $context);
    }
    catch (\Throwable) {
      // Repository will throw because the plugin ID is missing - the
      // pre-throw context setup is what we care about.
    }

    $this->assertSame(0, $context['sandbox']['count']);
    $this->assertSame(0, $context['results']['count']);
  }

  /**
   * Tests removeSingle() advances the sandbox count and writes the message.
   *
   * Repository::removeSingle() does not require a plugin - it issues
   * an internal database delete against the tracking table - so this
   * test exercises the full counter-and-message path.
   */
  public function testRemoveSingleAdvancesContext(): void {
    $info = ['entity_type' => 'node', 'bundle' => 'page'];
    $context = [
      'sandbox' => ['count' => 1],
      'results' => ['count' => 1],
    ];

    GeneratedContentBatch::removeSingle($info, 2, $context);

    $this->assertSame(2, $context['sandbox']['count']);
    $this->assertSame(2, $context['results']['count']);
    $this->assertEquals(1, $context['finished']);
    $this->assertStringContainsString('Deleting', (string) $context['message']);
    $this->assertStringContainsString('node', (string) $context['message']);
    $this->assertStringContainsString('page', (string) $context['message']);
  }

  /**
   * Tests removeSingle() initialises sandbox/results and increments.
   */
  public function testRemoveSingleInitialisesContext(): void {
    $info = ['entity_type' => 'node', 'bundle' => 'page'];
    $context = [];

    GeneratedContentBatch::removeSingle($info, 1, $context);

    // Initialised at 0 then incremented by 1.
    $this->assertSame(1, $context['sandbox']['count']);
    $this->assertSame(1, $context['results']['count']);
    $this->assertEquals(1, $context['finished']);
  }

  /**
   * Tests finished(TRUE, ...) clears caches and posts a success message.
   */
  public function testFinishedSuccess(): void {
    $cache = $this->container->get('cache.data');
    $cache->set('generated_content_test_seed', 'seeded');
    $this->assertNotFalse($cache->get('generated_content_test_seed'));

    /** @var \Drupal\Core\Messenger\MessengerInterface $messenger */
    $messenger = $this->container->get('messenger');
    $messenger->deleteAll();

    GeneratedContentBatch::finished(TRUE, ['count' => 5], []);

    $this->assertFalse($cache->get('generated_content_test_seed'));
    $messages = array_map(strval(...), $messenger->messagesByType('status'));
    $this->assertNotEmpty($messages);
    $combined = implode("\n", $messages);
    $this->assertStringContainsString('5', $combined);
  }

  /**
   * Tests finished(FALSE, ...) posts the error message and leaves caches alone.
   */
  public function testFinishedFailure(): void {
    $cache = $this->container->get('cache.data');
    $cache->set('generated_content_test_seed', 'seeded');

    /** @var \Drupal\Core\Messenger\MessengerInterface $messenger */
    $messenger = $this->container->get('messenger');
    $messenger->deleteAll();

    GeneratedContentBatch::finished(FALSE, [], []);

    $hit = $cache->get('generated_content_test_seed');
    $this->assertNotFalse($hit);
    $this->assertSame('seeded', $hit->data);

    $messages = array_map(strval(...), $messenger->messagesByType('status'));
    $combined = implode("\n", $messages);
    $this->assertStringContainsString('error', strtolower($combined));
  }

  /**
   * Call GeneratedContentBatch::set() and return the queued batch array.
   */
  protected function captureBatchAfter(string $op): array {
    $info_items = [
      ['entity_type' => 'node', 'bundle' => 'page'],
      ['entity_type' => 'user', 'bundle' => 'user'],
    ];

    $batch =& batch_get();
    $batch = NULL;

    GeneratedContentBatch::set($op, $info_items, 1);

    $queued =& batch_get();
    $this->assertIsArray($queued);
    $this->assertArrayHasKey('sets', $queued);
    $this->assertNotEmpty($queued['sets']);

    return end($queued['sets']);
  }

}
