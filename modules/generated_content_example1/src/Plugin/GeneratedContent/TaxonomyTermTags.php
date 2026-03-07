<?php

declare(strict_types=1);

namespace Drupal\generated_content_example1\Plugin\GeneratedContent;

use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;

/**
 * Generates taxonomy term tags entities.
 */
#[GeneratedContent(id: 'example1_taxonomy_term_tags', entity_type: 'taxonomy_term', bundle: 'tags', weight: 11)]
class TaxonomyTermTags extends GeneratedContentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function generate(): array {
    // This should never run as the same hook is implemented in
    // generated_content_example2.
    return [];
  }

}
