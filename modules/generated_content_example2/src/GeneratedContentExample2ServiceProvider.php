<?php

namespace Drupal\generated_content_example2;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Class GeneratedContentExample2ServiceProvider.
 *
 * Example of asset generator service replacement with an extending class.
 *
 * @package Drupal\generated_content_example2
 */
class GeneratedContentExample2ServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('generated_content.asset_generator');
    $definition->setClass('Drupal\generated_content_example2\GeneratedContentExample2AssetGenerator');
  }

}
