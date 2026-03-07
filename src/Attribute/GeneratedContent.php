<?php

declare(strict_types=1);

namespace Drupal\generated_content\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a GeneratedContent plugin attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class GeneratedContent extends Plugin {

  public function __construct(
    string $id,
    public readonly string $entity_type,
    public readonly string $bundle,
    public readonly int $weight = 0,
    public readonly bool $tracking = TRUE,
    public readonly ?TranslatableMarkup $label = NULL,
    public readonly ?string $helper = NULL,
  ) {
    parent::__construct($id);
  }

}
