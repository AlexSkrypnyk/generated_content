<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Unit;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\generated_content\Commands\GeneratedContentCommands;

/**
 * Tests the GeneratedContentCommands Drush command.
 *
 * The createContent() entry point calls global batch_set() and
 * drush_backend_batch_process(), which can only be exercised in a
 * Functional environment. The internal BatchBuilder construction is
 * the testable surface and is covered here via the protected
 * buildBatch() method (reached through Reflection).
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\Commands\GeneratedContentCommands
 */
class GeneratedContentCommandsTest extends GeneratedContentUnitTestBase {

  /**
   * Tests that the constructor wires the logger factory.
   */
  public function testConstructorWiresLoggerFactory(): void {
    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $commands = new GeneratedContentCommands($logger_factory);

    $this->assertSame(
      $logger_factory,
      $this->getProtectedProperty($commands, 'loggerChannelFactory')
    );
  }

  /**
   * Tests that buildBatch() produces the expected number of operations.
   *
   * @param int $total
   *   Total items requested.
   * @param int $expected_operations
   *   Expected operation count in the BatchBuilder array.
   *
   * @dataProvider dataProviderBuildBatchOperationCount
   */
  public function testBuildBatchOperationCount(int $total, int $expected_operations): void {
    $array = $this->callBuildBatch('node', 'page', $total);

    $this->assertCount($expected_operations, $array['operations']);
  }

  /**
   * Data provider for testBuildBatchOperationCount().
   *
   * Items are chunked into batches of 50. The exit condition is
   * checked at the top of the for-loop, so total=0 produces no
   * operations and total=50 stays at one operation.
   *
   * @return array<string, array<int>>
   *   Provider data.
   */
  public static function dataProviderBuildBatchOperationCount(): array {
    return [
      'zero items queue zero batches' => [0, 0],
      'one item fits in one batch' => [1, 1],
      'forty-nine items fit in one batch' => [49, 1],
      'fifty items fit in one batch' => [50, 1],
      'fifty-one items need two batches' => [51, 2],
      'one hundred items fit in two batches' => [100, 2],
      'two hundred items need four batches' => [200, 4],
    ];
  }

  /**
   * Tests the operation callback and argument shape.
   */
  public function testBuildBatchOperationShape(): void {
    $array = $this->callBuildBatch('user', 'user', 75);

    $this->assertCount(2, $array['operations']);

    foreach ($array['operations'] as $index => $operation) {
      [$callback, $args] = $operation;
      $this->assertSame('\Drupal\generated_content\GeneratedContentBatchService::processItem', $callback);

      [$batch_id, $entity_type, $bundle, $total] = $args;
      $this->assertSame($index + 1, $batch_id);
      $this->assertSame('user', $entity_type);
      $this->assertSame('user', $bundle);
      $this->assertSame(75, $total);
    }

    // Cumulative count is 50 then 100 in the current implementation.
    $this->assertSame(50, $array['operations'][0][1][4]);
    $this->assertSame(100, $array['operations'][1][1][4]);
  }

  /**
   * Tests that buildBatch() wires finish callback and error message.
   */
  public function testBuildBatchCallbacksAndMessages(): void {
    $array = $this->callBuildBatch('node', 'article', 10);

    $this->assertSame('\Drupal\generated_content\GeneratedContentBatchService::processItemFinished', $array['finished']);
    $this->assertNotEmpty((string) $array['error_message']);
    $this->assertNotEmpty((string) $array['title']);
    $this->assertStringContainsString('node', (string) $array['title']);
    $this->assertStringContainsString('article', (string) $array['title']);
    $this->assertStringContainsString('10', (string) $array['title']);
    // Total of 10 chunks into a single 50-item batch.
    $this->assertStringContainsString('1 batches', (string) $array['title']);
  }

  /**
   * Tests that the @batches placeholder matches the actual operation count.
   *
   * @param int $total
   *   Total items.
   * @param int $expected_batches
   *   Expected batch count reported in the title.
   *
   * @dataProvider dataProviderBuildBatchTitleBatchCount
   */
  public function testBuildBatchTitleBatchCount(int $total, int $expected_batches): void {
    $array = $this->callBuildBatch('node', 'page', $total);

    $this->assertCount($expected_batches, $array['operations']);
    $this->assertStringContainsString(sprintf('%d batches', $expected_batches), (string) $array['title']);
  }

  /**
   * Data provider for testBuildBatchTitleBatchCount().
   *
   * @return array<string, array<int>>
   *   Provider data.
   */
  public static function dataProviderBuildBatchTitleBatchCount(): array {
    return self::dataProviderBuildBatchOperationCount();
  }

  /**
   * Build a Commands instance, call buildBatch() via Reflection.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Bundle.
   * @param int $total
   *   Total.
   *
   * @return array<mixed>
   *   The BatchBuilder->toArray() output.
   */
  protected function callBuildBatch(string $entity_type, string $bundle, int $total): array {
    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $commands = new GeneratedContentCommands($logger_factory);

    $translation = $this->createMock(TranslationInterface::class);
    $translation->method('translateString')->willReturnCallback(static function ($translatable) {
      return (string) $translatable->getUntranslatedString();
    });
    $commands->setStringTranslation($translation);

    $reflection = new \ReflectionClass($commands);
    $method = $reflection->getMethod('buildBatch');

    /** @var \Drupal\Core\Batch\BatchBuilder $builder */
    $builder = $method->invoke($commands, $entity_type, $bundle, $total);
    $this->assertInstanceOf(BatchBuilder::class, $builder);

    return $builder->toArray();
  }

  /**
   * Read a protected property via Reflection.
   *
   * @param object $object
   *   Target instance.
   * @param string $property
   *   Property name.
   *
   * @return mixed
   *   The property value.
   */
  protected function getProtectedProperty(object $object, string $property) {
    $reflection = new \ReflectionClass($object);
    $prop = $reflection->getProperty($property);

    return $prop->getValue($object);
  }

}
