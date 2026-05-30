<?php

/**
 * @file
 * Fixture functions for GeneratedContentHelperVariationTest::variationFetchAll.
 *
 * The unique gc_test_var_ prefix prevents collisions with any
 * user-defined functions PHPUnit may load from Drupal core or contrib
 * before the test method runs.
 */

declare(strict_types=1);

/**
 * First variation contributor.
 *
 * @return array<array<string, string>>
 *   Variations.
 */
function gc_test_var_one(): array {
  return [
    ['title' => 'one-a'],
    ['title' => 'one-b'],
  ];
}

/**
 * Second variation contributor.
 *
 * @return array<array<string, string>>
 *   Variations.
 */
function gc_test_var_two(): array {
  return [
    ['title' => 'two-a'],
  ];
}

/**
 * A contributor that returns an empty array (must be skipped).
 *
 * @return array<mixed>
 *   Empty variations.
 */
function gc_test_var_empty(): array {
  return [];
}

/**
 * Post-process callback: tag every variation with a marker.
 *
 * @param array<array<string, string>> $variations
 *   Raw variations.
 *
 * @return array<array<string, string>>
 *   Post-processed variations.
 */
function gc_test_var_post_process(array $variations): array {
  foreach ($variations as &$variation) {
    $variation['post_processed'] = 'yes';
  }

  return $variations;
}
