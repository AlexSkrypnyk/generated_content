<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Unit;

use Drupal\generated_content\Plugin\ConfigFilter\GeneratedContentIgnoreFilter;

/**
 * Tests the GeneratedContentIgnoreFilter config filter.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\Plugin\ConfigFilter\GeneratedContentIgnoreFilter
 */
class GeneratedContentIgnoreFilterTest extends GeneratedContentUnitTestBase {

  /**
   * Tests filterWrite() with various config name / data combinations.
   *
   * @param string $name
   *   The config name being written.
   * @param array<mixed> $data
   *   The config data being written.
   * @param array<mixed> $expected
   *   The expected data after filtering.
   *
   * @dataProvider dataProviderFilterWrite
   */
  public function testFilterWrite(string $name, array $data, array $expected): void {
    $filter = new GeneratedContentIgnoreFilter([], 'generated_content_config_ignore', []);

    $this->assertSame($expected, $filter->filterWrite($name, $data));
  }

  /**
   * Data provider for testFilterWrite().
   *
   * @return array<string, array<mixed>>
   *   Provider data.
   */
  public static function dataProviderFilterWrite(): array {
    return [
      'core.extension removes generated_content from modules' => [
        'core.extension',
        ['module' => ['generated_content' => 0, 'node' => 0, 'user' => 0]],
        ['module' => ['node' => 0, 'user' => 0]],
      ],
      'core.extension without generated_content leaves modules unchanged' => [
        'core.extension',
        ['module' => ['node' => 0, 'user' => 0]],
        ['module' => ['node' => 0, 'user' => 0]],
      ],
      'core.extension preserves sibling keys' => [
        'core.extension',
        ['module' => ['generated_content' => 0, 'system' => 0], 'theme' => ['stark' => 0]],
        ['module' => ['system' => 0], 'theme' => ['stark' => 0]],
      ],
      'other config name is passed through unchanged' => [
        'system.site',
        ['name' => 'Site', 'mail' => 'a@example.com'],
        ['name' => 'Site', 'mail' => 'a@example.com'],
      ],
      'similarly named config is not filtered' => [
        'core.extension.backup',
        ['module' => ['generated_content' => 0]],
        ['module' => ['generated_content' => 0]],
      ],
    ];
  }

}
