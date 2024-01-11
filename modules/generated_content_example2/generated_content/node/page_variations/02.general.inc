<?php

/**
 * @file
 * Variations for Page nodes.
 */

declare(strict_types = 1);

use Drupal\generated_content_example2\GeneratedContentExample2Helper;

/**
 * Example of general page properties variations.
 *
 * @return array<mixed>
 *   Variations.
 */
function _generated_content_example2_create_node_page__variations__general(): array {
  /** @var \Drupal\generated_content_example2\GeneratedContentExample2Helper $helper */
  $helper = GeneratedContentExample2Helper::getInstance();

  $variations = [
    [
      'title' => 'Demo Page, default values',
    ],
    [
      'title' => 'Demo Page, Body',
      'body' => $helper::staticPlainParagraph(),
    ],
    [
      'title' => 'Demo Page, Body, Unpublished',
      'body' => $helper::staticPlainParagraph(),
      'status' => FALSE,
    ],
  ];

  return $variations;
}
