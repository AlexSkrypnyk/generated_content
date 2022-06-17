<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestFieldTrait;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestMockTrait;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestNodeTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests field* helpers in GeneratedContentHelper class.
 *
 * @group generated_content
 */
class GeneratedContentHelperFieldTest extends GeneratedContentKernelTestBase {

  use UserCreationTrait;
  use GeneratedContentTestMockTrait;
  use GeneratedContentTestNodeTrait;
  use GeneratedContentTestFieldTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'node',
    'text',
    'options',
    'generated_content',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->nodeSetUp();
  }

  /**
   * Tests the randomFieldAllowedValue() method.
   */
  public function testRandomFieldAllowedValue() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_value = $helper::randomFieldAllowedValue('node', $this->randomMachineName(), $this->randomMachineName());
    $this->assertNULL($actual_value);

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_value = $helper::randomFieldAllowedValue('node', $this->nodeTypes[0], $field_name);
    $this->assertNULL($actual_value);

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_value = $helper::randomFieldAllowedValue('node', $this->nodeTypes[0], $field_name);
    $this->assertTrue(in_array($actual_value, array_keys($allowed_values)));
  }

  /**
   * Tests the randomFieldAllowedValues() method.
   */
  public function testRandomFieldAllowedValues() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_values = $helper::randomFieldAllowedValues('node', $this->randomMachineName(), $this->randomMachineName());
    $this->assertSame([], $actual_values);

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::randomFieldAllowedValues('node', $this->nodeTypes[0], $field_name);
    $this->assertSame([], $actual_values);

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::randomFieldAllowedValues('node', $this->nodeTypes[0], $field_name);
    $this->assertSame(3, count(array_intersect($actual_values, array_keys($allowed_values))));

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::randomFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 1);
    $this->assertSame(1, count(array_intersect($actual_values, array_keys($allowed_values))));

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::randomFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 2);
    $this->assertSame(2, count(array_intersect($actual_values, array_keys($allowed_values))));
  }

}
