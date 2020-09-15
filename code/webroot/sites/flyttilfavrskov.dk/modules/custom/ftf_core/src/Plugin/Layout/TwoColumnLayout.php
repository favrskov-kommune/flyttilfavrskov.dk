<?php

namespace Drupal\ftf_core\Plugin\Layout;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dds_layouts\Plugin\Layout\DDSTwoColumnLayout;

class TwoColumnLayout extends DDSTwoColumnLayout {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['vertical_align'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Vertical align content'),
      '#default_value' => $this->configuration['vertical_align'] ?? FALSE,
      '#description' => $this->t('Vertical align content for this layout')
    ];
    $form['section_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Section ID'),
      '#default_value' => $this->configuration['section_id'],
      '#description' => $this->t('Should only contain lowercase letters, numbers and hyphens.')
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['vertical_align'] = $form_state->getValue('vertical_align');
    $this->configuration['section_id'] = Html::getId($form_state->getValue('section_id'));
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    $configuration['vertical_align'] = FALSE;
    $configuration['section_id'] = '';
    return $configuration;
  }
}
