<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Unit;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\generated_content\GeneratedContentLogger;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the GeneratedContentLogger service.
 *
 * @group generated_content
 */
#[Group('generated_content')]
class GeneratedContentLoggerTest extends GeneratedContentUnitTestBase {

  /**
   * Test that a message reaches both the messenger and the logger channel.
   */
  public function testLog(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $messenger->expects($this->once())
      ->method('addMessage')
      ->with($this->callback(static fn(MarkupInterface $message): bool => (string) $message === 'Test message.'));

    $logger = $this->createMock(LoggerChannelInterface::class);
    $logger->expects($this->once())->method('info')->with('Test message.');

    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger_factory->expects($this->once())->method('get')->with('generated_content')->willReturn($logger);

    (new GeneratedContentLogger($messenger, $logger_factory))->log('Test message.');
  }

  /**
   * Test that markup is rendered for the messenger but stripped for the log.
   */
  public function testLogStripsMarkupForLog(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $messenger->expects($this->once())
      ->method('addMessage')
      ->with($this->callback(static fn(MarkupInterface $message): bool => (string) $message === 'Created <a href="/node/1">Title</a> node.'));

    $logger = $this->createMock(LoggerChannelInterface::class);
    $logger->expects($this->once())->method('info')->with('Created Title node.');

    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger_factory->method('get')->willReturn($logger);

    (new GeneratedContentLogger($messenger, $logger_factory))->log('Created <a href="/node/1">Title</a> node.');
  }

}
