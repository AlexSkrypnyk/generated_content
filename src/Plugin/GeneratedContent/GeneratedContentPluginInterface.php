<?php

declare(strict_types=1);

namespace Drupal\generated_content\Plugin\GeneratedContent;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Interface for GeneratedContent plugins.
 */
interface GeneratedContentPluginInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Create generated content entities.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of created entities.
   */
  public function generate(): array;

  /**
   * Get the entity type.
   */
  public function getEntityType(): string;

  /**
   * Get the bundle.
   */
  public function getBundle(): string;

  /**
   * Get the weight.
   */
  public function getWeight(): int;

  /**
   * Get the tracking flag.
   */
  public function getTracking(): bool;

}
