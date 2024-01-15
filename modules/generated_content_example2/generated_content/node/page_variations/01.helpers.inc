<?php

/**
 * @file
 * Helpers for variations for Page nodes.
 */

declare(strict_types = 1);

/**
 * Variations post-process callback.
 *
 * @param array<mixed> $variations
 *   Variations.
 *
 * @return array<mixed>
 *   Variations.
 */
function _generated_content_example2_create_node_page__variations__post_process(array $variations): array {
  // Add common variation properties to all variations, but only if they were
  // not provided within variations.
  foreach ($variations as &$variation) {
    $variation += [
      'status' => TRUE,
      'alias' => '/generated-content/page/{title}',
    ];
  }

  return $variations;
}
