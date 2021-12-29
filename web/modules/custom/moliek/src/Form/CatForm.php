<?php

namespace Drupal\moliek\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Messenger\Messenger;

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
      '#placeholder' => $this->t("should be in the range of 2 and 32 symbols"),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t("Add cat"),
      '#ajax' => [
        'event' => 'click',
        'callback' => '::myAjax',
        'progress' => [
          'message' => NULL,
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $val = $form_state->getValue('cat_name');
    if (strlen($val) < 2 || strlen($val) > 32) {
      $form_state->setErrorByName('cat_name',
        $this->t("Cat name should be in the range of 2 and 32 symbols")
      );
    }
  }

  /**
   * Setting the message in our form.
   */
  public function myAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if ($form_state->getErrors()) {
      foreach ($form_state->getErrors() as $err) {
        $response->addCommand(new MessageCommand($err, NULL, ['type' => 'error']));
      }
      $form_state->clearErrors();
    }
    else {
      $response->addCommand(new MessageCommand(t('Your cat added successfully.')));
    }
    $this->messenger()->deleteAll();
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
