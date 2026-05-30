<?php

declare(strict_types=1);

namespace Drupal\Tests\generated_content\Kernel;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormState;
use Drupal\generated_content\Form\GeneratedContentForm;
use Drupal\generated_content\GeneratedContentRepository;

/**
 * Tests the GeneratedContentForm admin form.
 *
 * @group generated_content
 *
 * @covers \Drupal\generated_content\Form\GeneratedContentForm
 */
class GeneratedContentFormTest extends GeneratedContentKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'file',
    'taxonomy',
    'generated_content',
  ];

  /**
   * Tests getFormId() returns a stable identifier.
   */
  public function testGetFormId(): void {
    $form = $this->buildFormInstance();

    $this->assertSame('generated_content_form', $form->getFormId());
  }

  /**
   * Tests create() returns a configured form instance.
   */
  public function testCreate(): void {
    $form = GeneratedContentForm::create($this->container);

    $this->assertInstanceOf(GeneratedContentForm::class, $form);
    $this->assertInstanceOf(ContainerInjectionInterface::class, $form);
  }

  /**
   * Tests buildForm() with no plugins shows the empty state and no buttons.
   */
  public function testBuildFormEmpty(): void {
    $form_array = [];
    $form_state = new FormState();

    $form = $this->buildFormInstance();
    $built = $form->buildForm($form_array, $form_state);

    $this->assertArrayHasKey('table', $built);
    $this->assertSame('tableselect', $built['table']['#type']);
    $this->assertSame([], $built['table']['#options']);
    $this->assertArrayNotHasKey('generate', $built);
    $this->assertArrayNotHasKey('delete', $built);
    $this->assertArrayNotHasKey('regenerate', $built);
    $this->assertNotEmpty((string) $built['table']['#empty']);
  }

  /**
   * Tests buildForm() with plugins exposes the action buttons.
   */
  public function testBuildFormWithPlugins(): void {
    $repository = $this->createMock(GeneratedContentRepository::class);
    $repository->method('getInfo')->willReturn([
      'user__user' => [
        'entity_type' => 'user',
        'bundle' => 'user',
        '#weight' => 1,
        '#tracking' => TRUE,
        '#module' => 'test',
        '#plugin_id' => 'test_user',
      ],
    ]);
    $repository->method('getEntities')->willReturn([1, 2]);

    $form = new GeneratedContentForm($repository);
    $form->setStringTranslation($this->container->get('string_translation'));

    $form_array = [];
    $form_state = new FormState();
    $built = $form->buildForm($form_array, $form_state);

    $this->assertArrayHasKey('table', $built);
    $this->assertCount(1, $built['table']['#options']);
    $this->assertArrayHasKey('actions_description', $built);
    $this->assertArrayHasKey('generate', $built);
    $this->assertArrayHasKey('delete', $built);
    $this->assertArrayHasKey('regenerate', $built);
    $this->assertSame('generate', $built['generate']['#name']);
    $this->assertSame('delete', $built['delete']['#name']);
    $this->assertSame('regenerate', $built['regenerate']['#name']);
  }

  /**
   * Tests submitForm() dispatches each button to the matching repository call.
   *
   * @param string $button
   *   Triggering button name.
   * @param string $expected_method
   *   Repository method that must be invoked.
   *
   * @dataProvider dataProviderSubmitForm
   */
  public function testSubmitFormDispatch(string $button, string $expected_method): void {
    $info_item = [
      'entity_type' => 'user',
      'bundle' => 'user',
      '#weight' => 1,
      '#tracking' => TRUE,
      '#module' => 'test',
      '#plugin_id' => 'test_user',
    ];

    $repository = $this->createMock(GeneratedContentRepository::class);
    $repository->method('getInfo')->willReturn(['user__user' => $info_item]);
    $repository->method('findInfo')->willReturn($info_item);
    $repository->expects($this->once())
      ->method($expected_method)
      ->with([$info_item]);

    $form = new GeneratedContentForm($repository);

    $form_state = new FormState();
    $form_state->setValue('table', ['user__user' => 'user__user']);
    $form_state->setTriggeringElement(['#name' => $button]);

    $form_array = [];
    $form->submitForm($form_array, $form_state);
  }

  /**
   * Data provider for testSubmitFormDispatch().
   *
   * @return array<string, array<string>>
   *   Provider data.
   */
  public static function dataProviderSubmitForm(): array {
    return [
      'generate button calls createBatch' => ['generate', 'createBatch'],
      'delete button calls removeBatch' => ['delete', 'removeBatch'],
      'regenerate button calls regenerateBatch' => ['regenerate', 'regenerateBatch'],
    ];
  }

  /**
   * Tests submitForm() with an unknown button name is a no-op.
   */
  public function testSubmitFormUnknownButton(): void {
    $repository = $this->createMock(GeneratedContentRepository::class);
    $repository->method('getInfo')->willReturn([]);
    $repository->expects($this->never())->method('createBatch');
    $repository->expects($this->never())->method('removeBatch');
    $repository->expects($this->never())->method('regenerateBatch');

    $form = new GeneratedContentForm($repository);
    $form_state = new FormState();
    $form_state->setValue('table', []);
    $form_state->setTriggeringElement(['#name' => 'cancel']);

    $form_array = [];
    $form->submitForm($form_array, $form_state);
  }

  /**
   * Tests entityInfoToLink() for each supported entity type.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string|null $bundle
   *   Bundle.
   * @param string $must_contain
   *   Substring the returned link/string must include.
   *
   * @dataProvider dataProviderEntityInfoToLink
   */
  public function testEntityInfoToLink(string $entity_type, ?string $bundle, string $must_contain): void {
    $form = $this->buildFormInstance();
    $result = (string) self::callProtectedMethod($form, 'entityInfoToLink', [$entity_type, $bundle]);

    $this->assertStringContainsString($must_contain, $result);
  }

  /**
   * Data provider for testEntityInfoToLink().
   *
   * @return array<string, array<mixed>>
   *   Provider data.
   */
  public static function dataProviderEntityInfoToLink(): array {
    return [
      'node entity-only renders as label' => ['node', NULL, 'node'],
      'node with bundle renders bundle' => ['node', 'page', 'page'],
      'file path link' => ['file', NULL, 'file'],
      'media path link' => ['media', NULL, 'media'],
      'media with bundle renders bundle' => ['media', 'image', 'image'],
      'taxonomy term entity-only renders entity label' => ['taxonomy_term', NULL, 'taxonomy_term'],
      'user collection link' => ['user', NULL, 'user'],
      'unknown entity type passes through' => ['custom', NULL, 'custom'],
    ];
  }

  /**
   * Build a real GeneratedContentForm wired against the container.
   */
  protected function buildFormInstance(): GeneratedContentForm {
    return GeneratedContentForm::create($this->container);
  }

}
