<?php

declare(strict_types = 1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestFieldTrait;

/**
 * Tests field* helpers in GeneratedContentHelper class.
 *
 * @group generated_content
 */
class GeneratedContentHelperFieldTest extends GeneratedContentKernelTestBase {

  use GeneratedContentTestFieldTrait;

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  protected static $modules = [
    'field',
    'text',
    'options',
  ];

  /**
   * Tests the randomFieldAllowedValue() method.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testRandomFieldAllowedValue(): void {
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
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testRandomFieldAllowedValues(): void {
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
    $this->assertCount(3, array_intersect($actual_values, array_keys($allowed_values)));

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::randomFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 1);
    $this->assertCount(1, array_intersect($actual_values, array_keys($allowed_values)));

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::randomFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 2);
    $this->assertCount(2, array_intersect($actual_values, array_keys($allowed_values)));
  }

  /**
   * Tests the staticFieldAllowedValue() method.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testStaticFieldAllowedValue(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_value = $helper::staticFieldAllowedValue('node', $this->randomMachineName(), $this->randomMachineName());
    $this->assertNULL($actual_value);

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_value = $helper::staticFieldAllowedValue('node', $this->nodeTypes[0], $field_name);
    $this->assertNULL($actual_value);

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_value = $helper::staticFieldAllowedValue('node', $this->nodeTypes[0], $field_name);
    $this->assertSame($actual_value, array_keys($allowed_values)[0]);
    $actual_value = $helper::staticFieldAllowedValue('node', $this->nodeTypes[0], $field_name);
    $this->assertSame($actual_value, array_keys($allowed_values)[1]);
    $actual_value = $helper::staticFieldAllowedValue('node', $this->nodeTypes[0], $field_name);
    $this->assertSame($actual_value, array_keys($allowed_values)[2]);
    $actual_value = $helper::staticFieldAllowedValue('node', $this->nodeTypes[0], $field_name);
    $this->assertSame($actual_value, array_keys($allowed_values)[0]);
  }

  /**
   * Tests the staticFieldAllowedValues() method.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testStaticFieldAllowedValues(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_values = $helper::staticFieldAllowedValues('node', $this->randomMachineName(), $this->randomMachineName());
    $this->assertSame([], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->randomMachineName(), $this->randomMachineName());
    $this->assertSame([], $actual_values);

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name);
    $this->assertSame([], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name);
    $this->assertSame([], $actual_values);

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name);
    $this->assertSame(array_keys($allowed_values), $actual_values);

    $helper->reset();

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 1);
    $this->assertSame(['k1'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 1);
    $this->assertSame(['k2'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 1);
    $this->assertSame(['k3'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 1);
    $this->assertSame(['k1'], $actual_values);

    $helper->reset();

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 2);
    $this->assertSame(['k1', 'k2'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 2);
    $this->assertSame(['k3', 'k1'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 2);
    $this->assertSame(['k2', 'k3'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 2);
    $this->assertSame(['k1', 'k2'], $actual_values);

    $helper->reset();

    $field_name = strtolower($this->randomMachineName());
    $allowed_values = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];
    $this->fieldCreateListAllowedValues('node', $this->nodeTypes[0], $field_name, $allowed_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 5);
    $this->assertSame(['k1', 'k2', 'k3', 'k1', 'k2'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 5);
    $this->assertSame(['k3', 'k1', 'k2', 'k3', 'k1'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 5);
    $this->assertSame(['k2', 'k3', 'k1', 'k2', 'k3'], $actual_values);
    $actual_values = $helper::staticFieldAllowedValues('node', $this->nodeTypes[0], $field_name, 5);
    $this->assertSame(['k1', 'k2', 'k3', 'k1', 'k2'], $actual_values);
  }

}
