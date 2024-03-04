<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Traits;

use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Trait GeneratedContentTestUserTrait.
 *
 * Trait with user-related helpers.
 *
 * @package Drupal\generated_content\Tests
 */
trait GeneratedContentTestUserTrait {

  use UserCreationTrait;

  /**
   * Test setup for user.
   */
  public function userSetUp(): void {
    $this->installEntitySchema('user');
  }

  /**
   * Prepare users to be used in tests.
   *
   * @param int $count
   *   The number of users.
   *
   * @return \Drupal\user\Entity\User[]
   *   The users.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function prepareUsers(int $count): array {
    $users = [];

    for ($i = 0; $i < $count; $i++) {
      $user = $this->createUser(['access content']);
      if ($user) {
        $users[$user->id()] = $user;
      }
    }

    return $users;
  }

}
