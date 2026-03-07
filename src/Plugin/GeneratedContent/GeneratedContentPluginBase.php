<?php

declare(strict_types=1);

namespace Drupal\generated_content\Plugin\GeneratedContent;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for GeneratedContent plugins.
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class GeneratedContentPluginBase extends PluginBase implements GeneratedContentPluginInterface, ContainerFactoryPluginInterface {

  /**
   * The content helper.
   *
   * @var \Drupal\generated_content\Helpers\GeneratedContentHelper
   */
  protected GeneratedContentHelper $helper;

  /**
   * The content repository.
   *
   * @var \Drupal\generated_content\GeneratedContentRepository
   */
  protected GeneratedContentRepository $repository;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->repository = GeneratedContentRepository::getInstance();
    $this->helper = $this->resolveHelper();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    // @phpstan-ignore new.static
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType(): string {
    // @phpstan-ignore-next-line
    return $this->pluginDefinition['entity_type'];
  }

  /**
   * {@inheritdoc}
   */
  public function getBundle(): string {
    // @phpstan-ignore-next-line
    return $this->pluginDefinition['bundle'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(): int {
    // @phpstan-ignore-next-line
    return $this->pluginDefinition['weight'] ?? 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getTracking(): bool {
    // @phpstan-ignore-next-line
    return $this->pluginDefinition['tracking'] ?? TRUE;
  }

  /**
   * Resolve the helper instance from the plugin definition.
   */
  protected function resolveHelper(): GeneratedContentHelper {
    // @phpstan-ignore-next-line
    $helper_class = $this->pluginDefinition['helper'] ?? NULL;
    if ($helper_class && is_subclass_of($helper_class, GeneratedContentHelper::class)) {
      return $helper_class::getInstance();
    }

    return GeneratedContentHelper::getInstance();
  }

}
