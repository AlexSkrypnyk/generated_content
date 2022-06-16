<?php

namespace Drupal\Tests\generated_content\Traits;

use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Trait GeneratedContentTestTermTrait.
 *
 * Trait with term-related helpers.
 *
 * @package Drupal\generated_content\Tests
 */
trait GeneratedContentTestTermTrait {

  /**
   * Vocabularies.
   *
   * @var string[]
   */
  protected $vids = [];

  /**
   * Test setup for term.
   */
  public function termSetUp() {
    $this->installEntitySchema('taxonomy_vocabulary');
    $this->installEntitySchema('taxonomy_term');

    for ($i = 0; $i < 3; $i++) {
      $vocab = Vocabulary::create([
        'vid' => $this->randomMachineName(),
        'name' => $this->randomString(),
      ]);
      $vocab->save();
      $this->vids[] = $vocab->id();
    }
  }

  /**
   * Prepare terms to be used in tests.
   */
  protected function prepareTerms($count, $vids = NULL, $single_vid = FALSE) {
    $vids = $vids ?? $this->vids;
    $terms = [];
    foreach ($vids as $vid) {
      for ($i = 0; $i < $count; $i++) {
        $term = $this->container->get('entity_type.manager')->getStorage('taxonomy_term')->create([
          'vid' => $vid,
          'name' => sprintf('Term %s from vocabulary %s.', $i + 1, $vid),
        ]);
        $term->save();
        $terms[$vid][$term->id()] = $term;
      }
    }

    if ($single_vid) {
      $terms = reset($terms);
    }

    return $terms;
  }

}
