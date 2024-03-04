<?php

/**
 * @file
 * Create generated Tags terms.
 */

declare(strict_types=1);

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example1_generated_content_create_taxonomy_term_tags_weight(): int {
  return 11;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example1_generated_content_create_taxonomy_term_tags_tracking(): bool {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 *
 * @phpstan-ignore-next-line
 */
function generated_content_example1_generated_content_create_taxonomy_term_tags(): array {
  // This should never run as the same hook is implemented in
  // generated_content_example2.
  return [];
}
