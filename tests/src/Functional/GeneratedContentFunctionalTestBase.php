<?php

namespace Drupal\Tests\generated_content\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestHelperTrait;

/**
 * Class GeneratedContentFunctionalTestBase.
 *
 * Base class for functional tests.
 */
abstract class GeneratedContentFunctionalTestBase extends BrowserTestBase {

  use GeneratedContentTestHelperTrait;

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

}
