<?php

declare(strict_types=1);

namespace Drupal\generated_content\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\generated_content\GeneratedContentRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneratedContentForm.
 *
 * Admin form to provision generated-content items.
 *
 * @package Drupal\generated_content\Form
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class GeneratedContentForm extends FormBase implements ContainerInjectionInterface {


  /**
   * The generated content repository instance.
   *
   * @var \Drupal\generated_content\GeneratedContentRepository
   */
  protected GeneratedContentRepository $repository;

  /**
   * GeneratedContentForm constructor.
   */
  public function __construct(GeneratedContentRepository $repository) {
    $this->repository = $repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): GeneratedContentForm {
    // @phpstan-ignore-next-line
    return new static(
      GeneratedContentRepository::getInstance()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'generated_content_form';
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $info = $this->repository->getInfo();

    $options = [];
    foreach ($info as $item) {
      $options[$item['entity_type'] . '__' . $item['bundle']] = [
        $this->entityInfoToLink($item['entity_type']),
        $this->entityInfoToLink($item['entity_type'], $item['bundle']),
        $item['#weight'],
        $item['#tracking'] ? $this->t('Enabled') : $this->t('Disabled'),
        $item['#module'],
        count($this->repository->getEntities($item['entity_type'], $item['bundle'])),
      ];
    }

    $header = [
      $this->t('Entity'),
      $this->t('Bundle'),
      $this->t('Weight'),
      $this->t('Tracking'),
      $this->t('Module'),
      $this->t('Created count'),
    ];
    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options,
      '#empty' => $this->t('No generated content implementations found. Please refer to <code>generated_content.api.php</code>.'),
    ];

    $form['generate'] = [
      '#type' => 'submit',
      '#name' => 'generate',
      '#value' => $this->t('Generate'),
    ];

    $form['delete'] = [
      '#type' => 'submit',
      '#name' => 'delete',
      '#value' => $this->t('Delete'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $results = array_filter($form_state->getValue('table'));

    $info = $this->repository->getInfo();
    // Sort results by weight set in info.
    $results = array_intersect_key(array_merge($info, $results), $results);

    $info = [];
    foreach ($results as $result) {
      [$entity_type, $bundle] = explode('__', $result);
      $item_info = $this->repository->findInfo($entity_type, $bundle);
      if ($item_info) {
        $info[] = $item_info;
      }
    }

    $triggering_element = $form_state->getTriggeringElement();
    $button_name = $triggering_element['#name'];
    if ($button_name === 'generate') {
      $this->repository->createBatch($info);
    }
    elseif ($button_name === 'delete') {
      $this->repository->removeBatch($info);
    }
  }

  /**
   * Get link for specific entity_type and optional bundle.
   *
   * Since each entity type declares routes for collection pages in own ways,
   * this method tries to "guess" collection links from provided information.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Optional bundle. If provided - more "granular" link will be returned.
   *
   * @return \Drupal\Core\GeneratedLink|string
   *   Link to the entity collection or originally passed value if there is no
   *   internal mapping for this entity_type/bundle.
   */
  protected function entityInfoToLink($entity_type, $bundle = NULL) {
    $collection_route = NULL;
    $route_params = [];
    $path = NULL;
    $query = [];

    switch ($entity_type) {
      case 'node':
        $collection_route = 'system.admin_content';
        $query = $bundle ? ['type' => $bundle] : $query;
        break;

      case 'file':
        $path = '/admin/content/files';
        break;

      case 'media':
        $path = '/admin/content/media';
        $query = $bundle ? ['type' => $bundle] : $query;
        break;

      case 'taxonomy_term':
        $collection_route = $bundle ? 'entity.taxonomy_vocabulary.overview_form' : 'entity.taxonomy_vocabulary.collection';
        $route_params = $bundle ? ['taxonomy_vocabulary' => $bundle] : $route_params;
        break;

      case 'user':
        $collection_route = 'entity.user.collection';
        break;
    }

    $label = $bundle ?: $entity_type;

    if ($collection_route) {
      return Link::createFromRoute($label, $collection_route, $route_params, ['query' => $query])->toString();
    }
    elseif ($path) {
      return Link::fromTextAndUrl($label, Url::fromUserInput($path, ['query' => $query]))->toString();
    }

    return $label;
  }

}
