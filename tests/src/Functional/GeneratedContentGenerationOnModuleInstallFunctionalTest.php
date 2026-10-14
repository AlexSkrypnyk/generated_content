<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Functional;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DataProvider;
use Drupal\Core\Logger\RfcLogLevel;

/**
 * Class GeneratedContentGenerationOnModuleInstallFunctionalTest.
 *
 * Test generation of content when a module with generated content items are
 * installed.
 *
 * @group generated_content
 */
#[Group('generated_content')]
#[RunTestsInSeparateProcesses]
class GeneratedContentGenerationOnModuleInstallFunctionalTest extends GeneratedContentFunctionalTestBase {

  /**
   * Test generation when modules are enabled.
   *
   * @param array<string[]> $modules_batches
   *   A list of module batches to install.
   * @param string[] $env_vars
   *   Env vars.
   * @param int[] $expected_count
   *   Expected count.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @dataProvider dataProviderGenerateOnModuleInstall
   * @group wip1
   */
  #[Group('wip1')]
  #[DataProvider('dataProviderGenerateOnModuleInstall')]
  public function testGenerateOnModuleInstall(array $modules_batches, array $env_vars, array $expected_count): void {
    foreach ($env_vars as $env_var) {
      putenv($env_var);
    }

    // Deliberately install modules in batches to assert that the content is
    // generated not only when modules installed in bulk but also when
    // installed one by one.
    foreach ($modules_batches as $modules) {
      $this->container->get('module_installer')->install($modules);
    }

    $admin = $this->createUser([], NULL, TRUE);

    $this->drupalLogin($admin);

    $this->drupalGet('/admin/config/development/generated-content');

    $this->assertInfoTableItems(...$expected_count);
  }

  /**
   * Data provider for testGenerateOnModuleInstall().
   *
   * @return array<mixed>
   *   Test data.
   */
  public static function dataProviderGenerateOnModuleInstall(): array {
    return [
      // None from installed modules.
      [
        [
          [
            'generated_content',
            'generated_content_example1',
            'generated_content_example2',
          ],
        ],
        [],
        [0, 0, 0, 0, 0, 0, 0],
      ],

      // All from all installed modules.
      [
        [
          [
            'generated_content',
            'generated_content_example1',
            'generated_content_example2',
          ],
        ],
        [
          'GENERATED_CONTENT_CREATE=1',
        ],
        [0, 70, 10, 10, 10, 3, 10],
      ],

      // All from all installed modules, but examples installed first.
      [
        [
          [
            'generated_content_example1',
            'generated_content_example2',
          ],
          [
            'generated_content',
          ],
        ],
        [
          'GENERATED_CONTENT_CREATE=1',
        ],
        [0, 70, 10, 10, 10, 3, 10],
      ],

      // All from only installed modules.
      [
        [
          [
            'generated_content',
            'generated_content_example2',
          ],
        ],
        [
          'GENERATED_CONTENT_CREATE=1',
        ],
        [NULL, NULL, NULL, 5, 10, 3, 10],
      ],

      // Selected from all installed modules.
      [
        [
          [
            'generated_content',
            'generated_content_example1',
            'generated_content_example2',
          ],
        ],
        [
          'GENERATED_CONTENT_CREATE=1',
          'GENERATED_CONTENT_ITEMS=file-file,media-image,taxonomy_term-tags,node-page',
        ],
        [0, 70, 10, 0, 10, 3, 0],
      ],

      // Selected from only installed modules.
      [
        [
          [
            'generated_content',
            'generated_content_example2',
          ],
        ],
        [
          'GENERATED_CONTENT_CREATE=1',
          'GENERATED_CONTENT_ITEMS=node-page',
        ],
        [NULL, NULL, NULL, 0, 0, 3, 0],
      ],
    ];
  }

  /**
   * Test that install-time generation reports progress to the logger channel.
   */
  public function testInstallTimeGenerationIsLogged(): void {
    putenv('GENERATED_CONTENT_CREATE=1');

    $this->container->get('module_installer')->install([
      'generated_content',
      'generated_content_example1',
      'generated_content_example2',
    ]);

    putenv('GENERATED_CONTENT_CREATE');

    $messages = $this->getGeneratedContentLog();

    $this->assertNotEmpty(array_filter($messages, static fn(string $message): bool => str_starts_with($message, 'Started creation of generated content from modules:')));
    $this->assertNotEmpty(array_filter($messages, static fn(string $message): bool => str_starts_with($message, 'Created generated content entities')));
    $this->assertContains('Finished creation of generated content.', $messages);
  }

  /**
   * Get info-level messages logged to the generated_content channel.
   *
   * @return string[]
   *   The logged message text.
   */
  protected function getGeneratedContentLog(): array {
    $rows = $this->container->get('database')->select('watchdog', 'w')
      ->fields('w', ['message'])
      ->condition('type', 'generated_content')
      ->condition('severity', RfcLogLevel::INFO)
      ->execute()
      ->fetchCol();

    return array_map(strval(...), $rows);
  }

}
