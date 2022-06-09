<?php

/**
 * @file
 * Create generated users.
 *
 * Note that hook implementation for weight is not provided and a default
 * is used instead.
 */

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example1_generated_content_create_user_user_tracking() {
  return FALSE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example1_generated_content_create_user_user() {
  $total_users_per_role = 3;

  /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
  $helper = GeneratedContentHelper::getInstance();

  $users = [];

  $roles = user_roles();
  foreach ($roles as $role) {
    if (in_array($role->id(), [
      'anonymous', 'authenticated', 'administrator',
    ])) {
      continue;
    }

    for ($i = 1; $i <= $total_users_per_role; $i++) {
      $name = sprintf('generated_%s_%s@example.com', $role->id(), $i);

      $existing_user = user_load_by_name($name);
      if ($existing_user) {
        \Drupal::entityTypeManager()->getStorage('user')->load($existing_user->id())->delete();
      }

      if (Role::load($role->id())) {
        $user = User::create();
        $user->setUsername($name);
        $user->setEmail($name);
        $user->addRole($role->id());
        $user->activate();
        $user->enforceIsNew();
        try {
          $user->save();
          $users[] = $user;

          $helper::log(
            'Created an account %s [ID: %s] %s',
            Link::createFromRoute($user->getDisplayName(), 'entity.user.canonical', ['user' => $user->id()])->toString(),
            $user->id(),
            Link::createFromRoute('Edit', 'entity.user.edit_form', ['user' => $user->id()])->toString(),
          );
        }
        catch (\Exception $exception) {
          watchdog_exception('generated_content_example1', $exception);
        }
      }
    }
  }

  return $users;
}
