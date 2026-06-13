<?php

declare(strict_types=1);

namespace Drupal\generated_content\Commands;

use Drupal\generated_content\GeneratedContentBatchService;
use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drush\Commands\DrushCommands;

/**
 * A Drush command file for generated_content module.
 */
class GeneratedContentCommands extends DrushCommands {

  use StringTranslationTrait;

  /**
   * Constructs a new UpdateVideosStatsController object.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   */
  public function __construct(
    /**
     * Logger service.
     */
    private LoggerChannelFactoryInterface $loggerChannelFactory,
  ) {
    parent::__construct();
  }

  /**
   * Create generated content.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Entity bundle.
   * @param int $total
   *   Number of items to create.
   *
   * @command generated-content:create-content
   *
   * @usage drush generated-content:create-content entity_type bundle count
   */
  public function createContent(string $entity_type, string $bundle, int $total): void {
    $this->loggerChannelFactory->get('generated_content')->info('Generate content operations started.');

    $batch_builder = $this->buildBatch($entity_type, $bundle, $total);

    batch_set($batch_builder->toArray());
    drush_backend_batch_process();

    $this->loggerChannelFactory->get('generated_content')->info('Batch operations finished.');
  }

  /**
   * Build the BatchBuilder for a create-content run.
   *
   * Chunks the total into 50-item operations, each dispatched to
   * GeneratedContentBatchService::processItem. Extracted from
   * createContent() so it can be unit-tested without invoking the
   * global batch_set() / drush_backend_batch_process() functions.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Entity bundle.
   * @param int $total
   *   Number of items to create.
   *
   * @return \Drupal\Core\Batch\BatchBuilder
   *   Configured batch builder.
   */
  protected function buildBatch(string $entity_type, string $bundle, int $total): BatchBuilder {
    $batch_builder = new BatchBuilder();
    $batch_id = 1;

    for ($count = 0; $count < $total;) {
      $count += 50;
      $batch_builder->addOperation(GeneratedContentBatchService::class . '::processItem', [
        $batch_id,
        $entity_type,
        $bundle,
        $total,
        $count,
      ]);
      $batch_id++;
    }

    return $batch_builder
      ->setTitle($this->t('Creating generated content for @entity_type @bundle (@total items in @batches batches)', [
        '@entity_type' => $entity_type,
        '@bundle' => $bundle,
        '@total' => $total,
        '@batches' => $batch_id - 1,
      ]))
      ->setFinishCallback(GeneratedContentBatchService::class . '::processItemFinished')
      ->setErrorMessage($this->t('Batch has encountered an error'));
  }

}
