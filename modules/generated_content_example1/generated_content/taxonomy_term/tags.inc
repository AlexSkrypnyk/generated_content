<?php

/**
 * @file
 * Create generated Tags terms.
 */

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example1_generated_content_create_taxonomy_term_tags_weight() {
  return 11;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example1_generated_content_create_taxonomy_term_tags_tracking() {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example1_generated_content_create_taxonomy_term_tags() {
  // This should never run as the same hook is implemented in
  // generated_content_example2.
  return [];
}
