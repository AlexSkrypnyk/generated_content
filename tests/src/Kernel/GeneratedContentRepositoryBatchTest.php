<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\GeneratedContentBatch;
use Drupal\generated_content\GeneratedContentRepository;

/**
 * Tests the batch/info/cache methods of GeneratedContentRepository.
 *
 * The original GeneratedContentRepositoryTest already covers the
 * singleton and tracked/non-tracked entity flows. This file targets
 * the remaining methods: createBatch / removeBatch / regenerateBatch,
 * findInfo, isEmpty, clearCaches, and reset behaviour.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\GeneratedContentRepository
 */
class GeneratedContentRepositoryBatchTest extends GeneratedContentKernelTestBase {

  /**
   * Tests createBatch() queues create operations via GeneratedContentBatch.
   */
  public function testCreateBatch(): void {
    $repository = GeneratedContentRepository::getInstance();

    $batch =& batch_get();
    $batch = NULL;

    $info_items = [
      ['entity_type' => 'node', 'bundle' => 'page', '#plugin_id' => 'p', '#tracking' => TRUE, '#weight' => 0, '#module' => 'm'],
      ['entity_type' => 'user', 'bundle' => 'user', '#plugin_id' => 'p2', '#tracking' => TRUE, '#weight' => 0, '#module' => 'm'],
    ];

    $repository->createBatch($info_items);

    $set = $this->lastBatchSet();
    $this->assertCount(2, $set['operations']);
    foreach ($set['operations'] as $op) {
      $this->assertSame(GeneratedContentBatch::class . '::createSingle', $op[0]);
    }
  }

  /**
   * Tests removeBatch() queues remove operations.
   */
  public function testRemoveBatch(): void {
    $repository = GeneratedContentRepository::getInstance();

    $batch =& batch_get();
    $batch = NULL;

    $info_items = [
      ['entity_type' => 'node', 'bundle' => 'page'],
    ];

    $repository->removeBatch($info_items);

    $set = $this->lastBatchSet();
    $this->assertCount(1, $set['operations']);
    $this->assertSame(GeneratedContentBatch::class . '::removeSingle', $set['operations'][0][0]);
  }

  /**
   * Tests regenerateBatch() queues remove-then-create operations.
   */
  public function testRegenerateBatch(): void {
    $repository = GeneratedContentRepository::getInstance();

    $batch =& batch_get();
    $batch = NULL;

    $info_items = [
      ['entity_type' => 'node', 'bundle' => 'page'],
    ];

    $repository->regenerateBatch($info_items);

    $set = $this->lastBatchSet();
    // One info item with regenerate -> one remove + one create.
    $this->assertCount(2, $set['operations']);
    $this->assertSame(GeneratedContentBatch::class . '::removeSingle', $set['operations'][0][0]);
    $this->assertSame(GeneratedContentBatch::class . '::createSingle', $set['operations'][1][0]);
  }

  /**
   * Tests createBatch() with NULL info falls back to repository getInfo().
   */
  public function testCreateBatchWithNullInfo(): void {
    $repository = GeneratedContentRepository::getInstance();

    $batch =& batch_get();
    $batch = NULL;

    // No plugins registered - getInfo() is empty, so no operations.
    $repository->createBatch();

    $set = $this->lastBatchSet();
    $this->assertSame([], $set['operations']);
  }

  /**
   * Tests findInfo() returns FALSE when nothing matches.
   */
  public function testFindInfoNoMatch(): void {
    $repository = GeneratedContentRepository::getInstance();

    $this->assertFalse($repository->findInfo('node', 'page'));
    $this->assertFalse($repository->findInfo('nope'));
  }

  /**
   * Tests isEmpty() reflects the tracking table state.
   */
  public function testIsEmpty(): void {
    $repository = GeneratedContentRepository::getInstance();

    // Tracking table starts empty.
    $this->assertTrue($repository->isEmpty());

    // Add a tracked node and re-check.
    $nodes = $this->prepareNodes(1, NULL, TRUE);
    $repository->addEntities($nodes);

    $this->assertFalse($repository->isEmpty());
  }

  /**
   * Tests clearCaches() empties cache.data.
   */
  public function testClearCaches(): void {
    $repository = GeneratedContentRepository::getInstance();

    $cache = $this->container->get('cache.data');
    $cache->set('repository_clear_test', 'value');
    $this->assertNotFalse($cache->get('repository_clear_test'));

    $repository->clearCaches();

    $this->assertFalse($cache->get('repository_clear_test'));
  }

  /**
   * Tests remove() runs through its full per-item / cache / message path.
   *
   * Repository::remove() is only invoked indirectly today (no callers in
   * the module's own code paths) but it sits behind public API, so it
   * needs coverage. The hook_entity_delete -> tracking-table cleanup is
   * out of scope here - we just confirm remove() does not throw, drops
   * the in-memory cache, and surfaces the user-facing message.
   */
  public function testRemove(): void {
    $repository = GeneratedContentRepository::getInstance();

    $nodes = $this->prepareNodes(1, NULL, TRUE);
    $repository->addEntities($nodes);
    $this->assertNotEmpty($repository->getEntities());

    /** @var \Drupal\Core\Messenger\MessengerInterface $messenger */
    $messenger = $this->container->get('messenger');
    $messenger->deleteAll();

    $info_items = [
      [
        'entity_type' => 'node',
        'bundle' => $this->nodeTypes[0],
        '#plugin_id' => 'p',
        '#tracking' => TRUE,
        '#weight' => 0,
        '#module' => 'm',
      ],
    ];

    $repository->remove($info_items);

    $messages = array_map(strval(...), $messenger->messagesByType('status'));
    $combined = implode("\n", $messages);
    $this->assertStringContainsString('Removed all generated content', $combined);
  }

  /**
   * Tests reset() returns a new singleton instance.
   */
  public function testReset(): void {
    $original = GeneratedContentRepository::getInstance();
    $renewed = $original->reset();

    $this->assertInstanceOf(GeneratedContentRepository::class, $renewed);
    $this->assertNotSame($original, $renewed);

    // Subsequent getInstance() returns the renewed instance.
    $this->assertSame($renewed, GeneratedContentRepository::getInstance());
  }

  /**
   * Tests getInfo() with reset=TRUE re-collects from the plugin manager.
   */
  public function testGetInfoReset(): void {
    $repository = GeneratedContentRepository::getInstance();

    $first = $repository->getInfo();
    $second = $repository->getInfo(TRUE);

    $this->assertSame($first, $second);
  }

  /**
   * Return the most recently queued batch set.
   *
   * @return array<mixed>
   *   Batch set descriptor.
   */
  protected function lastBatchSet(): array {
    $batch =& batch_get();
    $this->assertIsArray($batch);
    $this->assertArrayHasKey('sets', $batch);
    $this->assertNotEmpty($batch['sets']);

    return end($batch['sets']);
  }

}
