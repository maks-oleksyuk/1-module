<?php

namespace Drupal\moliek\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an example form.
 */
class CatForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cat_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['cat_name'] = [
      '#maxlength' => 32,
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title_display' => 'before',
      '#title' => $this->t('Your cat’s name:'),
      '#pattern' => '^(?!\s*$)[0-9a-zA-Zа-яА-ЯіІїЇЁё`\' ]{2,32}$',
      '#placeholder' => $this->t("should be in the range of 2 and 32 symbols"),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t("Add cat"),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
