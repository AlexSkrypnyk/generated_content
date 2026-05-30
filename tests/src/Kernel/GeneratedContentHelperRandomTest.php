<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\Helpers\GeneratedContentHelper;

/**
 * Tests random content generation.
 *
 * @group generated_content
 */
class GeneratedContentHelperRandomTest extends GeneratedContentKernelTestBase {

  /**
   * Test randomSentence().
   */
  public function testRandomSentence(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomSentence();
    $word_count = count(explode(' ', $content));
    $this->assertGreaterThanOrEqual(5, $word_count);
    $this->assertLessThanOrEqual(10, $word_count);
    $this->assertSame('.', substr($content, -1));

    $content = $helper::randomSentence(4, 4);
    $word_count = count(explode(' ', $content));
    $this->assertSame(4, $word_count);
    $this->assertSame('.', substr($content, -1));

    $content1 = $helper::randomSentence();
    $content2 = $helper::randomSentence();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomString().
   */
  public function testRandomString(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomString();
    $this->assertSame(32, strlen($content));

    $content = $helper::randomString(5);
    $this->assertSame(5, strlen($content));

    $content1 = $helper::randomString();
    $content2 = $helper::randomString();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomName().
   */
  public function testRandomName(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomName();
    $this->assertSame(16, strlen($content));

    $content = $helper::randomName(5);
    $this->assertSame(5, strlen($content));

    $content1 = $helper::randomName();
    $content2 = $helper::randomName();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomAbbreviation().
   */
  public function testRandomAbbreviation(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomAbbreviation();
    $this->assertSame(2, strlen($content));

    $content = $helper::randomAbbreviation(5);
    $this->assertSame(5, strlen($content));

    $samples = [];
    for ($i = 0; $i < 20; $i++) {
      $samples[] = $helper::randomAbbreviation();
    }
    $this->assertGreaterThan(1, count(array_unique($samples)));
  }

  /**
   * Test randomPlainParagraph().
   */
  public function testRandomPlainParagraph(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomPlainParagraph();
    $this->assertStringNotContainsString("\n", $content);
    $this->assertStringNotContainsString("\r", $content);

    $content1 = $helper::randomPlainParagraph();
    $content2 = $helper::randomPlainParagraph();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomHtmlParagraph().
   */
  public function testRandomHtmlParagraph(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomHtmlParagraph();
    $this->assertStringNotContainsString("\n", $content);
    $this->assertStringNotContainsString("\r", $content);
    $this->assertStringContainsString("<p>", $content);
    $this->assertStringContainsString("</p>", $content);

    $content1 = $helper::randomHtmlParagraph();
    $content2 = $helper::randomHtmlParagraph();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomHtmlHeading().
   */
  public function testRandomHtmlHeading(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomHtmlHeading();
    $this->assertStringNotContainsString("\n", $content);
    $this->assertStringNotContainsString("\r", $content);
    $this->assertStringContainsString("<h1>", $content);
    $this->assertStringContainsString("</h1>", $content);
    $word_count = count(explode(' ', $content));
    $this->assertGreaterThanOrEqual(5, $word_count);
    $this->assertLessThanOrEqual(10, $word_count);

    $content = $helper::randomHtmlHeading(4, 4, 3);
    $this->assertStringNotContainsString("\n", $content);
    $this->assertStringNotContainsString("\r", $content);
    $this->assertStringContainsString("<h3>", $content);
    $this->assertStringContainsString("</h3>", $content);
    $word_count = count(explode(' ', $content));
    $this->assertSame(4, $word_count);

    $content = $helper::randomHtmlHeading(4, 4, 3, 'custom_prefix');
    $this->assertStringNotContainsString("\n", $content);
    $this->assertStringNotContainsString("\r", $content);
    $this->assertStringContainsString("<h3>custom_prefix", $content);
    $this->assertStringContainsString("</h3>", $content);
    $word_count = count(explode(' ', $content));
    $this->assertSame(4, $word_count);

    $content1 = $helper::randomHtmlHeading();
    $content2 = $helper::randomHtmlHeading();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomRichText().
   */
  public function testRandomRichText(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomRichText();
    $this->assertStringNotContainsString("\n", $content);
    $this->assertStringNotContainsString("\r", $content);
    $this->assertStringContainsString("<h2>", $content);
    $this->assertStringContainsString("</h2>", $content);
    $this->assertStringContainsString("<p>", $content);
    $this->assertStringContainsString("</p>", $content);
    $paragraphs_count = substr_count($content, '<p>');
    $this->assertGreaterThanOrEqual(4, $paragraphs_count);
    $this->assertLessThanOrEqual(12, $paragraphs_count);
    $headings_count = substr_count($content, '<h');
    $this->assertSame((int) ceil($paragraphs_count / 2), $headings_count);

    $content = $helper::randomRichText(4, 4);
    $this->assertStringNotContainsString("\n", $content);
    $this->assertStringNotContainsString("\r", $content);
    $this->assertStringContainsString("<h2>", $content);
    $this->assertStringContainsString("</h2>", $content);
    $this->assertStringContainsString("<p>", $content);
    $this->assertStringContainsString("</p>", $content);
    $paragraphs_count = substr_count($content, '<p>');
    $this->assertSame(4, $paragraphs_count);
    $headings_count = substr_count($content, '<h');
    $this->assertSame(2, $headings_count);

    $content1 = $helper::randomRichText();
    $content2 = $helper::randomRichText();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomEmail().
   */
  public function testRandomEmail(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomEmail();
    $this->assertStringContainsString('@', $content);
    $this->assertStringContainsString('.com', $content);

    $content = $helper::randomEmail('example.org');
    $this->assertStringContainsString('@example.org', $content);
    $this->assertStringNotContainsString('.com', $content);

    $content1 = $helper::randomEmail();
    $content2 = $helper::randomEmail();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomUrl().
   */
  public function testRandomUrl(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomUrl();
    $this->assertStringContainsString('https://www.example.com/', $content);
    $this->assertGreaterThan(strlen('https://www.example.com/') + 1, strlen($content));

    $content = $helper::randomUrl('www.example.org');
    $this->assertStringContainsString('https://www.example.org/', $content);
    $this->assertGreaterThan(strlen('https://www.example.org/') + 1, strlen($content));
    $this->assertStringNotContainsString('www.example.com', $content);

    $content = $helper::randomUrl('www.example.org/');
    $this->assertStringContainsString('https://www.example.org/', $content);
    $this->assertGreaterThan(strlen('https://www.example.org/') + 1, strlen($content));
    $this->assertStringNotContainsString('www.example.com', $content);

    $content1 = $helper::randomUrl();
    $content2 = $helper::randomUrl();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomUuid().
   */
  public function testRandomUuid(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $content = $helper::randomUuid();
    $parts = explode('-', $content);
    $this->assertCount(5, $parts);
    $this->assertMatchesRegularExpression('/[0-9a-f-]/', $content);

    $content1 = $helper::randomUuid();
    $content2 = $helper::randomUuid();
    $this->assertNotSame($content1, $content2);
  }

  /**
   * Test randomBool().
   */
  public function testRandomBool(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $value = $helper::randomBool();
    $this->assertIsBool($value);

    $value = $helper::randomBool(-1000);
    $this->assertIsBool($value);

    $value = $helper::randomBool(1000);
    $this->assertIsBool($value);
  }

  /**
   * Test randomArrayItems().
   */
  public function testRandomArrayItems(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $array = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];

    $values = $helper::randomArrayItems($array, 1);
    $this->assertIsArray($values);
    $this->assertCount(1, array_intersect($values, $array));
    $this->assertCount(1, array_intersect_key($values, $array));

    $values = $helper::randomArrayItems($array, 2);
    $this->assertIsArray($values);
    $this->assertCount(2, array_intersect($values, $array));
    $this->assertCount(2, array_intersect_key($values, $array));
  }

  /**
   * Test randomArrayItem().
   */
  public function testRandomArrayItem(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $array = [
      'k1' => 'v1',
      'k2' => 'v2',
      'k3' => 'v3',
    ];

    $value = $helper::randomArrayItem($array);
    $this->assertIsNotArray($value);
    $this->assertTrue(in_array($value, $array));
  }

  /**
   * Test randomArrayItem() with an empty haystack.
   */
  public function testRandomArrayItemEmpty(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->assertFalse($helper::randomArrayItem([]));
  }

  /**
   * Test randomArrayItems() with zero count.
   */
  public function testRandomArrayItemsZero(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->assertSame([], $helper::randomArrayItems(['a', 'b', 'c'], 0));
  }

  /**
   * Test randomTimestamp() default and explicit ranges.
   */
  public function testRandomTimestamp(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $default = $helper::randomTimestamp();
    $now = time();
    $one_year = 60 * 60 * 24 * 366;
    $this->assertGreaterThanOrEqual($now - $one_year, $default);
    $this->assertLessThanOrEqual($now + $one_year, $default);

    $explicit = $helper::randomTimestamp('-1day', '+1day');
    $this->assertGreaterThanOrEqual($now - 60 * 60 * 24 - 1, $explicit);
    $this->assertLessThanOrEqual($now + 60 * 60 * 24 + 1, $explicit);
  }

  /**
   * Test randomTimestamp() rejects invalid "from".
   */
  public function testRandomTimestampInvalidFrom(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('From value is not valid.');
    $helper::randomTimestamp('not-a-date');
  }

  /**
   * Test randomTimestamp() rejects invalid "to".
   */
  public function testRandomTimestampInvalidTo(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('To value is not valid.');
    $helper::randomTimestamp('-1year', 'also-not-a-date');
  }

  /**
   * Test randomDate() default behaviour and explicit format.
   */
  public function testRandomDate(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $date = $helper::randomDate();
    $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date);

    $date_with_time = $helper::randomDate('now', 'now', TRUE);
    $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:00$/', $date_with_time);

    $explicit_range = $helper::randomDate('2020-01-01', '2020-12-31');
    $this->assertGreaterThanOrEqual('2020-01-01', $explicit_range);
    $this->assertLessThanOrEqual('2020-12-31', $explicit_range);
  }

  /**
   * Test randomDate() rejects invalid "start".
   */
  public function testRandomDateInvalidStart(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Start value is not valid.');
    $helper::randomDate('garbage', 'now');
  }

  /**
   * Test randomDate() rejects invalid "finish".
   */
  public function testRandomDateInvalidFinish(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Finish value is not valid.');
    $helper::randomDate('now', 'garbage');
  }

  /**
   * Test randomDateRange() default format and value/end_value ordering.
   */
  public function testRandomDateRange(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $range = $helper::randomDateRange('2020-01-01', '2020-12-31');
    $this->assertArrayHasKey('value', $range);
    $this->assertArrayHasKey('end_value', $range);
    $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $range['value']);
    $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $range['end_value']);
    $this->assertLessThanOrEqual($range['end_value'], $range['value']);

    $custom = $helper::randomDateRange('2020-01-01', '2020-12-31', 'Y');
    $this->assertSame('2020', $custom['value']);
    $this->assertSame('2020', $custom['end_value']);
  }

  /**
   * Test randomDateRange() rejects invalid "start".
   */
  public function testRandomDateRangeInvalidStart(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Start value is not valid.');
    $helper::randomDateRange('garbage', '2020-12-31');
  }

  /**
   * Test randomDateRange() rejects invalid "finish".
   */
  public function testRandomDateRangeInvalidFinish(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Finish value is not valid.');
    $helper::randomDateRange('2020-01-01', 'garbage');
  }

  /**
   * Test randomDisperse() exercises both splice paths.
   *
   * Implementation uses array_splice($scope, rand(0, count($scope)),
   * 1, $filler) - when rand() returns count($scope) the splice
   * appends; otherwise it replaces an element. Either path can
   * overwrite a filler that an earlier iteration just placed, so we
   * cannot assert that every filler survives in the final array.
   * We assert size bounds and presence of at least one filler.
   */
  public function testRandomDisperse(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $scope = ['a', 'b', 'c', 'd'];
    $fillers = ['X', 'Y'];

    $result = $helper::randomDisperse($scope, $fillers);

    $this->assertGreaterThanOrEqual(count($scope), count($result));
    $this->assertLessThanOrEqual(count($scope) + count($fillers), count($result));
    $this->assertNotEmpty(array_intersect($fillers, $result));
  }

  /**
   * Test randomDisperse() with a single filler always places it.
   *
   * One iteration cannot overwrite itself, so the single filler is
   * always present in the result.
   */
  public function testRandomDisperseSingleFiller(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $result = $helper::randomDisperse(['a', 'b', 'c'], ['Z']);

    $this->assertContains('Z', $result);
  }

  /**
   * Test randomDisperse() with empty fillers leaves the scope unchanged.
   */
  public function testRandomDisperseEmptyFillers(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $scope = ['a', 'b', 'c'];
    $this->assertSame($scope, $helper::randomDisperse($scope, []));
  }

  /**
   * Test randomBool() with default skew returns booleans.
   */
  public function testRandomBoolDistribution(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $values = [];
    for ($i = 0; $i < 50; $i++) {
      $values[] = $helper::randomBool();
    }

    foreach ($values as $value) {
      $this->assertIsBool($value);
    }
  }

}
