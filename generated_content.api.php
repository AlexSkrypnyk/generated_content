<?php

/**
 * @file
 * API for generated content module.
 *
 * The hook implementations below must be created in the file named
 * 'generated_content/{entity_type}/{bundle}.inc' for each entity type and
 * bundle that should be generated.
 */

use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\taxonomy\Entity\Term;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight() {
  // Define custom weight for this content generation.
  //
  // If hook is not implemented - defaults to 0 for this entity type/bundle.
  return 0;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking() {
  // Define if this entity type/bundle should be tracked to be considered
  // as a generated content so that it could be referenced when creating
  // other generated content or removing this entity type/bundle entities.
  //
  // If hook is not implemented - defaults to TRUE making this
  // entity type/bundle being tracked.
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function hook_generated_content_create_ENTITY_TYPE_BUNDLE() {
  // Create entity type/bundle entities.
  //
  // Implementation must:
  // - save entities
  // - return array of saved entities
  //
  // Example implementation below to create 50 terms.
  //
  // Total number of terms to create.
  $total_terms_count = 50;

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
      'Created "%s" term "%s" (id: %s)',
      $term->bundle(),
      $term->toLink()->toString(),
      $term->id()
    );
  }

  // Return created term instances.
  return $terms;
}
