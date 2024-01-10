<?php

declare(strict_types = 1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestMediaTrait;

/**
 * Tests media* helpers in GeneratedContentHelper class.
 *
 * @group generated_content
 */
class GeneratedContentHelperMediaTest extends GeneratedContentKernelTestBase {

  use GeneratedContentTestMediaTrait;

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  protected static $modules = [
    'field',
    'media',
    'file',
    'image',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->mediaSetUp();
  }

  /**
   * Tests the randomMediaItem() method.
   */
  public function testRandomMediaItem(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_media = $helper::randomMediaItem();
    $this->assertNULL($actual_media);

    $medias = $this->prepareMediaItems(5);
    $medias_merged = array_merge($medias[$this->mediaTypes[0]], $medias[$this->mediaTypes[1]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($medias_merged);

    $actual_media = $helper::randomMediaItem();
    $this->assertTrue(in_array($actual_media->id(), $this->replaceEntitiesWithIds($medias_merged)));

    $actual_media = $helper::randomMediaItem($this->mediaTypes[0]);
    $this->assertTrue(in_array($actual_media->id(), $this->replaceEntitiesWithIds($medias_merged)));
    $actual_media = $helper::randomMediaItem($this->mediaTypes[1]);
    $this->assertTrue(in_array($actual_media->id(), $this->replaceEntitiesWithIds($medias_merged)));

    $actual_media = $helper::randomMediaItem($this->randomString());
    $this->assertNull($actual_media);
  }

  /**
   * Tests the randomMediaItems() method.
   */
  public function testRandomMediaItems(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_medias = $helper::randomMediaItems();
    $this->assertSame([], $actual_medias);

    $medias = $this->prepareMediaItems(5);
    $medias_merged = array_merge($medias[$this->mediaTypes[0]], $medias[$this->mediaTypes[1]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($medias_merged);

    $actual_medias = $helper::randomMediaItems();
    $this->assertCount(5, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias_merged)));

    $actual_medias = $helper::randomMediaItems(NULL, 2);
    $this->assertCount(2, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias_merged)));

    $actual_medias = $helper::randomMediaItems(NULL, 20);
    $this->assertCount(10, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias_merged)));

    $actual_medias = $helper::randomMediaItems($this->mediaTypes[0]);
    $this->assertCount(5, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[0]])));

    $actual_medias = $helper::randomMediaItems($this->mediaTypes[0], 2);
    $this->assertCount(2, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[0]])));

    $actual_medias = $helper::randomMediaItems($this->mediaTypes[0], 20);
    $this->assertCount(5, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[0]])));
  }

  /**
   * Tests the randomRealMediaItem() method.
   */
  public function testRandomRealMediaItem(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_media = $helper::randomRealMediaItem();
    $this->assertNull($actual_media);

    $medias = $this->prepareMediaItems(5);
    $medias_merged = array_merge($medias[$this->mediaTypes[0]], $medias[$this->mediaTypes[1]]);

    $medias_in_repository = array_slice($medias_merged, 0, 7, TRUE);
    $medias_not_in_repository = array_slice($medias_merged, 7, 3, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($medias_in_repository);

    $this->assertTrue(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_not_in_repository)));
    $this->assertFalse(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_in_repository)));
    $this->assertFalse(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_in_repository)));
    $this->assertFalse(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_in_repository)));
    $this->assertFalse(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_in_repository)));
    $this->assertFalse(in_array($helper::randomRealMediaItem()->id(), $this->replaceEntitiesWithIds($medias_in_repository)));
  }

  /**
   * Tests the randomRealMediaItems() method.
   */
  public function testRandomRealMediaItems(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_medias = $helper::randomRealMediaItems();
    $this->assertSame([], $actual_medias);

    $medias = $this->prepareMediaItems(5);
    $medias_merged = array_merge($medias[$this->mediaTypes[0]], $medias[$this->mediaTypes[1]]);

    $medias_in_repository = array_slice($medias_merged, 0, 7, TRUE);
    $medias_not_in_repository = array_slice($medias_merged, 7, 3, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($medias_in_repository);

    $actual_medias = $helper::randomRealMediaItems();
    $this->assertCount(3, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias_not_in_repository)));
    $this->assertCount(0, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias_in_repository)));

    $actual_medias = $helper::randomRealMediaItems(NULL, 2);
    $this->assertCount(2, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias_not_in_repository)));

    $actual_medias = $helper::randomRealMediaItems(NULL, 10);
    $this->assertCount(3, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias_not_in_repository)));

    $actual_medias = $helper::randomRealMediaItems($this->mediaTypes[0]);
    $this->assertCount(0, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[0]])));
    $actual_medias = $helper::randomRealMediaItems($this->mediaTypes[1]);
    $this->assertCount(3, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[1]])));

    $actual_medias = $helper::randomRealMediaItems($this->mediaTypes[0], 2);
    $this->assertCount(0, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[0]])));
    $actual_medias = $helper::randomRealMediaItems($this->mediaTypes[1], 2);
    $this->assertCount(2, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[1]])));

    $actual_medias = $helper::randomRealMediaItems($this->mediaTypes[0], 20);
    $this->assertCount(0, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[0]])));
    $actual_medias = $helper::randomRealMediaItems($this->mediaTypes[1], 20);
    $this->assertCount(3, array_intersect($this->replaceEntitiesWithIds($actual_medias), $this->replaceEntitiesWithIds($medias[$this->mediaTypes[1]])));
  }

  /**
   * Tests the staticMediaItem() method.
   */
  public function testStaticMediaItem(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_media = $helper::staticMediaItem();
    $this->assertNull($actual_media);

    $medias = $this->prepareMediaItems(3);
    $medias_merged = array_merge($medias[$this->mediaTypes[0]], $medias[$this->mediaTypes[1]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($medias_merged);

    $ids = $this->replaceEntitiesWithIds($medias_merged);
    $this->assertSame($helper::staticMediaItem()->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticMediaItem()->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticMediaItem()->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticMediaItem()->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticMediaItem()->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticMediaItem()->id(), array_values($ids)[5]);
    $this->assertSame($helper::staticMediaItem()->id(), array_values($ids)[0]);

    $this->assertSame($helper::staticMediaItem($this->mediaTypes[0])->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticMediaItem($this->mediaTypes[0])->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticMediaItem($this->mediaTypes[0])->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticMediaItem($this->mediaTypes[0])->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticMediaItem($this->mediaTypes[1])->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticMediaItem($this->mediaTypes[1])->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticMediaItem($this->mediaTypes[1])->id(), array_values($ids)[5]);
    $this->assertSame($helper::staticMediaItem($this->mediaTypes[1])->id(), array_values($ids)[3]);
  }

  /**
   * Tests the staticMediaItems() method.
   */
  public function testStaticMediaItems(): void {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_medias = $helper::staticMediaItems();
    $this->assertSame([], $actual_medias);

    $medias = $this->prepareMediaItems(3);
    $medias_merged = array_merge($medias[$this->mediaTypes[0]], $medias[$this->mediaTypes[1]]);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($medias_merged);

    $ids = array_values($this->replaceEntitiesWithIds($medias_merged));

    $actual_medias = $helper::staticMediaItems();
    $this->assertCount(6, array_intersect($this->replaceEntitiesWithIds($actual_medias), $ids));

    $helper->reset();

    $actual_medias = $helper::staticMediaItems(NULL, 7);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_medias)), array_merge($ids, [$ids[0]]));

    $helper->reset();

    $actual_medias = $helper::staticMediaItems($this->mediaTypes[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_medias)), array_merge([$ids[0]]));
    $actual_medias = $helper::staticMediaItems($this->mediaTypes[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_medias)), array_merge([$ids[1]]));
    $actual_medias = $helper::staticMediaItems($this->mediaTypes[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_medias)), array_merge([$ids[2]]));
    $actual_medias = $helper::staticMediaItems($this->mediaTypes[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_medias)), array_merge([$ids[0]]));

    $helper->reset();

    $actual_medias = $helper::staticMediaItems($this->mediaTypes[0], 4);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_medias)), array_merge([
      $ids[0],
      $ids[1],
      $ids[2],
      $ids[0],
    ]));

    $actual_medias = $helper::staticMediaItems($this->mediaTypes[1], 4);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_medias)), array_merge([
      $ids[3],
      $ids[4],
      $ids[5],
      $ids[3],
    ]));

    $actual_medias = $helper::staticMediaItems(NULL, 7);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_medias)), array_merge($ids, [$ids[0]]));
  }

}
