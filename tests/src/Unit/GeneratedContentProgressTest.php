<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Unit;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\generated_content\GeneratedContentProgress;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the GeneratedContentProgress service.
 *
 * @group generated_content
 */
#[Group('generated_content')]
class GeneratedContentProgressTest extends GeneratedContentUnitTestBase {

  /**
   * Test that a message reaches both the messenger and the logger channel.
   */
  public function testReport(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $messenger->expects($this->once())
      ->method('addMessage')
      ->with($this->callback(static fn(MarkupInterface $message): bool => (string) $message === 'Test message.'));

    $logger = $this->createMock(LoggerChannelInterface::class);
    $logger->expects($this->once())->method('info')->with('Test message.');

    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger_factory->expects($this->once())->method('get')->with('generated_content')->willReturn($logger);

    (new GeneratedContentProgress($messenger, $logger_factory))->report('Test message.');
  }

  /**
   * Test that markup is rendered for the messenger but stripped for the log.
   */
  public function testReportStripsMarkupForLog(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $messenger->expects($this->once())
      ->method('addMessage')
      ->with($this->callback(static fn(MarkupInterface $message): bool => (string) $message === 'Created <a href="/node/1">Title</a> node.'));

    $logger = $this->createMock(LoggerChannelInterface::class);
    $logger->expects($this->once())->method('info')->with('Created Title node.');

    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger_factory->method('get')->willReturn($logger);

    (new GeneratedContentProgress($messenger, $logger_factory))->report('Created <a href="/node/1">Title</a> node.');
  }

}
