<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Traits;

use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Trait GeneratedContentTestTermTrait.
 *
 * Trait with term-related helpers.
 *
 * @package Drupal\generated_content\Tests
 *
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
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
  public function termSetUp(): void {
    $this->installEntitySchema('taxonomy_vocabulary');
    $this->installEntitySchema('taxonomy_term');

    for ($i = 0; $i < 3; $i++) {
      $vocab = Vocabulary::create([
        'vid' => $this->randomMachineName(),
        'name' => $this->randomString(),
      ]);
      $vocab->save();
      $this->vids[] = (string) $vocab->id();
    }
  }

  /**
   * Prepare terms to be used in tests.
   *
   * @param int $count
   *   Num of terms.
   * @param string[]|null $vids
   *   Vids.
   * @param bool $single_vid
   *   Is sigle vid.
   *
   * @return array<mixed>
   *   List terms group by vids.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function prepareTerms(int $count, array $vids = NULL, bool $single_vid = FALSE): array {
    $vids = $vids ?? $this->vids;
    $terms = [];
    foreach ($vids as $vid) {
      for ($i = 0; $i < $count; $i++) {
        $term = $this->container->get('entity_type.manager')->getStorage('taxonomy_term')->create([
          'vid' => $vid,
          'name' => sprintf('Term %s from vocabulary %s.', $i + 1, $vid),
        ]);
        $saved = $term->save();
        if ($saved && $term->id()) {
          $terms[$vid][$term->id()] = $term;
        }
      }
    }

    if ($single_vid) {
      $reset_terms = reset($terms);
      if ($reset_terms) {
        $terms = $reset_terms;
      }
    }

    return $terms;
  }

}
