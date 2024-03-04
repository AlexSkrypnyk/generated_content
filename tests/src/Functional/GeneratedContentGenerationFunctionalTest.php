<?php

declare(strict_types=1);

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
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Behat\Mink\Exception\ResponseTextException
   */
  public function testGenerateDelete(): void {
    $admin = $this->createUser([], NULL, TRUE);
    $this->drupalLogin($admin);

    $this->drupalGet('/admin/config/development/generated-content');

    $this->assertInfoTableItems(0, 0, 0, 0, 0, 0, 0);

    $edit = [
      'table[user__user]' => TRUE,
      'table[file__file]' => TRUE,
      'table[media__image]' => TRUE,
      'table[media__document]' => TRUE,
      'table[taxonomy_term__tags]' => TRUE,
      'table[node__page]' => TRUE,
      'table[node__article]' => TRUE,
    ];
    $this->submitForm($edit, 'Generate');
    $this->assertInfoTableItems(0, 70, 10, 10, 10, 3, 10);

    $this->assertSession()->pageTextContains('Created an account generated_content_editor_1@example.com');
    $this->assertSession()->pageTextContains('Created an account generated_content_editor_2@example.com');
    $this->assertSession()->pageTextContains('Created an account generated_content_editor_3@example.com');
    $this->assertSession()->pageTextContains('Created generated content entities "user" with bundle "user"');

    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_1');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_2');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_3');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_4');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_5');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_6');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_7');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_8');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_9');
    $this->assertSession()->pageTextContains('Created file "Demo_static_jpg_file_10');

    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_1');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_2');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_3');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_4');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_5');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_6');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_7');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_8');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_9');
    $this->assertSession()->pageTextContains('Created file "Demo_static_png_file_10');

    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_1');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_2');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_3');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_4');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_5');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_6');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_7');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_8');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_9');
    $this->assertSession()->pageTextContains('Created file "Demo_static_pdf_file_10');

    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_1');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_2');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_3');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_4');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_5');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_6');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_7');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_8');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_9');
    $this->assertSession()->pageTextContains('Created file "Demo_static_docx_file_10');

    $this->assertSession()->pageTextContains('Created media Image "Demo static Image media 1');
    $this->assertSession()->pageTextContains('Created media Image "Demo random Image media 2');
    $this->assertSession()->pageTextContains('Created media Image "Demo static Image media 3');
    $this->assertSession()->pageTextContains('Created media Image "Demo random Image media 4');
    $this->assertSession()->pageTextContains('Created media Image "Demo static Image media 5');
    $this->assertSession()->pageTextContains('Created media Image "Demo random Image media 6');
    $this->assertSession()->pageTextContains('Created media Image "Demo static Image media 7');
    $this->assertSession()->pageTextContains('Created media Image "Demo random Image media 8');
    $this->assertSession()->pageTextContains('Created media Image "Demo static Image media 9');
    $this->assertSession()->pageTextContains('Created media Image "Demo random Image media 10');
    $this->assertSession()->pageTextContains('Created generated content entities "media" with bundle "image"');

    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 1');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 2');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 3');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 4');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 5');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 6');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 7');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 8');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 9');
    $this->assertSession()->pageTextContains('Created media Document "Demo random Document media 10 ');
    $this->assertSession()->pageTextContains('Created generated content entities "media" with bundle "document"');

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

    $this->assertSession()->pageTextContains('Created "article" node "Generated article');
    $this->assertSession()->pageTextContains('Created generated content entities "node" with bundle "article"');

    $edit = [
      'table[user__user]' => TRUE,
      'table[file__file]' => TRUE,
      'table[media__image]' => TRUE,
      'table[media__document]' => TRUE,
      'table[taxonomy_term__tags]' => TRUE,
      'table[node__page]' => TRUE,
      'table[node__article]' => TRUE,
    ];
    $this->submitForm($edit, 'Delete');
    $this->assertInfoTableItems(0, 0, 0, 0, 0, 0, 0);

    $this->assertSession()->pageTextContains('Removed all generated content entities "user" in bundle "user"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "file" in bundle "file"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "media" in bundle "image"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "media" in bundle "document"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "taxonomy_term" in bundle "tags"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "node" in bundle "page"');
    $this->assertSession()->pageTextContains('Removed all generated content entities "node" in bundle "article"');
    $this->assertSession()->pageTextContains('7 items processed.');
  }

}
