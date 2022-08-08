<?php

namespace Drupal\generated_content\Helpers;

use Drupal\Component\Utility\Random;

/**
 * Class GeneratedContentRandomTrait.
 *
 * Random content generators.
 *
 * @package Drupal\generated_content
 */
trait GeneratedContentRandomTrait {

  /**
   * Generate a random sentence.
   */
  public static function randomSentence($min_word_count = 5, $max_word_count = 10) {
    $randomiser = new Random();

    $content = $randomiser->sentences(mt_rand($min_word_count, $max_word_count), TRUE);
    $content = rtrim($content, '.') . '.';

    return $content;
  }

  /**
   * Generates a random string.
   */
  public static function randomString($length = 32) {
    $randomiser = new Random();

    return $randomiser->string($length);
  }

  /**
   * Generates a name.
   */
  public static function randomName($length = 16) {
    $randomiser = new Random();

    return $randomiser->name($length, TRUE);
  }

  /**
   * Generates a letter abbreviation.
   *
   * @param int $length
   *   Length of abbreviation.
   *
   * @return string
   *   Abbreviation string.
   */
  public static function randomAbbreviation($length = 2) {
    $randomiser = new Random();

    return $randomiser->name($length, TRUE);
  }

  /**
   * Generate a random plain text paragraph.
   */
  public static function randomPlainParagraph() {
    $randomiser = new Random();

    return str_replace(["\r", "\n"], '', $randomiser->paragraphs(1));
  }

  /**
   * Generate a random HTML paragraph.
   */
  public static function randomHtmlParagraph() {
    return '<p>' . static::randomPlainParagraph() . '</p>';
  }

  /**
   * Generate a random HTML heading.
   */
  public static function randomHtmlHeading($min_word_count = 5, $max_word_count = 10, $level = 1, $prefix = '') {
    if (!$level) {
      $level = mt_rand(2, 5);
    }

    return '<h' . $level . '>' . $prefix . static::randomSentence($min_word_count, $max_word_count) . '</h' . $level . '>';
  }

  /**
   * Generate random HTML paragraphs.
   *
   * @param int $min_paragraph_count
   *   Minimum number of paragraphs to generate.
   * @param int $max_paragraph_count
   *   Maximum number of paragraphs to generate.
   * @param string $prefix
   *   Optional prefix to add to the very first heading.
   *
   * @return string
   *   Paragraphs.
   */
  public static function randomRichText($min_paragraph_count = 4, $max_paragraph_count = 12, $prefix = '') {
    $paragraphs = [];
    $paragraph_count = mt_rand($min_paragraph_count, $max_paragraph_count);
    for ($i = 1; $i <= $paragraph_count; $i++) {
      if ($i % 2) {
        $paragraphs[] = static::randomHtmlHeading(5, 10, $i == 1 ? 2 : rand(2, 4), $prefix);
      }
      $paragraphs[] = static::randomHtmlParagraph();
    }

    return implode('', $paragraphs);
  }

  /**
   * Return a random email address.
   *
   * @param string $domain
   *   Optional domain. If not provided, a random domain will be generated.
   *
   * @return string
   *   Random email address.
   */
  public static function randomEmail($domain = NULL) {
    $randomiser = new Random();
    $domain = $domain ?? $randomiser->name() . '.com';

    return $randomiser->name() . '@' . $domain;
  }

  /**
   * Generate random external URL.
   *
   * @param string|bool $domain
   *   (optional) Domain name. Defaults to 'www.example.com'.
   *
   * @return string
   *   URL with a path.
   */
  public static function randomUrl($domain = FALSE) {
    $parts = [];
    $parts[] = 'https://';
    $parts[] = $domain ? rtrim($domain, '/') : 'www.example.com';
    $parts[] = '/';
    $parts[] = str_replace(' ', '-', static::randomSentence());

    return implode('', $parts);
  }

  /**
   * Generate a random 36-character UUID.
   */
  public static function randomUuid() {
    $data = random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100.
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10.
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }

  /**
   * Generate a random boolean value.
   *
   * @param int $skew
   *   Amount to skew towards one of the values. FALSE is on the left of the
   *   skew line, and TRUE is on the right.
   *   For example, ::bool(33), will have a probability 1/3 for FALSE and
   *   2/3 for TRUE.
   *
   * @return bool
   *   Random value.
   */
  public static function randomBool($skew = 50) {
    return mt_rand(0, 100) > max(min($skew, 100), 0);
  }

  /**
   * Return a random timestamp.
   */
  public static function randomTimestamp($from = '-1year', $to = "+1year") {
    $from = strtotime($from);
    $to = strtotime($to);

    return mt_rand($from, $to);
  }

  /**
   * Generate a random date with or without time.
   *
   * @param string $start
   *   (optional) Start offset in format suitable for strtotime().
   *   Defaults to "now".
   * @param string $finish
   *   (optional) Finish offset in format suitable for strtotime().
   *   Defaults to "now".
   * @param bool $with_time
   *   (optional) Whether or not to include time. Defaults to FALSE.
   *
   * @return string
   *   Random date string with or without time.
   */
  public static function randomDate($start = 'now', $finish = 'now', $with_time = FALSE) {
    $start = strtotime($start);
    $finish = strtotime($finish);

    $start = min($start, $finish);
    $finish = max($start, $finish);

    $format = 'Y-m-d';
    if ($with_time) {
      $format .= '\TH:i:00';
    }

    $timestamp = rand($start, $finish);

    return date($format, $timestamp);
  }

  /**
   * Generate a random date range.
   *
   * @param string $start
   *   Start offset in format suitable for strtotime().
   * @param string $finish
   *   Finish offset in format suitable for strtotime().
   * @param string $format
   *   (optional) Date format. Defaults to 'Y-m-d'.
   *
   * @return array
   *   Array of values suitable for daterange field:
   *   - value: (string) Range start value.
   *   - end_value: (string) Range end value.
   */
  public static function randomDateRange($start, $finish, $format = 'Y-m-d') {
    $start = strtotime($start);
    $finish = strtotime($finish);

    $start = min($start, $finish);
    $finish = max($start, $finish);

    $start = rand($start, $finish - 1);
    $finish = rand($start + 1, $finish);

    return [
      'value' => date($format, $start),
      'end_value' => date($format, $finish),
    ];
  }

  /**
   * Disperse $fillers within $scope.
   */
  public static function randomDisperse(array $scope, array $fillers) {
    foreach ($fillers as $filler) {
      array_splice($scope, rand(0, count($scope)), 1, $filler);
    }

    return $scope;
  }

  /**
   * Helper to get random array items.
   */
  public static function randomArrayItems($haystack, $count) {
    if ($count == 0) {
      return [];
    }

    $haystack_keys = array_keys($haystack);
    shuffle($haystack_keys);
    $haystack_keys = array_slice($haystack_keys, 0, $count);

    return array_intersect_key($haystack, array_flip($haystack_keys));
  }

  /**
   * Helper to get a single random array item.
   */
  public static function randomArrayItem($haystack) {
    if (empty($haystack)) {
      return FALSE;
    }

    $items = static::randomArrayItems($haystack, 1);

    return count($items) > 0 ? reset($items) : FALSE;
  }

}
