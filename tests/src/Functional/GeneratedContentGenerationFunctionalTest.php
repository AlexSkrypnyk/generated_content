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
   * Test generation and deletion of content.
   */
  public function testGenerateDelete() {
    $admin = $this->createUser([], NULL, TRUE);
    $this->drupalLogin($admin);

    $this->drupalGet('/admin/config/development/generated-content');

    $this->assertInfoTableItems(0, 0, 0, 0, 0, 0);

    $edit = [
      'table[user__user]' => TRUE,
      'table[media__document]' => TRUE,
      'table[media__image]' => TRUE,
      'table[taxonomy_term__tags]' => TRUE,
      'table[node__page]' => TRUE,
      'table[node__article]' => TRUE,
    ];
    $this->submitForm($edit, 'Generate');
    $this->assertInfoTableItems(0, 10, 10, 10, 3, 10);

    $this->assertSession()->pageTextContains('Created an account generated_content_editor_1@example.com');
    $this->assertSession()->pageTextContains('Created an account generated_content_editor_2@example.com');
    $this->assertSession()->pageTextContains('Created an account generated_content_editor_3@example.com');
    $this->assertSession()->pageTextContains('Created generated content entities "user" with bundle "user"');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 1');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 2');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 3');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 4');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 5');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 6');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 7');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 8');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 9');
    $this->assertSession()->pageTextContains('Created media Document "Demo Document media 10 ');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 1');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 2');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 3');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 4');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 5');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 6');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 7');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 8');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 9');
    $this->assertSession()->pageTextContains('Created media Image "Demo Image media 10 ');
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
    $this->assertSession()->pageTextContains('Created "page" node "Demo Page, default values"');
    $this->assertSession()->pageTextContains('Created "page" node "Demo Page, Body"');
    $this->assertSession()->pageTextContains('Created "page" node "Demo Page, Body, Unpublished"');
    $this->assertSession()->pageTextContains('Created generated content entities "node" with bundle "page"');

    $edit = [
      'table[user__user]' => TRUE,
      'table[media__document]' => TRUE,
      'table[media__image]' => TRUE,
      'table[taxonomy_term__tags]' => TRUE,
      'table[node__page]' => TRUE,
      'table[node__article]' => TRUE,
    ];
    $this->submitForm($edit, 'Delete');
    $this->assertInfoTableItems(0, 0, 0, 0, 0, 0);

    $this->assertSession()->pageTextContains('Removed all generated content entities "user" in bundle "user"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "media" in bundle "document"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "media" in bundle "image"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "taxonomy_term" in bundle "tags"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "node" in bundle "page"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "node" in bundle "article"');
    $this->assertSession()->pageTextContains('6 items processed.');
  }

  /**
   * Assert table items are present with values.
   */
  protected function assertInfoTableItems($c1, $c2, $c3, $c4, $c5, $c6) {
    $this->assertSession()->responseContains('Generate content');

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[2]', 'user');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[3]', 'user');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[4]', -100);
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[5]', 'Disabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[6]', 'generated_content_example1');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[1]/td[7]', $c1);

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[2]', 'media');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[3]', 'document');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[4]', 0);
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[5]', 'Enabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[6]', 'generated_content_example1');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[2]/td[7]', $c2);

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[2]', 'media');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[3]', 'image');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[4]', 0);
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[5]', 'Enabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[6]', 'generated_content_example1');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[3]/td[7]', $c3);

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[2]', 'taxonomy_term');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[3]', 'tags');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[4]', 12);
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[5]', 'Enabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[6]', 'generated_content_example2');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[4]/td[7]', $c4);

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[5]/td[2]', 'node');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[5]/td[3]', 'page');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[5]/td[4]', 35);
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[5]/td[5]', 'Enabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[5]/td[6]', 'generated_content_example2');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[5]/td[7]', $c5);

    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[6]/td[2]', 'node');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[6]/td[3]', 'article');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[6]/td[4]', 36);
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[6]/td[5]', 'Enabled');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[6]/td[6]', 'generated_content_example2');
    $this->assertSession()->elementTextContains('xpath', '//table/tbody/tr[6]/td[7]', $c6);
  }

}
