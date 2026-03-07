<?php

declare(strict_types=1);

namespace Drupal\generated_content_example1\Plugin\GeneratedContent;

use Drupal\Core\Link;
use Drupal\Core\Utility\Error;
use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User as UserEntity;
use Psr\Log\LogLevel;

/**
 * Generates user entities.
 */
#[GeneratedContent(id: 'example1_user_user', entity_type: 'user', bundle: 'user', weight: -100, tracking: FALSE)]
class User extends GeneratedContentPluginBase {

  /**
   * {@inheritdoc}
   */
  public function generate(): array {
    $total_users_per_role = 3;

    $users = [];

    $roles = Role::loadMultiple();
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
          $this->entityTypeManager->getStorage('user')->load($existing_user->id())->delete();
        }

        if ($role->id() && Role::load($role->id())) {
          $user = UserEntity::create();
          $user->setUsername($name);
          $user->setEmail($name);
          $user->addRole((string) $role->id());
          $user->activate();
          $user->enforceIsNew();
          try {
            $user->save();
            $users[] = $user;

            $this->helper::log(
              'Created an account %s [ID: %s] %s',
              Link::createFromRoute($user->getDisplayName(), 'entity.user.canonical', ['user' => $user->id()])->toString(),
              $user->id(),
              Link::createFromRoute('Edit', 'entity.user.edit_form', ['user' => $user->id()])->toString()
            );
          }
          catch (\Exception $exception) {
            // @phpstan-ignore-next-line
            $logger = \Drupal::logger('generated_content_example1');
            $logger->log(LogLevel::ERROR, Error::DEFAULT_ERROR_MESSAGE, Error::decodeException($exception));
          }
        }
      }
    }

    return $users;
  }

}
