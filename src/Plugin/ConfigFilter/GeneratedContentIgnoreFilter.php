<?php

declare(strict_types = 1);

namespace Drupal\generated_content\Plugin\ConfigFilter;

use Drupal\config_filter\Plugin\ConfigFilterBase;

/**
 * Ignore all generated_content config.
 *
 * @ConfigFilter(
 *   id = "generated_content_config_ignore",
 *   label = "Generated Content Config Ignore",
 *   weight = 100
 * )
 */
class GeneratedContentIgnoreFilter extends ConfigFilterBase {

  /**
   * {@inheritdoc}
   */
  public function filterWrite($name, array $data): array {
    if ($name === 'core.extension') {
      $excluded_modules = ['generated_content' => 'generated_content'];
      $data['module'] = array_diff_key($data['module'], $excluded_modules);
    }

    return $data;
  }

}
