<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Traits;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Trait GeneratedContentTestFieldTrait.php.
 *
 * Trait with field-related helpers.
 *
 * @package Drupal\generated_content\Tests
 */
trait GeneratedContentTestFieldTrait {

  /**
   * Create an allowed values list field storage, instance and display.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Bundle.
   * @param string $field_name
   *   Field name.
   * @param array<string|int> $allowed_values
   *   Allow values.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function fieldCreateListAllowedValues(string $entity_type, string $bundle, string $field_name, array $allowed_values): void {
    /** @var \Drupal\field\Entity\FieldStorageConfig $field_storage_config */
    $field_storage_config = FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => $entity_type,
      'type' => 'list_string',
      'settings' => [
        'allowed_values' => $allowed_values,
      ],
    ]);
    $field_storage_config->save();

    $field = [
      'field_name' => $field_name,
      'entity_type' => $entity_type,
      'bundle' => $bundle,
      'required' => TRUE,
    ];
    FieldConfig::create($field)->save();

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getFormDisplay($field['entity_type'], $field['bundle'])
      ->setComponent($field['field_name'], [
        'type' => 'options_select',
      ])
      ->save();
  }

}
