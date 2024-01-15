<?php

/**
 * @file
 * Create generated Tags terms.
 */

declare(strict_types = 1);

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\taxonomy\Entity\Term;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags_weight(): int {
  return 12;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags_tracking(): bool {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 *
 * @phpstan-ignore-next-line
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags(): array {
  // Total number of terms to create.
  $total_terms_count = 10;

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
      Link::createFromRoute('Edit', 'entity.taxonomy_term.edit_form', ['taxonomy_term' => $term->id()])->toString()
    );
  }

  // Return created term instances.
  return $terms;
}
