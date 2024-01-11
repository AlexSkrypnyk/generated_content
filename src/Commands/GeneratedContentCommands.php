<?php

declare(strict_types = 1);

namespace Drupal\generated_content\Commands;

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
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private LoggerChannelFactoryInterface $loggerChannelFactory;

  /**
   * Constructs a new UpdateVideosStatsController object.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   */
  public function __construct(LoggerChannelFactoryInterface $loggerChannelFactory) {
    parent::__construct();
    $this->loggerChannelFactory = $loggerChannelFactory;
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
    $this->loggerChannelFactory->get('generated_content')->info($this->t('Generate content operations started.'));

    $batchBuilder = new BatchBuilder();
    $batch_id = 1;

    for ($count = 0; $count < $total;) {
      $count += 50;
      $batchBuilder->addOperation('\Drupal\generated_content\GeneratedContentBatchService::processItem', [
        $batch_id,
        $entity_type,
        $bundle,
        $total,
        $count,
      ]);
      $batch_id++;
    }

    $batchBuilder
      ->setTitle($this->t('Creating generated content for @entity_type @bundle (@total items in @batches batches)', [
        '@entity_type' => $entity_type,
        '@bundle' => $bundle,
        '@total' => $total,
        '@batches' => $batch_id,
      ]))
      ->setFinishCallback('\Drupal\generated_content\GeneratedContentBatchService::processItemFinished')
      ->setErrorMessage($this->t('Batch has encountered an error'));

    batch_set($batchBuilder->toArray());
    drush_backend_batch_process();

    $this->loggerChannelFactory->get('generated_content')->info($this->t('Batch operations finished.'));
  }

}
