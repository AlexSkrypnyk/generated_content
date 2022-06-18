<?php

/**
 * @file
 * Variations for Page nodes.
 */

use Drupal\generated_content_example2\GeneratedContentExample2Helper;

/**
 * Example of general page properties variations.
 */
function _generated_content_example2_create_node_page__variations__general() {
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
