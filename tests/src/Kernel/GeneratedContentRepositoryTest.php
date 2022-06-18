<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\GeneratedContentRepository;

/**
 * Tests repository singleton.
 *
 * @group generated_content
 */
class GeneratedContentRepositoryTest extends GeneratedContentKernelTestBase {

  /**
   * Test singleton instance.
   */
  public function testSingleton() {
    $repository1 = GeneratedContentRepository::getInstance();
    $repository2 = GeneratedContentRepository::getInstance();
    $repository3 = $repository1;

    $this->assertSame($repository1, $repository2);
    $this->assertSame($repository1, $repository3);

    $repository1->reset();
    // Getting an instance after a reset renews the singleton instance.
    $repository3 = GeneratedContentRepository::getInstance();
    $this->assertSame($repository1, $repository2);
    $this->assertNotSame($repository1, $repository3);
  }

  /**
   * Test for addEntities() and getEntities() methods for tracked entities.
   */
  public function testAddEntitiesGetEntitiesTracked() {
    $repository = GeneratedContentRepository::getInstance();

    $nodes = $this->prepareNodes(5, NULL, TRUE);
    $users = $this->prepareUsers(5);
    $repository->addEntities($nodes);
    $repository->addEntities($users);

    $expected = [
      'node' => [
        $this->nodeTypes[0] => $this->replaceEntitiesWithIds($nodes),
      ],
      'user' => [
        'user' => $this->replaceEntitiesWithIds($users),
      ],
    ];

    $actual_entities = $this->replaceEntitiesWithIds($repository->getEntities());
    $this->assertSame($expected, $actual_entities);

    $actual_entities = $this->replaceEntitiesWithIds($repository->getEntities('node'));
    $this->assertSame($expected['node'], $actual_entities);
    $actual_entities = $this->replaceEntitiesWithIds($repository->getEntities('user'));
    $this->assertSame($expected['user'], $actual_entities);

    $actual_entities = $this->replaceEntitiesWithIds($repository->getEntities('node', $this->nodeTypes[0]));
    $this->assertSame($expected['node'][$this->nodeTypes[0]], $actual_entities);

    // Negative tests.
    $actual_entities = $repository->getEntities($this->randomString());
    $this->assertSame([], $actual_entities);

    $actual_entities = $repository->getEntities('node', $this->randomString());
    $this->assertSame([], $actual_entities);

    // Cache reset.
    $actual_entities = $this->replaceEntitiesWithIds($repository->getEntities(NULL, NULL, TRUE));
    $this->assertSame($expected, $actual_entities);
  }

  /**
   * Test for addEntities() and getEntities() methods for not tracked entities.
   */
  public function testAddEntitiesGetEntitiesNotTracked() {
    $repository = GeneratedContentRepository::getInstance();

    $nodes = $this->prepareNodes(5, NULL, TRUE);
    $users = $this->prepareUsers(5);
    $nodes_tracked = array_slice($nodes, 0, 2, TRUE);
    $nodes_not_tracked = array_slice($nodes, 2, 3, TRUE);

    $repository->addEntities($nodes_tracked);
    $repository->addEntities($nodes_not_tracked, FALSE);
    $repository->addEntities($users);

    $expected_all = [
      'node' => [
        $this->nodeTypes[0] => $this->replaceEntitiesWithIds($nodes),
      ],
      'user' => [
        'user' => $this->replaceEntitiesWithIds($users),
      ],
    ];

    $expected_tracked = [
      'node' => [
        $this->nodeTypes[0] => $this->replaceEntitiesWithIds($nodes_tracked),
      ],
      'user' => [
        'user' => $this->replaceEntitiesWithIds($users),
      ],
    ];

    // Non-tracked items are still stored in internal cache.
    $actual_entities = $repository->getEntities();
    $actual_entities = $this->replaceEntitiesWithIds($actual_entities);
    $this->assertSame($expected_all, $actual_entities);

    // Reload repository from the DB. This should refresh internal cache.
    $actual_entities = $repository->getEntities(NULL, NULL, TRUE);
    $actual_entities = $this->replaceEntitiesWithIds($actual_entities);
    $this->assertSame($expected_tracked, $actual_entities);

    $actual_entities = $repository->getEntities('node');
    $actual_entities = $this->replaceEntitiesWithIds($actual_entities);
    $this->assertSame($expected_tracked['node'], $actual_entities);

    $actual_entities = $repository->getEntities('user');
    $actual_entities = $this->replaceEntitiesWithIds($actual_entities);
    $this->assertSame($expected_tracked['user'], $actual_entities);

    $actual_entities = $repository->getEntities('node', $this->nodeTypes[0]);
    $actual_entities = $this->replaceEntitiesWithIds($actual_entities);
    $this->assertSame($expected_tracked['node'][$this->nodeTypes[0]], $actual_entities);

    // Negative tests.
    $actual_entities = $repository->getEntities($this->randomString());
    $actual_entities = $this->replaceEntitiesWithIds($actual_entities);
    $this->assertSame([], $actual_entities);

    $actual_entities = $repository->getEntities('node', $this->randomString());
    $actual_entities = $this->replaceEntitiesWithIds($actual_entities);
    $this->assertSame([], $actual_entities);
  }

}
