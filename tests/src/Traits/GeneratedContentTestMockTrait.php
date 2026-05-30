<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Traits;

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

    return $property->getValue($class);
  }

  /**
   * Check if testing framework was ran with --debug option.
   */
  protected function isDebug(): bool {
    return in_array('--debug', $_SERVER['argv'], TRUE);
  }

}
