<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Unit;

/**
 * Class GeneratedContentExampleUnitTest.
 *
 * Example test case class.
 *
 * @group generated_content
 */
class GeneratedContentHelperUnitTest extends GeneratedContentUnitTestBase {

  /**
   * Test for GeneratedContentHelper::arraySliceCircular().
   *
   * @param string[] $array
   *   Array input.
   * @param int $count
   *   Count.
   * @param int $idx
   *   Index.
   * @param string[] $expected
   *   Expected array.
   *
   * @dataProvider dataProviderArraySliceCircular
   */
  public function testArraySliceCircular(array $array, int $count, int $idx, array $expected): void {
    $actual = $this->callProtectedMethod(
      'Drupal\generated_content\Helpers\GeneratedContentHelper',
      'arraySliceCircular',
      [$array, $count, $idx]
    );
    $this->assertEquals($expected, $actual);
  }

  /**
   * Data provider for testArraySliceCircular.
   *
   * @return array<mixed>
   *   Provider test data.
   */
  public function dataProviderArraySliceCircular(): array {
    return [
      [[], 0, 0, []],
      [[], 5, 10, []],
      [['a'], 0, 0, []],

      [['a'], 1, 0, ['a']],
      [['a', 'b'], 1, 0, ['a']],
      [['a', 'b'], 2, 0, ['a', 'b']],

      [['a', 'b'], 3, 0, ['a', 'b', 'a']],
      [['a', 'b'], 4, 0, ['a', 'b', 'a', 'b']],

      [['a', 'b', 'c'], 3, 1, ['b', 'c', 'a']],
      [['a', 'b', 'c'], 4, 1, ['b', 'c', 'a', 'b']],

      [['a', 'b', 'c'], 4, 4, ['b', 'c', 'a', 'b']],
      [['a', 'b', 'c'], 4, 5, ['c', 'a', 'b', 'c']],

      [['a', 'b', 'c'], 1, 5, ['c']],

      [[1 => 'a', 3 => 'b', 4 => 'c'], 1, 5, ['c']],
      [[1 => 'a', 3 => 'b', 4 => 'c'], 2, 5, ['c', 'a']],

      [['k1' => 'a', 3 => 'b', 'k3' => 'c'], 1, 5, ['c']],
      [['k1' => 'a', 3 => 'b', 'k3' => 'c'], 2, 5, ['c', 'a']],
    ];
  }

}
