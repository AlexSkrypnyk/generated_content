<?php

declare(strict_types=1);

namespace Drupal\generated_content\Plugin\GeneratedContent;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\generated_content\Attribute\GeneratedContent;

/**
 * Plugin manager for GeneratedContent plugins.
 */
class GeneratedContentPluginManager extends DefaultPluginManager {

  /**
   * Constructs a new GeneratedContentPluginManager.
   *
   * @phpstan-ignore missingType.iterableValue
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/GeneratedContent',
      $namespaces,
      $module_handler,
      GeneratedContentPluginInterface::class,
      GeneratedContent::class,
    );
    $this->alterInfo('generated_content_plugin');
    $this->setCacheBackend($cache_backend, 'generated_content_plugins');
  }

}
