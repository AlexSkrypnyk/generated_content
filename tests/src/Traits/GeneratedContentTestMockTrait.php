<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Traits;

use PHPUnit\Framework\MockObject\Stub\Stub;

/**
 * Trait GeneratedContentTestHelperTrait.
 *
 * Helper trait for tests.
 *
 * @package Drupal\generated_content\Tests
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
trait GeneratedContentTestMockTrait {

  /**
   * Call protected methods on the class.
   *
   * @param object|class-string $object
   *   Object or class name to use for a method call.
   * @param string $method
   *   Method name. Method can be static.
   * @param array<mixed> $args
   *   Array of arguments to pass to the method. To pass arguments by reference,
   *   pass them by reference as an element of this array.
   *
   * @return mixed
   *   Method result.
   *
   * @throws \ReflectionException
   */
  protected static function callProtectedMethod($object, string $method, array $args = []) {
    $class = new \ReflectionClass(is_object($object) ? get_class($object) : $object);
    $reflectionMethod = $class->getMethod($method);
    $reflectionMethod->setAccessible(TRUE);
    $object = $reflectionMethod->isStatic() || is_string($object) ? NULL : $object;

    return $reflectionMethod->invokeArgs($object, $args);
  }

  /**
   * Set protected property value.
   *
   * @param object $object
   *   Object to set the value on.
   * @param string $property
   *   Property name to set the value. Property should exists in the object.
   * @param mixed $value
   *   Value to set to the property.
   */
  protected static function setProtectedValue(object $object, string $property, $value): void {
    $class = new \ReflectionClass(get_class($object));
    $property = $class->getProperty($property);
    $property->setAccessible(TRUE);

    $property->setValue($object, $value);
  }

  /**
   * Get protected value from the object.
   *
   * @param object $object
   *   Object to set the value on.
   * @param string $property
   *   Property name to get the value. Property should exists in the object.
   *
   * @return mixed
   *   Protected property value.
   */
  protected static function getProtectedValue(object $object, string $property) {
    $class = new \ReflectionClass(get_class($object));
    $property = $class->getProperty($property);
    $property->setAccessible(TRUE);

    return $property->getValue($class);
  }

  /**
   * Helper to prepare class mock.
   *
   * @param class-string|object $class
   *   Class name to generate the mock.
   * @param array<string, mixed|\PHPUnit\Framework\MockObject\Stub\Stub> $methodsMap
   *   Optional array of methods and values, keyed by method name.
   * @param array<mixed> $args
   *   Optional array of constructor arguments. If omitted, a constructor will
   *   not be called.
   *
   * @return \PHPUnit\Framework\MockObject\MockObject|string
   *   Mocked class.
   *
   * @throws \ReflectionException
   */
  protected function prepareMock($class, array $methodsMap = [], array $args = []) {
    $methods = array_keys($methodsMap);

    $reflectionClass = new \ReflectionClass($class);

    $class_name = is_object($class) ? get_class($class) : $class;

    if ($reflectionClass->isAbstract()) {
      $mock = $this->getMockForAbstractClass(
        $class_name, $args, '', !empty($args), TRUE, TRUE, $methods
      );
    }
    else {
      $mock = $this->getMockBuilder($class_name);
      if (!empty($args)) {
        $mock = $mock->enableOriginalConstructor()
          ->setConstructorArgs($args);
      }
      else {
        $mock = $mock->disableOriginalConstructor();
      }
      $mock = $mock->onlyMethods($methods)
        ->getMock();
    }

    foreach ($methodsMap as $method => $value) {
      // Handle callback values differently.
      if ($value instanceof Stub && strpos(get_class($value), 'Callback') !== FALSE) {
        $mock->expects($this->any())
          ->method($method)
          ->will($value);
      }
      else {
        $mock->expects($this->any())
          ->method($method)
          ->willReturn($value);
      }
    }

    return $mock;
  }

  /**
   * Check if testing framework was ran with --debug option.
   */
  protected function isDebug(): bool {
    return in_array('--debug', $_SERVER['argv'], TRUE);
  }

}
