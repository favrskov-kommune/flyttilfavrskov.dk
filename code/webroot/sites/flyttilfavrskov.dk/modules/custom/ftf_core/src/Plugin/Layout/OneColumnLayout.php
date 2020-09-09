<?php

namespace Drupal\ftf_core\Plugin\Layout;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dds_layouts\Plugin\Layout\DDSOneColumnLayout;

class OneColumnLayout extends DDSOneColumnLayout {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['section_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Section ID'),
      '#default_value' => $this->configuration['section_id'],
      '#description' => 'Should only contain lowercase letters, numbers and hyphens.'
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['section_id'] = Html::getId($form_state->getValue('section_id'));
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    $configuration['section_id'] = '';
    return $configuration;
  }

}
