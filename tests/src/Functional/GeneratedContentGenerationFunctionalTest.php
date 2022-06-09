<?php

namespace Drupal\Tests\generated_content\Functional;

/**
 * Class GeneratedContentGenerationFunctionalTest.
 *
 * Example test case class.
 *
 * @group generated_content
 */
class GeneratedContentGenerationFunctionalTest extends GeneratedContentFunctionalTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    'generated_content',
    'generated_content_example1',
    'generated_content_example2',
  ];

  /**
   * Test information form.
   */
  public function testInfoTable() {
    $admin = $this->createUser([], NULL, TRUE);
    $this->drupalLogin($admin);

    $this->drupalGet('/admin/config/development/generated-content');

    $this->assertTableItems(0, 0, 0, 0);
  }

  /**
   * Test generation and deletion of content.
   */
  public function testGenerateDelete() {
    $admin = $this->createUser([], NULL, TRUE);
    $this->drupalLogin($admin);

    $this->drupalGet('/admin/config/development/generated-content');

    $this->assertTableItems(0, 0, 0, 0);

    $edit = [
      'table[user__user]' => TRUE,
      'table[media__image]' => TRUE,
      'table[taxonomy_term__tags]' => TRUE,
      'table[node__page]' => TRUE,
    ];
    $this->submitForm($edit, 'Generate');
    $this->assertTableItems(0, 10, 10, 10);

    $this->assertSession()->pageTextContains('Created an account generated_content_editor_1@example.com');
    $this->assertSession()->pageTextContains('Created an account generated_content_editor_2@example.com');
    $this->assertSession()->pageTextContains('Created an account generated_content_editor_3@example.com');
    $this->assertSession()->pageTextContains('Created generated content entities "user" with bundle "user"');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 1');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 2');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 3');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 4');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 5');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 6');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 7');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 8');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 9');
    $this->assertSession()->pageTextContains('Created media image for file "Demo Image media 10 ');
    $this->assertSession()->pageTextContains('Created generated content entities "media" with bundle "image"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 1"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 2"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 3"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 4"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 5"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 6"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 7"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 8"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 9"');
    $this->assertSession()->pageTextContains('Created "tags" term "Generated term 10"');
    $this->assertSession()->pageTextContains('Created generated content entities "taxonomy_term" with bundle "tags"');
    $this->assertSession()->pageTextContains('Created "page" node "Generated page Sta: Y, Con: Y"');
    $this->assertSession()->pageTextContains('Created generated content entities "node" with bundle "page"');

    $edit = [
      'table[user__user]' => TRUE,
      'table[media__image]' => TRUE,
      'table[taxonomy_term__tags]' => TRUE,
      'table[node__page]' => TRUE,
    ];
    $this->submitForm($edit, 'Delete');
    $this->assertTableItems(0, 0, 0, 0);

    $this->assertSession()->pageTextContains('Removed all generated content entities "user" in bundle "user"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "media" in bundle "image"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "taxonomy_term" in bundle "tags"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "node" in bundle "page"');
    $this->assertSession()->pageTextContains('4 items processed.');
  }

  /**
   * Assert table items are present with values.
   */
  protected function assertTableItems($c1, $c2, $c3, $c4) {
    $this->assertSession()->responseContains('Generate content');

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[2]', 'user');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[3]', 'user');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[4]', '-100');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[5]', 'Disabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[6]', 'generated_content_example1');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[7]', $c1);

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[2]', 'media');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[3]', 'image');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[4]', '0');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[5]', 'Enabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[6]', 'generated_content_example1');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[7]', $c2);

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[2]', 'taxonomy_term');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[3]', 'tags');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[4]', '12');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[5]', 'Enabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[6]', 'generated_content_example2');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[7]', $c3);

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[2]', 'node');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[3]', 'page');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[4]', '35');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[5]', 'Enabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[6]', 'generated_content_example2');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[7]', $c4);
  }

}
