<?php

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
  public function userSetUp() {
    $this->installEntitySchema('user');
  }

  /**
   * Prepare users to be used in tests.
   */
  protected function prepareUsers($count) {
    $users = [];

    for ($i = 0; $i < $count; $i++) {
      $user = $this->createUser(['access content']);
      $users[$user->id()] = $user;
    }

    return $users;
  }

}
