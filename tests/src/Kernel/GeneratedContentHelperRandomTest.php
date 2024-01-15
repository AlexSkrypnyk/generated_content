<?php

declare(strict_types = 1);

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

    $content1 = $helper::randomAbbreviation();
    $content2 = $helper::randomAbbreviation();
    $this->assertNotSame($content1, $content2);
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

}
