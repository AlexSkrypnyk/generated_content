<?php

namespace Drupal\generated_content\Plugin\ConfigFilter;

use Drupal\config_ignore\Plugin\ConfigFilter\IgnoreFilter;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Ignore all generated_content config.
 *
 * @ConfigFilter(
 *   id = "generated_content_config_ignore",
 *   label = "Generated Content Config Ignore",
 *   weight = 100
 * )
 */
class GeneratedContentIgnoreFilter extends IgnoreFilter implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function filterWrite($name, array $data) {
    if ($name === 'core.extension') {
      $excluded_modules = ['generated_content' => 'generated_content'];
      $data['module'] = array_diff_key($data['module'], $excluded_modules);
    }

    return $data;
  }

}
