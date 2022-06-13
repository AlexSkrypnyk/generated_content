<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\Helpers\GeneratedContentHelper;

/**
 * Tests static content generation.
 *
 * @group generated_content
 */
class GeneratedContentHelperStaticTest extends GeneratedContentKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'generated_content',
  ];

  /**
   * Test staticSentence().
   */
  public function testStaticSentence() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();
    $this->setExpectedStaticContent($helper, [
      'word11 word12 word13 word14 word15',
      'word21 word22 word23 word24 word25',
      'word31 word32 word33 word34 word35',
    ]);

    $this->assertSame('word11 word12 word13 word14 word15.', $helper::staticSentence());
    $this->assertSame('word21 word22 word23 word24 word25.', $helper::staticSentence());
    $this->assertSame('word31 word32 word33 word34 word35.', $helper::staticSentence());
    $this->assertSame('word11 word12 word13 word14 word15.', $helper::staticSentence());

    $helper->reset();

    $this->assertSame('word11 word12.', $helper::staticSentence(2));
    $this->assertSame('word21 word22.', $helper::staticSentence(2));
    $this->assertSame('word31 word32.', $helper::staticSentence(2));
    $this->assertSame('word11 word12.', $helper::staticSentence(2));

    $helper->reset();

    $this->assertSame('word11 word12 word13 word14 word15 word21 word22 word23 word24 word25.', $helper::staticSentence(10));
    $this->assertSame('word31 word32 word33 word34 word35 word11 word12 word13 word14 word15.', $helper::staticSentence(10));
    $this->assertSame('word21 word22 word23 word24 word25 word31 word32 word33 word34 word35.', $helper::staticSentence(10));
    $this->assertSame('word11 word12 word13 word14 word15 word21 word22 word23 word24 word25.', $helper::staticSentence(10));
  }

  /**
   * Test staticString().
   */
  public function testStaticString() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();
    $this->setExpectedStaticContent($helper, [
      'word11, word12, word13, word14, word15',
      'word21, word22, word23, word24, word25',
      'word31, word32, word33, word34, word35',
    ]);

    $this->assertSame('word11word12word13word14word15wo', $helper::staticString());
    $this->assertSame('word31word32word33word34word35wo', $helper::staticString());
    $this->assertSame('word21word22word23word24word25wo', $helper::staticString());
    $this->assertSame('word11word12word13word14word15wo', $helper::staticString());

    $helper->reset();

    $this->assertSame('word11word', $helper::staticString(10));
    $this->assertSame('word21word', $helper::staticString(10));
    $this->assertSame('word31word', $helper::staticString(10));
    $this->assertSame('word11word', $helper::staticString(10));

    $helper->reset();

    $this->assertSame('word11word12word13word14word15word21word22word23word24word25word31word32word33word34word35word11word', $helper::staticString(100));
    $this->assertSame('word21word22word23word24word25word31word32word33word34word35word11word12word13word14word15word21word', $helper::staticString(100));
    $this->assertSame('word31word32word33word34word35word11word12word13word14word15word21word22word23word24word25word31word', $helper::staticString(100));
    $this->assertSame('word11word12word13word14word15word21word22word23word24word25word31word32word33word34word35word11word', $helper::staticString(100));
  }

  /**
   * Test staticName().
   */
  public function testStaticName() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();
    $this->setExpectedStaticContent($helper, [
      'word11, word12, word13, word14, word15',
      'word21, word22, word23, word24, word25',
      'word31, word32, word33, word34, word35',
    ]);

    $this->assertSame('word11word12word', $helper::staticName());
    $this->assertSame('word21word22word', $helper::staticName());
    $this->assertSame('word31word32word', $helper::staticName());
    $this->assertSame('word11word12word', $helper::staticName());

    $helper->reset();

    $this->assertSame('word11word', $helper::staticName(10));
    $this->assertSame('word21word', $helper::staticName(10));
    $this->assertSame('word31word', $helper::staticName(10));
    $this->assertSame('word11word', $helper::staticName(10));
  }

  /**
   * Test staticAbbreviation().
   */
  public function testStaticAbbreviation() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();
    $this->setExpectedStaticContent($helper, [
      'word11, word12, word13, word14, word15',
      'word21, word22, word23, word24, word25',
      'word31, word32, word33, word34, word35',
    ]);

    $this->assertSame('wo', $helper::staticAbbreviation());
    $this->assertSame('wo', $helper::staticAbbreviation());
    $this->assertSame('wo', $helper::staticAbbreviation());
    $this->assertSame('wo', $helper::staticAbbreviation());

    $helper->reset();

    $this->assertSame('wor', $helper::staticAbbreviation(3));
    $this->assertSame('wor', $helper::staticAbbreviation(3));
    $this->assertSame('wor', $helper::staticAbbreviation(3));
    $this->assertSame('wor', $helper::staticAbbreviation(3));
  }

  /**
   * Test staticPlainParagraph().
   */
  public function testStaticPlainParagraph() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();
    $this->setExpectedStaticContent($helper, [
      'word11 word12 word13 word14 word15',
      'word21 word22 word23 word24 word25',
      'word31 word32 word33 word34 word35',
    ]);

    $this->assertSame('word11 word12 word13 word14 word15', $helper::staticPlainParagraph());
    $this->assertSame('word21 word22 word23 word24 word25', $helper::staticPlainParagraph());
    $this->assertSame('word31 word32 word33 word34 word35', $helper::staticPlainParagraph());
    $this->assertSame('word11 word12 word13 word14 word15', $helper::staticPlainParagraph());

    $helper->reset();

    $this->assertSame('word11 word12 word13 word14 word15', $helper::staticPlainParagraph());
    $this->assertSame('word21 word22 word23 word24 word25', $helper::staticPlainParagraph());
    $this->assertSame('word31 word32 word33 word34 word35', $helper::staticPlainParagraph());
    $this->assertSame('word11 word12 word13 word14 word15', $helper::staticPlainParagraph());
  }

  /**
   * Test staticHtmlParagraph().
   */
  public function testStaticHtmlParagraph() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();
    $this->setExpectedStaticContent($helper, [
      'word11 word12 word13 word14 word15',
      'word21 word22 word23 word24 word25',
      'word31 word32 word33 word34 word35',
    ]);

    $this->assertSame('<p>word11 word12 word13 word14 word15</p>', $helper::staticHtmlParagraph());
    $this->assertSame('<p>word21 word22 word23 word24 word25</p>', $helper::staticHtmlParagraph());
    $this->assertSame('<p>word31 word32 word33 word34 word35</p>', $helper::staticHtmlParagraph());
    $this->assertSame('<p>word11 word12 word13 word14 word15</p>', $helper::staticHtmlParagraph());

    $helper->reset();

    $this->assertSame('<p>word11 word12 word13 word14 word15</p>', $helper::staticHtmlParagraph());
    $this->assertSame('<p>word21 word22 word23 word24 word25</p>', $helper::staticHtmlParagraph());
    $this->assertSame('<p>word31 word32 word33 word34 word35</p>', $helper::staticHtmlParagraph());
    $this->assertSame('<p>word11 word12 word13 word14 word15</p>', $helper::staticHtmlParagraph());
  }

  /**
   * Test staticHtmlHeading().
   */
  public function testStaticHtmlHeading() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();
    $this->setExpectedStaticContent($helper, [
      'word11 word12 word13 word14 word15',
      'word21 word22 word23 word24 word25',
      'word31 word32 word33 word34 word35',
    ]);

    $this->assertSame('<h1>word11 word12 word13 word14 word15</h1>', $helper::staticHtmlHeading());
    $this->assertSame('<h1>word21 word22 word23 word24 word25</h1>', $helper::staticHtmlHeading());
    $this->assertSame('<h1>word31 word32 word33 word34 word35</h1>', $helper::staticHtmlHeading());
    $this->assertSame('<h1>word11 word12 word13 word14 word15</h1>', $helper::staticHtmlHeading());

    $helper->reset();

    $this->assertSame('<h1>word11 word12 word13 word14 word15</h1>', $helper::staticHtmlHeading());
    $this->assertSame('<h1>word21 word22 word23 word24 word25</h1>', $helper::staticHtmlHeading());
    $this->assertSame('<h1>word31 word32 word33 word34 word35</h1>', $helper::staticHtmlHeading());
    $this->assertSame('<h1>word11 word12 word13 word14 word15</h1>', $helper::staticHtmlHeading());

    $helper->reset();

    $helper = GeneratedContentHelper::getInstance();
    $this->assertSame('<h3>word11 word12 word13 word14 word15 word21 word22 word23 word24 word25</h3>', $helper::staticHtmlHeading(10, 3));
    $this->assertSame('<h3>word31 word32 word33 word34 word35 word11 word12 word13 word14 word15</h3>', $helper::staticHtmlHeading(10, 3));
    $this->assertSame('<h3>word21 word22 word23 word24 word25 word31 word32 word33 word34 word35</h3>', $helper::staticHtmlHeading(10, 3));
    $this->assertSame('<h3>word11 word12 word13 word14 word15 word21 word22 word23 word24 word25</h3>', $helper::staticHtmlHeading(10, 3));

    $helper->reset();

    $helper = GeneratedContentHelper::getInstance();
    $this->assertSame('<h6>word11 word12 word13 word14 word15 word21 word22 word23 word24 word25</h6>', $helper::staticHtmlHeading(10, 13));
    $this->assertSame('<h6>word31 word32 word33 word34 word35 word11 word12 word13 word14 word15</h6>', $helper::staticHtmlHeading(10, 13));
    $this->assertSame('<h6>word21 word22 word23 word24 word25 word31 word32 word33 word34 word35</h6>', $helper::staticHtmlHeading(10, 13));
    $this->assertSame('<h6>word11 word12 word13 word14 word15 word21 word22 word23 word24 word25</h6>', $helper::staticHtmlHeading(10, 13));

    $helper->reset();

    $helper = GeneratedContentHelper::getInstance();
    $this->assertSame('<h1>word11 word12 word13 word14 word15 word21 word22 word23 word24 word25</h1>', $helper::staticHtmlHeading(10, 0));
    $this->assertSame('<h1>word31 word32 word33 word34 word35 word11 word12 word13 word14 word15</h1>', $helper::staticHtmlHeading(10, 0));
    $this->assertSame('<h1>word21 word22 word23 word24 word25 word31 word32 word33 word34 word35</h1>', $helper::staticHtmlHeading(10, 0));
    $this->assertSame('<h1>word11 word12 word13 word14 word15 word21 word22 word23 word24 word25</h1>', $helper::staticHtmlHeading(10, 0));
  }

  /**
   * Test staticRichText().
   */
  public function testStaticRichText() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();
    $this->setExpectedStaticContent($helper, [
      'word11 word12 word13 word14 word15',
      'word21 word22 word23 word24 word25',
      'word31 word32 word33 word34 word35',
      'word41 word42 word43 word44 word45',
    ]);

    $content = '';
    $content .= '<h2>word11 word12 word13 word14 word15</h2>' . PHP_EOL;
    $content .= '<p>word21 word22 word23 word24 word25</p>' . PHP_EOL;
    $content .= '<p>word31 word32 word33 word34 word35</p>' . PHP_EOL;
    $content .= '<h3>word41 word42 word43 word44 word45</h3>' . PHP_EOL;
    $content .= '<p>word11 word12 word13 word14 word15</p>' . PHP_EOL;
    $content .= '<p>word21 word22 word23 word24 word25</p>';
    $this->assertSame($content, $helper::staticRichText());
  }

  /**
   * Set expected static content.
   */
  protected function setExpectedStaticContent($helper, $lines) {
    $this->setProtectedValue($helper, 'staticContent', $lines);
  }

}
