<?php

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
   */
  public static function randomTags($count = NULL) {
    return static::randomTerms('tags', $count);
  }

}
