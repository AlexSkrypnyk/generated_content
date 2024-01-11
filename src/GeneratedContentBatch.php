<?php

declare(strict_types = 1);

namespace Drupal\generated_content;

/**
 * Class GeneratedContentBatch.
 *
 * Batch processing for generated content items.
 *
 * @package Drupal\generated_content
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class GeneratedContentBatch {

  /**
   * Process creation of all entities.
   *
   * @param string $op
   *   Operation.
   * @param array<mixed> $info_items
   *   Info items.
   * @param int $total
   *   Total.
   */
  public static function set(string $op, array $info_items, int $total): void {
    $batch = [
      'title' => t('Processing generated content'),
      'operations' => [],
      'finished' => '\Drupal\generated_content\GeneratedContentBatch::finished',
    ];

    foreach ($info_items as $info_item) {
      $batch['operations'][] = [
        $op == 'create' ? '\Drupal\generated_content\GeneratedContentBatch::createSingle' : '\Drupal\generated_content\GeneratedContentBatch::removeSingle',
        [$info_item, $total],
      ];
    }
    batch_set($batch);
  }

  /**
   * Create single item batch callback.
   *
   * @param array<mixed> $info_item
   *   Info item.
   * @param int $total
   *   Total.
   * @param array<mixed> $context
   *   Context.
   */
  public static function createSingle(array $info_item, int $total, array &$context): void {
    if (!isset($context['sandbox']['count'])) {
      $context['sandbox']['count'] = 0;
      $context['results']['count'] = 0;
    }

    $repository = GeneratedContentRepository::getInstance();
    $repository->createSingle($info_item);

    $context['sandbox']['count'] += 1;
    $context['results']['count'] += 1;
    $context['finished'] = $context['sandbox']['count'] / $total;

    $context['message'] = t('Creating @entity_type @bundle items', [
      '@entity_type' => $info_item['entity_type'],
      '@bundle' => $info_item['bundle'],
    ]);
  }

  /**
   * Remove single item batch callback.
   *
   * @param array<mixed> $info_item
   *   Info item.
   * @param int $total
   *   Total.
   * @param array<mixed> $context
   *   Context.
   */
  public static function removeSingle(array $info_item, int $total, array &$context): void {
    if (!isset($context['sandbox']['count'])) {
      $context['sandbox']['count'] = 0;
      $context['results']['count'] = 0;
    }

    $repository = GeneratedContentRepository::getInstance();
    $repository->removeSingle($info_item);

    $context['sandbox']['count'] += 1;
    $context['results']['count'] += 1;
    $context['finished'] = $context['sandbox']['count'] / $total;

    $context['message'] = t('Deleting @entity_type @bundle items', [
      '@entity_type' => $info_item['entity_type'],
      '@bundle' => $info_item['bundle'],
    ]);
  }

  /**
   * Finish batch callback.
   *
   * @param bool $success
   *   Success or not.
   * @param array<mixed> $results
   *   Results.
   * @param array<mixed> $operations
   *   Operations.
   */
  public static function finished(bool $success, array $results, array $operations): void {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    $message = t('Finished with an error.');
    if ($success) {
      $repository = GeneratedContentRepository::getInstance();
      $repository->clearCaches();

      $message = \Drupal::translation()->formatPlural(
        $results['count'],
        'One item processed.', '@count items processed.'
      );
    }

    \Drupal::messenger()->addMessage($message);
  }

}
