<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestMockTrait;

/**
 * Class GeneratedContentFunctionalTestBase.
 *
 * Base class for functional tests.
 */
abstract class GeneratedContentFunctionalTestBase extends BrowserTestBase {

  use GeneratedContentTestMockTrait;

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Assert table items are present with values.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   * @throws \Behat\Mink\Exception\ElementTextException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  protected function assertInfoTableItems(?int $c1, ?int $c2, ?int $c3, ?int $c4, ?int $c5, ?int $c6, ?int $c7): void {
    $this->assertSession()->responseContains('Generate content');

    $row_idx = 1;
    if (!is_null($c1)) {
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%d]/td[2]', $row_idx), 'user');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%d]/td[3]', $row_idx), 'user');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%d]/td[4]', $row_idx), '-100');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%d]/td[5]', $row_idx), 'Disabled');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%d]/td[6]', $row_idx), 'generated_content_example1');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%d]/td[7]', $row_idx), (string) $c1);
      $row_idx++;
    }

    if (!is_null($c2)) {
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[2]', $row_idx), 'file');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[3]', $row_idx), 'file');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[4]', $row_idx), '-10');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[5]', $row_idx), 'Enabled');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[6]', $row_idx), 'generated_content_example1');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[7]', $row_idx), (string) $c2);
      $row_idx++;
    }

    if (!is_null($c3)) {
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[2]', $row_idx), 'media');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[3]', $row_idx), 'image');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[4]', $row_idx), '1');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[5]', $row_idx), 'Enabled');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[6]', $row_idx), 'generated_content_example1');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[7]', $row_idx), (string) $c3);
      $row_idx++;
    }

    if (!is_null($c4)) {
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[2]', $row_idx), 'media');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[3]', $row_idx), 'document');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[4]', $row_idx), '2');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[5]', $row_idx), 'Enabled');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[6]', $row_idx), 'generated_content_example2');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[7]', $row_idx), (string) $c4);
      $row_idx++;
    }

    if (!is_null($c5)) {
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[2]', $row_idx), 'taxonomy_term');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[3]', $row_idx), 'tags');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[4]', $row_idx), '12');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[5]', $row_idx), 'Enabled');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[6]', $row_idx), 'generated_content_example2');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[7]', $row_idx), (string) $c5);
      $row_idx++;
    }

    if (!is_null($c6)) {
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[2]', $row_idx), 'node');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[3]', $row_idx), 'page');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[4]', $row_idx), '35');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[5]', $row_idx), 'Enabled');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[6]', $row_idx), 'generated_content_example2');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[7]', $row_idx), (string) $c6);
      $row_idx++;
    }

    if (!is_null($c7)) {
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[2]', $row_idx), 'node');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[3]', $row_idx), 'article');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[4]', $row_idx), '36');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[5]', $row_idx), 'Enabled');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[6]', $row_idx), 'generated_content_example2');
      $this->assertSession()->elementTextContains('xpath', sprintf('//table/tbody/tr[%s]/td[7]', $row_idx), (string) $c7);
    }
  }

}
