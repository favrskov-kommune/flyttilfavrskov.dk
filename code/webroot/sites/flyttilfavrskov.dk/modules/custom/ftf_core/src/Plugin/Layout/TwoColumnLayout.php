<?php

namespace Drupal\ftf_core\Plugin\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\dds_layouts\Plugin\Layout\DDSTwoColumnLayout;

class TwoColumnLayout extends DDSTwoColumnLayout {

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['vertical_align'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Vertical align content'),
      '#default_value' => $this->configuration['vertical_align'] ?? FALSE,
      '#description' => $this->t('Vertical align content for this layout')
    ];
    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the plugin form as built
   *   by static::buildConfigurationForm().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. Calling code should pass on a subform
   *   state created through
   *   \Drupal\Core\Form\SubformState::createForSubform().
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['vertical_align'] = $form_state->getValue('vertical_align');
  }

  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    $configuration['vertical_align'] = FALSE;
    return $configuration;
  }
}
