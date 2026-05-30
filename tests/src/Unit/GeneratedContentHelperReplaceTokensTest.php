<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Unit;

use Drupal\generated_content\Helpers\GeneratedContentHelper;

/**
 * Tests GeneratedContentHelper::replaceTokens().
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\Helpers\GeneratedContentHelper::replaceTokens
 */
class GeneratedContentHelperReplaceTokensTest extends GeneratedContentUnitTestBase {

  /**
   * Tests scalar replacement with default delimiters.
   */
  public function testReplaceTokensScalarValues(): void {
    $result = GeneratedContentHelper::replaceTokens(
      'Hello {name}, you have {count} new messages.',
      ['name' => 'Alex', 'count' => 3]
    );

    $this->assertSame('Hello Alex, you have 3 new messages.', $result);
  }

  /**
   * Tests that non-scalar values are skipped.
   */
  public function testReplaceTokensSkipsNonScalars(): void {
    $result = GeneratedContentHelper::replaceTokens(
      'Keep {arr} and {obj} unchanged, replace {str}.',
      [
        'arr' => ['x', 'y'],
        'obj' => new \stdClass(),
        'str' => 'OK',
      ]
    );

    $this->assertStringContainsString('{arr}', $result);
    $this->assertStringContainsString('{obj}', $result);
    $this->assertStringContainsString('OK', $result);
  }

  /**
   * Tests that a callback transforms the value before insertion.
   */
  public function testReplaceTokensWithCallback(): void {
    $result = GeneratedContentHelper::replaceTokens(
      'Loud: {value}',
      ['value' => 'whisper'],
      static fn($v) => is_string($v) ? strtoupper($v) : $v
    );

    $this->assertSame('Loud: WHISPER', $result);
  }

  /**
   * Tests custom begin/end token delimiters.
   */
  public function testReplaceTokensCustomDelimiters(): void {
    $result = GeneratedContentHelper::replaceTokens(
      'Hello [[name]]!',
      ['name' => 'World'],
      NULL,
      '[[',
      ']]'
    );

    $this->assertSame('Hello World!', $result);
  }

  /**
   * Tests that strings with no replacements are returned unchanged.
   */
  public function testReplaceTokensNoReplacements(): void {
    $result = GeneratedContentHelper::replaceTokens('Static text', []);

    $this->assertSame('Static text', $result);
  }

}
