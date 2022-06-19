<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestTermTrait;

/**
 * Tests term* helpers in GeneratedContentHelper class.
 *
 * @group generated_content
 */
class GeneratedContentHelperTaxonomyTest extends GeneratedContentKernelTestBase {

  use GeneratedContentTestTermTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field',
    'text',
    'taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->termSetUp();
  }

  /**
   * Tests the randomTerm() method.
   */
  public function testRandomTerm() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_term = $helper::randomTerm();
    $this->assertNULL($actual_term);

    $terms = $this->prepareTerms(5);
    $terms_merged = array_merge($terms[$this->vids[0]], $terms[$this->vids[1]], $terms[$this->vids[2]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($terms_merged);

    $actual_term = $helper::randomTerm();
    $this->assertTrue(in_array($actual_term->id(), $this->replaceEntitiesWithIds($terms_merged)));

    $actual_term = $helper::randomTerm($this->vids[0]);
    $this->assertTrue(in_array($actual_term->id(), $this->replaceEntitiesWithIds($terms_merged)));
    $actual_term = $helper::randomTerm($this->vids[1]);
    $this->assertTrue(in_array($actual_term->id(), $this->replaceEntitiesWithIds($terms_merged)));
    $actual_term = $helper::randomTerm($this->vids[2]);
    $this->assertTrue(in_array($actual_term->id(), $this->replaceEntitiesWithIds($terms_merged)));

    $actual_term = $helper::randomTerm($this->randomString());
    $this->assertNull($actual_term);
  }

  /**
   * Tests the randomTerms() method.
   */
  public function testRandomTerms() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_terms = $helper::randomTerms();
    $this->assertSame([], $actual_terms);

    $terms = $this->prepareTerms(5);
    $terms_merged = array_merge($terms[$this->vids[0]], $terms[$this->vids[1]], $terms[$this->vids[2]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($terms_merged);

    $actual_terms = $helper::randomTerms();
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms_merged))));

    $actual_terms = $helper::randomTerms(NULL, 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms_merged))));

    $actual_terms = $helper::randomTerms(NULL, 20);
    $this->assertSame(15, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms_merged))));

    $actual_terms = $helper::randomTerms($this->vids[0]);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[0]]))));

    $actual_terms = $helper::randomTerms($this->vids[0], 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[0]]))));

    $actual_terms = $helper::randomTerms($this->vids[0], 20);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[0]]))));
  }

  /**
   * Tests the randomRealTerm() method.
   */
  public function testRandomRealTerm() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_term = $helper::randomRealTerm();
    $this->assertNull($actual_term);

    $terms = $this->prepareTerms(5);
    $terms_merged = array_merge($terms[$this->vids[0]], $terms[$this->vids[1]], $terms[$this->vids[2]]);

    $terms_in_repository = array_slice($terms_merged, 0, 7, TRUE);
    $terms_not_in_repository = array_slice($terms_merged, 7, 8, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($terms_in_repository);

    $this->assertTrue(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_not_in_repository)));
    $this->assertFalse(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_in_repository)));
    $this->assertFalse(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_in_repository)));
    $this->assertFalse(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_in_repository)));
    $this->assertFalse(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_in_repository)));
    $this->assertFalse(in_array($helper::randomRealTerm()->id(), $this->replaceEntitiesWithIds($terms_in_repository)));
  }

  /**
   * Tests the randomRealTerms() method.
   */
  public function testRandomRealTerms() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_terms = $helper::randomRealTerms();
    $this->assertSame([], $actual_terms);

    $terms = $this->prepareTerms(5);
    $terms_merged = array_merge($terms[$this->vids[0]], $terms[$this->vids[1]], $terms[$this->vids[2]]);

    $terms_in_repository = array_slice($terms_merged, 0, 7, TRUE);
    $terms_not_in_repository = array_slice($terms_merged, 7, 8, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($terms_in_repository);

    $actual_terms = $helper::randomRealTerms();
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms_not_in_repository))));
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms_in_repository))));

    $actual_terms = $helper::randomRealTerms(NULL, 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms_not_in_repository))));

    $actual_terms = $helper::randomRealTerms(NULL, 10);
    $this->assertSame(8, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms_not_in_repository))));

    $actual_terms = $helper::randomRealTerms($this->vids[0]);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[0]]))));
    $actual_terms = $helper::randomRealTerms($this->vids[1]);
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[1]]))));
    $actual_terms = $helper::randomRealTerms($this->vids[2]);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[2]]))));

    $actual_terms = $helper::randomRealTerms($this->vids[0], 2);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[0]]))));
    $actual_terms = $helper::randomRealTerms($this->vids[1], 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[1]]))));
    $actual_terms = $helper::randomRealTerms($this->vids[2], 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[2]]))));

    $actual_terms = $helper::randomRealTerms($this->vids[0], 20);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[0]]))));
    $actual_terms = $helper::randomRealTerms($this->vids[1], 20);
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[1]]))));
    $actual_terms = $helper::randomRealTerms($this->vids[2], 20);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $this->replaceEntitiesWithIds($terms[$this->vids[2]]))));
  }

  /**
   * Tests the staticTerm() method.
   */
  public function testStaticTerm() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_term = $helper::staticTerm();
    $this->assertNull($actual_term);

    $terms = $this->prepareTerms(3);
    $terms_merged = array_merge($terms[$this->vids[0]], $terms[$this->vids[1]], $terms[$this->vids[2]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($terms_merged);

    $ids = $this->replaceEntitiesWithIds($terms_merged);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[5]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[6]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[7]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[8]);
    $this->assertSame($helper::staticTerm()->id(), array_values($ids)[0]);

    $this->assertSame($helper::staticTerm($this->vids[0])->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticTerm($this->vids[0])->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticTerm($this->vids[0])->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticTerm($this->vids[0])->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticTerm($this->vids[1])->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticTerm($this->vids[1])->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticTerm($this->vids[1])->id(), array_values($ids)[5]);
    $this->assertSame($helper::staticTerm($this->vids[1])->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticTerm($this->vids[2])->id(), array_values($ids)[6]);
    $this->assertSame($helper::staticTerm($this->vids[2])->id(), array_values($ids)[7]);
    $this->assertSame($helper::staticTerm($this->vids[2])->id(), array_values($ids)[8]);
    $this->assertSame($helper::staticTerm($this->vids[2])->id(), array_values($ids)[6]);
  }

  /**
   * Tests the staticTerms() method.
   */
  public function testStaticTerms() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_terms = $helper::staticTerms();
    $this->assertSame([], $actual_terms);

    $terms = $this->prepareTerms(3);
    $terms_merged = array_merge($terms[$this->vids[0]], $terms[$this->vids[1]], $terms[$this->vids[2]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($terms_merged);

    $ids = array_values($this->replaceEntitiesWithIds($terms_merged));

    $actual_terms = $helper::staticTerms();
    $this->assertSame(9, count(array_intersect($this->replaceEntitiesWithIds($actual_terms), $ids)));

    $helper->reset();

    $actual_terms = $helper::staticTerms(NULL, 10);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_terms)), array_merge($ids, [$ids[0]]));

    $helper->reset();

    $actual_terms = $helper::staticTerms($this->vids[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_terms)), array_merge([$ids[0]]));
    $actual_terms = $helper::staticTerms($this->vids[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_terms)), array_merge([$ids[1]]));
    $actual_terms = $helper::staticTerms($this->vids[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_terms)), array_merge([$ids[2]]));
    $actual_terms = $helper::staticTerms($this->vids[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_terms)), array_merge([$ids[0]]));

    $helper->reset();

    $actual_terms = $helper::staticTerms($this->vids[0], 4);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_terms)), array_merge([
      $ids[0],
      $ids[1],
      $ids[2],
      $ids[0],
    ]));

    $actual_terms = $helper::staticTerms($this->vids[1], 4);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_terms)), array_merge([
      $ids[3],
      $ids[4],
      $ids[5],
      $ids[3],
    ]));

    $actual_terms = $helper::staticTerms(NULL, 10);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_terms)), array_merge($ids, [$ids[0]]));
  }

}
