<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Functional;

/**
 * Class GeneratedContentGenerationOnModuleInstallFunctionalTest.
 *
 * Test generation of content when a module with generated content items are
 * installed.
 *
 * @group generated_content
 */
class GeneratedContentGenerationOnModuleInstallFunctionalTest extends GeneratedContentFunctionalTestBase {

  /**
   * Test generation when modules are enabled.
   *
   * @param string[] $modules
   *   Modules.
   * @param string[] $env_vars
   *   Env vars.
   * @param int[] $expected_count
   *   Expected count.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @dataProvider dataProviderGenerateOnModuleInstall
   */
  public function testGenerateOnModuleInstall(array $modules, array $env_vars, array $expected_count): void {
    foreach ($env_vars as $env_var) {
      putenv($env_var);
    }

    $this->container->get('module_installer')->install($modules, TRUE);

    $admin = $this->createUser([], NULL, TRUE);
    $this->drupalLogin($admin);

    $this->drupalGet('/admin/config/development/generated-content');

    call_user_func_array([$this, 'assertInfoTableItems'], $expected_count);
  }

  /**
   * Data provider for testGenerateOnModuleInstall().
   *
   * @return array<mixed>
   *   Test data.
   */
  public function dataProviderGenerateOnModuleInstall(): array {
    return [
      // None from installed modules.
      [
        [
          'generated_content_example1',
          'generated_content_example2',
        ],
        [],
        [0, 0, 0, 0, 0, 0, 0],
      ],

      // All from all installed modules.
      [
        [
          'generated_content_example1',
          'generated_content_example2',
        ],
        [
          'GENERATED_CONTENT_CREATE=1',
        ],
        [0, 70, 10, 10, 10, 3, 10],
      ],

      // All from only installed modules.
      [
        [
          'generated_content_example2',
        ],
        [
          'GENERATED_CONTENT_CREATE=1',
        ],
        [NULL, NULL, NULL, 5, 10, 3, 10],
      ],

      // Selected from all installed modules.
      [
        [
          'generated_content_example1',
          'generated_content_example2',
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
          'generated_content_example2',
        ],
        [
          'GENERATED_CONTENT_CREATE=1',
          'GENERATED_CONTENT_ITEMS=node-page',
        ],
        [NULL, NULL, NULL, 0, 0, 3, 0],
      ],
    ];
  }

}
