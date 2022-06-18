<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestMockTrait;

/**
 * Tests user* helpers in GeneratedContentHelper class.
 *
 * @group generated_content
 */
class GeneratedContentHelperUserTest extends GeneratedContentKernelTestBase {

  use GeneratedContentTestMockTrait;

  /**
   * Tests the randomUser() method.
   */
  public function testRandomUser() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_user = $helper::randomUser();
    $this->assertNULL($actual_user);

    $users = $this->prepareUsers(5);

    // Assert that once user is added to the repository - it is returned.
    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($users);
    $actual_user = $helper::randomUser();
    $this->assertTrue(in_array($actual_user->id(), $this->replaceEntitiesWithIds($users)));
  }

  /**
   * Tests the randomUsers() method.
   */
  public function testRandomUsers() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_users = $helper::randomUsers();
    $this->assertSame([], $actual_users);

    $users = $this->prepareUsers(5);

    // Assert that once users are added to the repository - they are returned.
    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($users);

    // All added users.
    $actual_users = $helper::randomUsers();
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_users), $this->replaceEntitiesWithIds($users))));

    // Only 2 of added users.
    $actual_users = $helper::randomUsers(2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_users), $this->replaceEntitiesWithIds($users))));
  }

  /**
   * Tests the randomRealUser() method.
   */
  public function testRandomRealUser() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_user = $helper::randomRealUser();
    $this->assertNull($actual_user);

    $users = $this->prepareUsers(5);

    $users_in_repository = array_slice($users, 0, 2, TRUE);
    $users_not_in_repository = array_slice($users, 2, 3, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($users_in_repository);

    $this->assertTrue(in_array($helper::randomRealUser()->id(), $this->replaceEntitiesWithIds($users_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealUser()->id(), $this->replaceEntitiesWithIds($users_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealUser()->id(), $this->replaceEntitiesWithIds($users_not_in_repository)));
    $this->assertFalse(in_array($helper::randomRealUser()->id(), $this->replaceEntitiesWithIds($users_in_repository)));
    $this->assertFalse(in_array($helper::randomRealUser()->id(), $this->replaceEntitiesWithIds($users_in_repository)));
    $this->assertFalse(in_array($helper::randomRealUser()->id(), $this->replaceEntitiesWithIds($users_in_repository)));
  }

  /**
   * Tests the randomRealUsers() method.
   */
  public function testRandomRealUsers() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_users = $helper::randomRealUsers();
    $this->assertSame([], $actual_users);

    $users = $this->prepareUsers(5);

    $users_in_repository = array_slice($users, 0, 2, TRUE);
    $users_not_in_repository = array_slice($users, 2, 3, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($users_in_repository);

    // All added users.
    $actual_users = $helper::randomRealUsers();
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_users), $this->replaceEntitiesWithIds($users_not_in_repository))));
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_users), $this->replaceEntitiesWithIds($users_in_repository))));

    // Only 2 of added users.
    $actual_users = $helper::randomRealUsers(2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_users), $this->replaceEntitiesWithIds($users_not_in_repository))));
  }

  /**
   * Tests the staticUser() method.
   */
  public function testStaticUser() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_user = $helper::staticUser();
    $this->assertNull($actual_user);

    $users = $this->prepareUsers(5);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($users);

    $ids = $this->replaceEntitiesWithIds($users);
    $this->assertSame($helper::staticUser()->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticUser()->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticUser()->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticUser()->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticUser()->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticUser()->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticUser()->id(), array_values($ids)[1]);
  }

  /**
   * Tests the staticUsers() method.
   */
  public function testStaticUsers() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_users = $helper::staticUsers();
    $this->assertSame([], $actual_users);

    $users = $this->prepareUsers(5);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($users);

    $ids = array_values($this->replaceEntitiesWithIds($users));

    $actual_users = $helper::staticUsers();
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_users), $ids)));

    $actual_users = $helper::staticUsers(1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_users)), [$ids[0]]);
    $actual_users = $helper::staticUsers(1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_users)), [$ids[1]]);
    $actual_users = $helper::staticUsers(2);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_users)), [
      $ids[2],
      $ids[3],
    ]);

    $helper->reset();

    $actual_users = $helper::staticUsers(6);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_users)), [
      $ids[0],
      $ids[1],
      $ids[2],
      $ids[3],
      $ids[4],
      $ids[0],
    ]);

    $actual_users = $helper::staticUsers();
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_users)), [
      $ids[1],
      $ids[2],
      $ids[3],
      $ids[4],
      $ids[0],
    ]);
  }

}
