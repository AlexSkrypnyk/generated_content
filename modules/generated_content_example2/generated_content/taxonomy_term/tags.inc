<?php

/**
 * @file
 * Create generated Tags terms.
 */

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\taxonomy\Entity\Term;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags_weight() {
  return 12;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags_tracking() {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags() {
  // Total number of terms to create.
  $total_terms_count = 10;

  // Generated content helper to get access to logging.
  /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
  $helper = GeneratedContentHelper::getInstance();

  $terms = [];

  for ($i = 0; $i < $total_terms_count; $i++) {
    // Create a term instance.
    $term = Term::create([
      'vid' => 'tags',
      'name' => 'Generated term ' . ($i + 1),
    ]);

    // Save term instance.
    $term->save();

    // Track saved term instance to return.
    $terms[] = $term;

    // Log creation of this entity.
    $helper::log(
      'Created "%s" term "%s" [ID: %s] %s',
      $term->bundle(),
      $term->toLink()->toString(),
      $term->id(),
      Link::createFromRoute('Edit', 'entity.taxonomy_term.edit_form', ['taxonomy_term' => $term->id()])->toString(),
    );
  }

  // Return created term instances.
  return $terms;
}
