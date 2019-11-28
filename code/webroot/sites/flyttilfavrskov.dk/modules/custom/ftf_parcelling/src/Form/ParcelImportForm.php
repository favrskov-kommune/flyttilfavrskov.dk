<?php

namespace Drupal\ftf_parcelling\Form;

use Drupal\Core\Form\FormBase;
use Drupal\ftf_parcelling\Service\AreaImportService;
use Drupal\ftf_parcelling\Service\ParcelImportService;
use Drupal\ftf_parcelling\Service\ParcellingImportService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ParcelImportForm extends FormBase {

  /**
   * @var \Drupal\ftf_parcelling\Service\AreaImportService
   */
  protected $area_import_service;

  /**
   * @var \Drupal\ftf_parcelling\Service\ParcellingImportService
   */
  protected $parcelling_import_service;

  /**
   * @var \Drupal\ftf_parcelling\Service\ParcelImportService
   */
  protected $parcel_import_service;

  /**
   * ParcelImportForm constructor.
   *
   * @param \Drupal\ftf_parcelling\Service\AreaImportService $area_import_service
   * @param \Drupal\ftf_parcelling\Service\ParcellingImportService $parcelling_import_service
   * @param \Drupal\ftf_parcelling\Service\ParcelImportService $parcel_import_service
   */
  public function __construct(AreaImportService $area_import_service, ParcellingImportService $parcelling_import_service, ParcelImportService $parcel_import_service) {
    $this->area_import_service = $area_import_service;
    $this->parcelling_import_service = $parcelling_import_service;
    $this->parcel_import_service = $parcel_import_service;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return \Drupal\Core\Form\FormBase|static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ftf_parcelling.area_import'),
      $container->get('ftf_parcelling.parcelling_import'),
      $container->get('ftf_parcelling.parcel_import')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'parcel_import_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    // just to make sure noone ever accidentally uses this or
    // out of curiosity i have commented it out
    /*
    $form['delete'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete all courses'),
    ];
    */
    $this->area_import_service->startAreaImport();
    $this->parcelling_import_service->startParcellingImport();
    $this->parcel_import_service->startParcelImport();

    $form['import'] = [
      '#type' => 'submit',
      '#value' => 'Import Parcel Data',
    ];


    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

    // Delete courses before importing
    if ($form_state->getValue('delete')) {
      //delete parcel data
    }
    else {
      $response = $this->import_service->startParcelImport();
      if ($response['status']) {
        \Drupal::messenger()->addStatus($response['message']);
      }
    }
  }

}
