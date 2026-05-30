<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\node\NodeInterface;

/**
 * Tests the GeneratedContentVariationTrait via GeneratedContentHelper.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\Helpers\GeneratedContentVariationTrait
 */
class GeneratedContentHelperVariationTest extends GeneratedContentKernelTestBase {

  /**
   * Tests variationRandomValue() expansion of NULL/array/scalar inputs.
   */
  public function testVariationRandomValueShapes(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $input = [
      'null_field' => NULL,
      'array_field' => ['x', 'y', 'z'],
      'string_field' => 'keep me',
      'int_field' => 42,
      'bool_field' => TRUE,
    ];

    $result = $helper::variationRandomValue($input);

    $this->assertIsBool($result['null_field']);
    $this->assertContains($result['array_field'], ['x', 'y', 'z']);
    $this->assertSame('keep me', $result['string_field']);
    $this->assertSame(42, $result['int_field']);
    $this->assertTrue($result['bool_field']);
  }

  /**
   * Tests variationRandomValue() with an empty variation.
   */
  public function testVariationRandomValueEmpty(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->assertSame([], $helper::variationRandomValue([]));
  }

  /**
   * Tests variationFormatInfo() output for each value type.
   *
   * @param array<mixed> $variation
   *   Variation input.
   * @param int $name_length
   *   Name length argument.
   * @param string $expected
   *   Expected formatted string.
   *
   * @dataProvider dataProviderVariationFormatInfo
   */
  public function testVariationFormatInfo(array $variation, int $name_length, string $expected): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->assertSame($expected, $helper::variationFormatInfo($variation, $name_length));
  }

  /**
   * Data provider for testVariationFormatInfo().
   *
   * @return array<string, array<mixed>>
   *   Provider data.
   */
  public static function dataProviderVariationFormatInfo(): array {
    return [
      'truthy bool renders as Y' => [['enabled' => TRUE], 3, 'Ena: Y'],
      'falsy null renders as N' => [['enabled' => NULL], 3, 'Ena: N'],
      'int zero renders as N' => [['count' => 0], 3, 'Cou: N'],
      'int non-zero renders as value' => [['count' => 7], 3, 'Cou: 7'],
      'array renders as length' => [['tags' => ['a', 'b', 'c']], 3, 'Tag: 3'],
      'string is trimmed to 3 chars' => [['label' => 'hello world'], 3, 'Lab: hel'],
      'string with HTML is stripped' => [['label' => '<em>hi</em>'], 3, 'Lab: hi'],
      'snake-cased name is collapsed' => [['my_field_name' => 'abc'], 4, 'Myfi: abc'],
      'multiple fields are comma-joined' => [
        ['status' => TRUE, 'count' => 2],
        3,
        'Sta: Y, Cou: 2',
      ],
    ];
  }

  /**
   * Tests variationFetchAll() with a fixture directory of .inc files.
   */
  public function testVariationFetchAllWithPath(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $path = $this->fixturesPath();

    $variations = $helper::variationFetchAll('gc_test_var_', $path);

    // Three variations from gc_test_var_one + gc_test_var_two; the
    // gc_test_var_empty contributor returns nothing and is skipped.
    $this->assertCount(3, $variations);

    // Post-processor tags every variation.
    foreach ($variations as $variation) {
      $this->assertSame('yes', $variation['post_processed']);
    }
  }

  /**
   * Tests variationFetchAll() against an unreadable directory.
   */
  public function testVariationFetchAllInvalidPath(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('does not exist');
    $helper::variationFetchAll('gc_test_var_', '/path/that/does/not/exist/anywhere');
  }

  /**
   * Tests variationFetchAll() without a path - just uses defined functions.
   *
   * This depends on the fixture file having been included by an earlier
   * test in this run. PHPUnit runs methods in declaration order by
   * default and require_once side effects persist across methods, so
   * this test calls variationFetchAll() with a NULL path after the
   * fixture has already been loaded.
   *
   * @depends testVariationFetchAllWithPath
   */
  public function testVariationFetchAllNoPath(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Ensure the fixture is loaded - the @depends already guarantees
    // testVariationFetchAllWithPath ran first, but Kernel tests reset
    // the container per method, so re-include the file defensively.
    require_once $this->fixturesPath() . '/01.gc_test_helper.inc';

    $variations = $helper::variationFetchAll('gc_test_var_');

    $this->assertCount(3, $variations);
  }

  /**
   * Tests variationCreateNode() without a post-process callback.
   *
   * The test node bundles do not declare a moderation_state field so
   * Drupal silently drops the moderation hint - the published status
   * is the observable side-effect.
   */
  public function testVariationCreateNodePublished(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $node = $helper::variationCreateNode($this->nodeTypes[0], ['status' => TRUE], 0);

    $this->assertInstanceOf(NodeInterface::class, $node);
    $this->assertSame($this->nodeTypes[0], $node->bundle());
    $this->assertSame('Node title from variation', $node->getTitle());
    $this->assertTrue($node->isPublished());
  }

  /**
   * Tests variationCreateNode() produces an unpublished node for status FALSE.
   */
  public function testVariationCreateNodeUnpublished(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $node = $helper::variationCreateNode($this->nodeTypes[0], ['status' => FALSE], 0);

    $this->assertInstanceOf(NodeInterface::class, $node);
    $this->assertFalse($node->isPublished());
  }

  /**
   * Tests variationCreateNode() invokes the post-process callback.
   */
  public function testVariationCreateNodePostprocess(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $captured = [];
    $callback = function ($node, $variation, $variation_idx) use (&$captured): void {
      $captured = [
        'bundle' => $node->bundle(),
        'variation' => $variation,
        'index' => $variation_idx,
      ];
      $node->setTitle('post-processed');
    };

    $variation = ['status' => TRUE, 'foo' => 'bar'];
    $node = $helper::variationCreateNode($this->nodeTypes[0], $variation, 7, $callback);

    $this->assertInstanceOf(NodeInterface::class, $node);
    $this->assertSame('post-processed', $node->getTitle());
    $this->assertSame($this->nodeTypes[0], $captured['bundle']);
    $this->assertSame($variation, $captured['variation']);
    $this->assertSame(7, $captured['index']);
  }

  /**
   * Absolute path to the variations fixture directory.
   */
  protected function fixturesPath(): string {
    return __DIR__ . '/../../fixtures/variations';
  }

}
