<?php

declare(strict_types=1);

namespace Drupal\generated_content;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drush\Drush;

/**
 * Logs generated content progress to every available output channel.
 */
class GeneratedContentLogger {

  /**
   * Logger channel.
   */
  protected LoggerChannelInterface $logger;

  /**
   * Constructs a GeneratedContentLogger object.
   */
  public function __construct(
    protected MessengerInterface $messenger,
    LoggerChannelFactoryInterface $logger_channel_factory,
  ) {
    $this->logger = $logger_channel_factory->get('generated_content');
  }

  /**
   * Logs a progress message.
   *
   * Queues the message on the messenger, which drives the web UI and is
   * relayed by the Drush batch runner. When running under Drush it is also
   * written straight to the console, so it stays visible without a verbosity
   * flag even where the messenger output would otherwise be suppressed. The
   * message is additionally sent to the generated_content logger channel so
   * that watchdog and syslog retain a record of the run.
   *
   * @param string|\Stringable $message
   *   The pre-formatted message. May contain HTML markup, which is rendered
   *   for the messenger and stripped for the console and the log.
   */
  public function log(string|\Stringable $message): void {
    $message = (string) $message;

    $this->messenger->addMessage(new FormattableMarkup($message, []));

    if (class_exists(Drush::class) && Drush::hasContainer()) {
      // Console output is only reachable under a bootstrapped Drush runtime,
      // which is not present during PHPUnit runs.
      // @codeCoverageIgnoreStart
      Drush::output()->writeln(html_entity_decode(strip_tags($message)));
      // @codeCoverageIgnoreEnd
    }

    $this->logger->info(strip_tags($message));
  }

}
