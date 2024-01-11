<?php

declare(strict_types = 1);

namespace Drupal\generated_content_example2;

use Drupal\generated_content\Helpers\GeneratedContentHelper;

/**
 * Class GeneratedContentExample2Helper.
 *
 * Example of content generation helper class extension.
 *
 * @package \Drupal\generated_content_example2
 */
class GeneratedContentExample2Helper extends GeneratedContentHelper {

  /**
   * Random tags.
   *
   * @param int|null $count
   *   Number of term.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   Terms.
   */
  public static function randomTags(int $count = NULL): array {
    return static::randomTerms('tags', $count);
  }

}
