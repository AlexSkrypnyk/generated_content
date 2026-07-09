<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\generated_content\GeneratedContentProgress;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the GeneratedContentProgress reporter.
 *
 * @group generated_content
 */
#[Group('generated_content')]
class GeneratedContentProgressTest extends GeneratedContentKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['dblog'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('dblog', ['watchdog']);
  }

  /**
   * Test that a message reaches both the messenger and the logger channel.
   */
  public function testReportSendsToMessengerAndLog(): void {
    GeneratedContentProgress::report('Test progress message.');

    $status_messages = array_map(strval(...), \Drupal::messenger()->messagesByType(MessengerInterface::TYPE_STATUS));
    $this->assertContains('Test progress message.', $status_messages);

    $logged = $this->getChannelLog();
    $this->assertArrayHasKey('Test progress message.', $logged);
    $this->assertEquals(RfcLogLevel::INFO, $logged['Test progress message.']);
  }

  /**
   * Test that markup is rendered for the messenger but stripped for the log.
   */
  public function testReportStripsMarkupForLog(): void {
    GeneratedContentProgress::report('Created <a href="/node/1">Title</a> node.');

    $status_messages = array_map(strval(...), \Drupal::messenger()->messagesByType(MessengerInterface::TYPE_STATUS));
    $this->assertContains('Created <a href="/node/1">Title</a> node.', $status_messages);

    $logged = $this->getChannelLog();
    $this->assertArrayHasKey('Created Title node.', $logged);
  }

  /**
   * Get logged messages for the generated_content channel.
   *
   * @return array<string, int>
   *   Logged message text keyed to its severity.
   */
  protected function getChannelLog(): array {
    return \Drupal::database()->select('watchdog', 'w')
      ->fields('w', ['message', 'severity'])
      ->condition('type', 'generated_content')
      ->execute()
      ->fetchAllKeyed();
  }

}
