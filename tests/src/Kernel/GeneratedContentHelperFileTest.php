<?php

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\generated_content\GeneratedContentRepository;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\Tests\generated_content\Traits\GeneratedContentTestFileTrait;

/**
 * Tests file* helpers in GeneratedContentHelper class.
 *
 * @group generated_content
 */
class GeneratedContentHelperFileTest extends GeneratedContentKernelTestBase {

  use GeneratedContentTestFileTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field',
    'file',
    'image',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->fileSetUp();
  }

  /**
   * Tests the randomFile() method.
   */
  public function testRandomFile() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_file = $helper::randomFile();
    $this->assertNULL($actual_file);

    $files1 = $this->prepareFiles(5, $this->fileExtensions[0]);
    $files2 = $this->prepareFiles(5, $this->fileExtensions[1]);
    $files_merged = array_merge($files1, $files2);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($files_merged);

    $actual_file = $helper::randomFile();
    $this->assertTrue(in_array($actual_file->id(), $this->replaceEntitiesWithIds($files_merged)));

    $actual_file = $helper::randomFile($this->fileExtensions[0]);
    $this->assertTrue(in_array($actual_file->id(), $this->replaceEntitiesWithIds($files_merged)));
    $actual_file = $helper::randomFile($this->fileExtensions[1]);
    $this->assertTrue(in_array($actual_file->id(), $this->replaceEntitiesWithIds($files_merged)));

    $actual_file = $helper::randomFile($this->randomString());
    $this->assertNull($actual_file);
  }

  /**
   * Tests the randomFiles() method.
   */
  public function testRandomFiles() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    $actual_files = $helper::randomFiles();
    $this->assertSame([], $actual_files);

    $files1 = $this->prepareFiles(5, $this->fileExtensions[0]);
    $files2 = $this->prepareFiles(5, $this->fileExtensions[1]);
    $files_merged = array_merge($files1, $files2);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($files_merged);

    $actual_files = $helper::randomFiles();
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files_merged))));

    $actual_files = $helper::randomFiles(NULL, 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files_merged))));

    $actual_files = $helper::randomFiles(NULL, 20);
    $this->assertSame(10, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files_merged))));

    $actual_files = $helper::randomFiles($this->fileExtensions[0]);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files1))));

    $actual_files = $helper::randomFiles($this->fileExtensions[0], 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files1))));

    $actual_files = $helper::randomFiles($this->fileExtensions[0], 20);
    $this->assertSame(5, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files1))));
  }

  /**
   * Tests the randomRealFile() method.
   */
  public function testRandomRealFile() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a file not added to the repository is not returned.
    $actual_file = $helper::randomRealFile();
    $this->assertNull($actual_file);

    $files1 = $this->prepareFiles(5, $this->fileExtensions[0]);
    $files2 = $this->prepareFiles(5, $this->fileExtensions[1]);
    $files_merged = array_merge($files1, $files2);

    $files_in_repository = array_slice($files_merged, 0, 7, TRUE);
    $files_not_in_repository = array_slice($files_merged, 7, 3, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($files_in_repository);

    $this->assertTrue(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_not_in_repository)));
    $this->assertTrue(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_not_in_repository)));
    $this->assertFalse(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_in_repository)));
    $this->assertFalse(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_in_repository)));
    $this->assertFalse(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_in_repository)));
    $this->assertFalse(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_in_repository)));
    $this->assertFalse(in_array($helper::randomRealFile()->id(), $this->replaceEntitiesWithIds($files_in_repository)));
  }

  /**
   * Tests the randomRealFiles() method.
   */
  public function testRandomRealFiles() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_files = $helper::randomRealFiles();
    $this->assertSame([], $actual_files);

    $files1 = $this->prepareFiles(5, $this->fileExtensions[0]);
    $files2 = $this->prepareFiles(5, $this->fileExtensions[1]);
    $files_merged = array_merge($files1, $files2);

    $files_in_repository = array_slice($files_merged, 0, 7, TRUE);
    $files_not_in_repository = array_slice($files_merged, 7, 3, TRUE);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($files_in_repository);

    $actual_files = $helper::randomRealFiles();
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files_not_in_repository))));
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files_in_repository))));

    $actual_files = $helper::randomRealFiles(NULL, 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files_not_in_repository))));

    $actual_files = $helper::randomRealFiles(NULL, 10);
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files_not_in_repository))));

    $actual_files = $helper::randomRealFiles($this->fileExtensions[0]);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files1))));
    $actual_files = $helper::randomRealFiles($this->fileExtensions[1]);
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files2))));

    $actual_files = $helper::randomRealFiles($this->fileExtensions[0], 2);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files1))));
    $actual_files = $helper::randomRealFiles($this->fileExtensions[1], 2);
    $this->assertSame(2, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files2))));

    $actual_files = $helper::randomRealFiles($this->fileExtensions[0], 20);
    $this->assertSame(0, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files1))));
    $actual_files = $helper::randomRealFiles($this->fileExtensions[1], 20);
    $this->assertSame(3, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $this->replaceEntitiesWithIds($files2))));
  }

  /**
   * Tests the staticFile() method.
   */
  public function testStaticFile() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that a user not added to the repository is not returned.
    $actual_file = $helper::staticFile();
    $this->assertNull($actual_file);

    $files1 = $this->prepareFiles(3, $this->fileExtensions[0]);
    $files2 = $this->prepareFiles(3, $this->fileExtensions[1]);
    $files_merged = array_merge($files1, $files2);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($files_merged);

    $ids = $this->replaceEntitiesWithIds($files_merged);
    $this->assertSame($helper::staticFile()->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticFile()->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticFile()->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticFile()->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticFile()->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticFile()->id(), array_values($ids)[5]);
    $this->assertSame($helper::staticFile()->id(), array_values($ids)[0]);

    $this->assertSame($helper::staticFile($this->fileExtensions[0])->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticFile($this->fileExtensions[0])->id(), array_values($ids)[1]);
    $this->assertSame($helper::staticFile($this->fileExtensions[0])->id(), array_values($ids)[2]);
    $this->assertSame($helper::staticFile($this->fileExtensions[0])->id(), array_values($ids)[0]);
    $this->assertSame($helper::staticFile($this->fileExtensions[1])->id(), array_values($ids)[3]);
    $this->assertSame($helper::staticFile($this->fileExtensions[1])->id(), array_values($ids)[4]);
    $this->assertSame($helper::staticFile($this->fileExtensions[1])->id(), array_values($ids)[5]);
    $this->assertSame($helper::staticFile($this->fileExtensions[1])->id(), array_values($ids)[3]);
  }

  /**
   * Tests the staticFiles() method.
   */
  public function testStaticFiles() {
    /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
    $helper = GeneratedContentHelper::getInstance();

    // Assert that when users are not added to the repository they are not
    // returned.
    $actual_files = $helper::staticFiles();
    $this->assertSame([], $actual_files);

    $files1 = $this->prepareFiles(3, $this->fileExtensions[0]);
    $files2 = $this->prepareFiles(3, $this->fileExtensions[1]);
    $files_merged = array_merge($files1, $files2);

    $repository = GeneratedContentRepository::getInstance();
    $repository->addEntities($files_merged);

    $ids = array_values($this->replaceEntitiesWithIds($files_merged));

    $actual_files = $helper::staticFiles();
    $this->assertSame(6, count(array_intersect($this->replaceEntitiesWithIds($actual_files), $ids)));

    $helper->reset();

    $actual_files = $helper::staticFiles(NULL, 7);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_files)), array_merge($ids, [$ids[0]]));

    $helper->reset();

    $actual_files = $helper::staticFiles($this->fileExtensions[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_files)), array_merge([$ids[0]]));
    $actual_files = $helper::staticFiles($this->fileExtensions[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_files)), array_merge([$ids[1]]));
    $actual_files = $helper::staticFiles($this->fileExtensions[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_files)), array_merge([$ids[2]]));
    $actual_files = $helper::staticFiles($this->fileExtensions[0], 1);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_files)), array_merge([$ids[0]]));

    $helper->reset();

    $actual_files = $helper::staticFiles($this->fileExtensions[0], 4);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_files)), array_merge([
      $ids[0],
      $ids[1],
      $ids[2],
      $ids[0],
    ]));

    $actual_files = $helper::staticFiles($this->fileExtensions[1], 4);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_files)), array_merge([
      $ids[3],
      $ids[4],
      $ids[5],
      $ids[3],
    ]));

    $actual_files = $helper::staticFiles(NULL, 7);
    $this->assertSame(array_values($this->replaceEntitiesWithIds($actual_files)), array_merge($ids, [$ids[0]]));
  }

}
