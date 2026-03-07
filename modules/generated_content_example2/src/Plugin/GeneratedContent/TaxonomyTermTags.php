<?php

declare(strict_types=1);

namespace Drupal\generated_content_example2\Plugin\GeneratedContent;

use Drupal\Core\Link;
use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Generates taxonomy term tags entities.
 */
#[GeneratedContent(id: 'example2_taxonomy_term_tags', entity_type: 'taxonomy_term', bundle: 'tags', weight: 12)]
class TaxonomyTermTags extends GeneratedContentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function generate(): array {
    $total_terms_count = 10;

    $terms = [];

    for ($i = 0; $i < $total_terms_count; $i++) {
      $term = Term::create([
        'vid' => 'tags',
        'name' => 'Generated term ' . ($i + 1),
      ]);

      $term->save();

      $terms[] = $term;

      $this->helper::log(
        'Created "%s" term "%s" [ID: %s] %s',
        $term->bundle(),
        $term->toLink()->toString(),
        $term->id(),
        Link::createFromRoute('Edit', 'entity.taxonomy_term.edit_form', ['taxonomy_term' => $term->id()])->toString()
      );
    }

    return $terms;
  }

}
