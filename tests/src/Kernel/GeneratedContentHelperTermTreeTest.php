<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Group;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\TermInterface;

/**
 * Tests saveTermTree() and getTermsAtDepth() helpers.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\Helpers\GeneratedContentHelper::saveTermTree
 * @covers \Drupal\generated_content\Helpers\GeneratedContentHelper::getTermsAtDepth
 */
#[Group('generated_content')]
#[RunTestsInSeparateProcesses]
class GeneratedContentHelperTermTreeTest extends GeneratedContentKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'file',
    'text',
    'taxonomy',
    'generated_content',
  ];

  /**
   * Vocabulary used by the tree tests.
   */
  protected string $vid = 'test_tree';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('taxonomy_term');
    Vocabulary::create(['vid' => $this->vid, 'name' => 'Test Tree'])->save();

    // saveTermTree() / getTermsAtDepth() use the static entityTypeManager
    // property on the Helper - getInstance() is what populates it.
    GeneratedContentHelper::getInstance();
  }

  /**
   * Tests saveTermTree() with a flat list of terms.
   */
  public function testSaveTermTreeFlat(): void {
    $tree = [
      'Apples',
      'Bananas',
      'Cherries',
    ];

    $terms = GeneratedContentHelper::saveTermTree($this->vid, $tree);

    $this->assertCount(3, $terms);
    foreach ($terms as $term) {
      $this->assertInstanceOf(TermInterface::class, $term);
      $this->assertSame($this->vid, $term->bundle());
    }
  }

  /**
   * Tests saveTermTree() builds a nested hierarchy.
   */
  public function testSaveTermTreeNested(): void {
    $tree = [
      'Fruit' => [
        'Apples',
        'Bananas',
      ],
      'Vegetables' => [
        'Carrots',
      ],
    ];

    $terms = GeneratedContentHelper::saveTermTree($this->vid, $tree);

    // Two parents + three children.
    $this->assertCount(5, $terms);

    // getTermsAtDepth uses loadTree under the hood.
    $depth_zero = GeneratedContentHelper::getTermsAtDepth($this->vid, 0);
    $depth_one = GeneratedContentHelper::getTermsAtDepth($this->vid, 1);

    $this->assertCount(2, $depth_zero);
    $this->assertCount(3, $depth_one);
  }

  /**
   * Tests getTermsAtDepth() clamps negative depth to zero.
   */
  public function testGetTermsAtDepthNegative(): void {
    GeneratedContentHelper::saveTermTree($this->vid, ['A', 'B']);

    $terms = GeneratedContentHelper::getTermsAtDepth($this->vid, -5);

    // -5 is treated as 0 -> root-level terms.
    $this->assertCount(2, $terms);
  }

  /**
   * Tests getTermsAtDepth() with load_entities=TRUE returns Term entities.
   */
  public function testGetTermsAtDepthLoaded(): void {
    GeneratedContentHelper::saveTermTree($this->vid, ['Loaded']);

    $terms = GeneratedContentHelper::getTermsAtDepth($this->vid, 0, TRUE);

    $this->assertCount(1, $terms);
    foreach ($terms as $term) {
      $this->assertInstanceOf(TermInterface::class, $term);
    }
  }

}
