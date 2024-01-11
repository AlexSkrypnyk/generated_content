<?php

declare(strict_types = 1);

namespace Drupal\generated_content;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BatchService.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class GeneratedContentBatchService implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * Drupal\Core\Messenger\MessengerInterface definition.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected MessengerInterface $messenger;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The core messenger service.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container): GeneratedContentBatchService {
    // @phpstan-ignore-next-line
    return new static(
      $container->get('messenger')
    );
  }

  /**
   * Batch process callback.
   *
   * @param int $batch_id
   *   Batch id.
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Bundle.
   * @param int $total
   *   Total processed.
   * @param int $current
   *   Current processed.
   * @param array<mixed> $context
   *   Context.
   */
  public function processItem(int $batch_id, string $entity_type, string $bundle, int $total, int $current, array &$context): void {
    $repository = GeneratedContentRepository::getInstance();
    $repository->createEntities([$entity_type => [$bundle => TRUE]]);

    $context['message'] = strtr('Running batch "@id" for @entity_type @bundle (@current of @total).', [
      '@id' => $batch_id,
      '@entity_type' => $entity_type,
      '@bundle' => $bundle,
      '@total' => $total,
      '@current' => $current,
    ]);
  }

  /**
   * Batch Finished callback.
   *
   * @param bool $success
   *   Success of the operation.
   * @param array<mixed> $results
   *   Array of results for post processing.
   * @param array<mixed> $operations
   *   Array of operations.
   */
  public function processItemFinished(bool $success, array $results, array $operations): void {
    if ($success) {
      $repository = GeneratedContentRepository::getInstance();
      $repository->clearCaches();
    }
  }

}
