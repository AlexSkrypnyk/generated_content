<?php

namespace Drupal\generated_content\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\generated_content\GeneratedContentRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneratedContentForm.
 *
 * Admin form to provision generated-content items.
 *
 * @package Drupal\generated_content\Form
 */
class GeneratedContentForm extends FormBase implements ContainerInjectionInterface {

  /**
   * The generated content repository instance.
   *
   * @var \Drupal\generated_content\GeneratedContentRepository
   */
  protected $repository;

  /**
   * GeneratedContentForm constructor.
   */
  public function __construct(GeneratedContentRepository $repository) {
    $this->repository = $repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      GeneratedContentRepository::getInstance()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'generated_content_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $info = $this->repository->getInfo();

    $options = [];
    foreach ($info as $item) {
      $options[$item['entity_type'] . '__' . $item['bundle']] = [
        $item['entity_type'],
        $item['bundle'],
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
      $this->t('Tracked'),
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

}
