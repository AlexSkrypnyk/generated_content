<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestMockTrait;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestNodeTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests node* helpers in GeneratedContentHelper class.
 *
 * @group generated_content
 */
class GeneratedContentHelperNodeTest extends GeneratedContentKernelTestBase {

  use UserCreationTrait;
  use GeneratedContentTestMockTrait;
  use GeneratedContentTestNodeTrait;

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
   * Tests the randomNode() method.
   */
  public function testRandomNode() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_node = $helper::randomNode();
    $this->assertNULL($actual_node);

    $nodes = $this->prepareNodes(5);
    $nodes_merged = array_merge($nodes[$this->nodeTypes[0]], $nodes[$this->nodeTypes[1]], $nodes[$this->nodeTypes[2]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($nodes_merged);

    $actual_node = $helper::randomNode();
    $this->assertTrue(in_array($actual_node->id(), $this->replaceEntitiesWithIds($nodes_merged)));

    $actual_node = $helper::randomNode($this->nodeTypes[0]);
    $this->assertTrue(in_array($actual_node->id(), $this->replaceEntitiesWithIds($nodes_merged)));
    $actual_node = $helper::randomNode($this->nodeTypes[1]);
    $this->assertTrue(in_array($actual_node->id(), $this->replaceEntitiesWithIds($nodes_merged)));
    $actual_node = $helper::randomNode($this->nodeTypes[2]);
    $this->assertTrue(in_array($actual_node->id(), $this->replaceEntitiesWithIds($nodes_merged)));

    $actual_node = $helper::randomNode($this->randomString());
    $this->assertNull($actual_node);
  }

  /**
   * Tests the randomNodes() method.
   */
  public function testRandomNodes() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_nodes = $helper::randomNodes();
    $this->assertSame([], $actual_nodes);

    $nodes = $this->prepareNodes(5);
    $nodes_merged = array_merge($nodes[$this->nodeTypes[0]], $nodes[$this->nodeTypes[1]], $nodes[$this->nodeTypes[2]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($nodes_merged);

    $actual_nodes = $helper::randomNodes();
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes_merged))));

    $actual_nodes = $helper::randomNodes(NULL, 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes_merged))));

    $actual_nodes = $helper::randomNodes(NULL, 20);
    $this->assertSame(15, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes_merged))));

    $actual_nodes = $helper::randomNodes($this->nodeTypes[0]);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[0]]))));

    $actual_nodes = $helper::randomNodes($this->nodeTypes[0], 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[0]]))));

    $actual_nodes = $helper::randomNodes($this->nodeTypes[0], 20);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[0]]))));
  }

  /**
   * Tests the randomRealNode() method.
   */
  public function testRandomRealNode() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_node = $helper::randomRealNode();
    $this->assertNull($actual_node);

    $nodes = $this->prepareNodes(5);
    $nodes_merged = array_merge($nodes[$this->nodeTypes[0]], $nodes[$this->nodeTypes[1]], $nodes[$this->nodeTypes[2]]);

    $nodes_in_repository = array_slice($nodes_merged, 0, 7, TRUE);
    $nodes_not_in_repository = array_slice($nodes_merged, 7, 8, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($nodes_in_repository);

    $this->assertTrue(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_not_in_repository)));
    $this->assertFalse(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_in_repository)));
    $this->assertFalse(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_in_repository)));
    $this->assertFalse(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_in_repository)));
    $this->assertFalse(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_in_repository)));
    $this->assertFalse(in_array($helper::randomRealNode()->id(), $this->replaceEntitiesWithIds($nodes_in_repository)));
  }

  /**
   * Tests the randomRealNodes() method.
   */
  public function testRandomRealNodes() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_nodes = $helper::randomRealNodes();
    $this->assertSame([], $actual_nodes);

    $nodes = $this->prepareNodes(5);
    $nodes_merged = array_merge($nodes[$this->nodeTypes[0]], $nodes[$this->nodeTypes[1]], $nodes[$this->nodeTypes[2]]);

    $nodes_in_repository = array_slice($nodes_merged, 0, 7, TRUE);
    $nodes_not_in_repository = array_slice($nodes_merged, 7, 8, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($nodes_in_repository);

    $actual_nodes = $helper::randomRealNodes();
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes_not_in_repository))));
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes_in_repository))));

    $actual_nodes = $helper::randomRealNodes(NULL, 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes_not_in_repository))));

    $actual_nodes = $helper::randomRealNodes(NULL, 10);
    $this->assertSame(8, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes_not_in_repository))));

    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[0]);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[0]]))));
    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[1]);
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[1]]))));
    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[2]);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[2]]))));

    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[0], 2);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[0]]))));
    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[1], 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[1]]))));
    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[2], 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[2]]))));

    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[0], 20);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[0]]))));
    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[1], 20);
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[1]]))));
    $actual_nodes = $helper::randomRealNodes($this->nodeTypes[2], 20);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $this->replaceEntitiesWithIds($nodes[$this->nodeTypes[2]]))));
  }

  /**
   * Tests the staticNode() method.
   */
  public function testStaticNode() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_node = $helper::staticNode();
    $this->assertNull($actual_node);

    $nodes = $this->prepareNodes(3);
    $nodes_merged = array_merge($nodes[$this->nodeTypes[0]], $nodes[$this->nodeTypes[1]], $nodes[$this->nodeTypes[2]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($nodes_merged);

    $ids = $this->replaceEntitiesWithIds($nodes_merged);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[5]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[6]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[7]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[8]);
    $this->assertSame($helper::staticNode()->id(), array_values($ids)[0]);

    $this->assertSame($helper::staticNode($this->nodeTypes[0])->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticNode($this->nodeTypes[0])->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticNode($this->nodeTypes[0])->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticNode($this->nodeTypes[0])->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticNode($this->nodeTypes[1])->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticNode($this->nodeTypes[1])->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticNode($this->nodeTypes[1])->id(), array_values($ids)[5]);
    $this->assertSame($helper::staticNode($this->nodeTypes[1])->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticNode($this->nodeTypes[2])->id(), array_values($ids)[6]);
    $this->assertSame($helper::staticNode($this->nodeTypes[2])->id(), array_values($ids)[7]);
    $this->assertSame($helper::staticNode($this->nodeTypes[2])->id(), array_values($ids)[8]);
    $this->assertSame($helper::staticNode($this->nodeTypes[2])->id(), array_values($ids)[6]);
  }

  /**
   * Tests the staticNodes() method.
   */
  public function testStaticNodes() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_nodes = $helper::staticNodes();
    $this->assertSame([], $actual_nodes);

    $nodes = $this->prepareNodes(3);
    $nodes_merged = array_merge($nodes[$this->nodeTypes[0]], $nodes[$this->nodeTypes[1]], $nodes[$this->nodeTypes[2]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($nodes_merged);

    $ids = array_values($this->replaceEntitiesWithIds($nodes_merged));

    $actual_nodes = $helper::staticNodes();
    $this->assertSame(9, count(array_intersect($this->replaceEntitiesWithIds($actual_nodes), $ids)));

    $helper->reset();

    $actual_nodes = $helper::staticNodes(NULL, 10);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_nodes)), array_merge($ids, [$ids[0]]));

    $helper->reset();

    $actual_nodes = $helper::staticNodes($this->nodeTypes[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_nodes)), array_merge([$ids[0]]));
    $actual_nodes = $helper::staticNodes($this->nodeTypes[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_nodes)), array_merge([$ids[1]]));
    $actual_nodes = $helper::staticNodes($this->nodeTypes[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_nodes)), array_merge([$ids[2]]));
    $actual_nodes = $helper::staticNodes($this->nodeTypes[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_nodes)), array_merge([$ids[0]]));

    $helper->reset();

    $actual_nodes = $helper::staticNodes($this->nodeTypes[0], 4);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_nodes)), array_merge([
      $ids[0],
      $ids[1],
      $ids[2],
      $ids[0],
    ]));

    $actual_nodes = $helper::staticNodes($this->nodeTypes[1], 4);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_nodes)), array_merge([
      $ids[3],
      $ids[4],
      $ids[5],
      $ids[3],
    ]));

    $actual_nodes = $helper::staticNodes(NULL, 10);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_nodes)), array_merge($ids, [$ids[0]]));
  }

}
