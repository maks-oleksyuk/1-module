<?php

namespace Drupal\moliek\Form;

use Drupal\file\Entity\File;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Ajax\MessageCommand;

/**
 * Implements an add cat form.
 */
class CatForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'cat_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['cat_name'] = [
      '#maxlength' => 32,
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title_display' => 'before',
      '#title' => $this->t('Your cat’s name:'),
      '#placeholder' => $this->t("should be in the range of 2 and 32 symbols"),
    ];
    $form['email'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#title_display' => 'before',
      '#title' => $this->t('Your email:'),
      '#suffix' => '<div class="email-massage"></div>',
      '#placeholder' => $this->t("username@domain.com Only latin characters, -, _"),
      '#ajax' => [
        'event' => 'keyup',
        'callback' => '::emailValidator',
        'progress' => 'none',
      ],
      '#attached' => [
        'library' => [
          'moliek/ajax-patch',
        ],
      ],
    ];
    $form['img'] = [
      '#type' => 'managed_file',
      '#required' => TRUE,
      '#title_display' => 'before',
      '#title' => $this->t('Add Cat image:'),
      '#upload_location' => 'public://cat_images',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpeg jpg png'],
        'file_validate_size' => [2097152],
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t("Add cat"),
      '#ajax' => [
        'event' => 'click',
        'callback' => '::submitAjax',
        'progress' => 'none',
      ],
    ];
    return $form;
  }

  /**
   * Setting the message in our form.
   */
  public function submitAjax(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    if ($form_state->getErrors()) {
      foreach ($form_state->getErrors() as $err) {
        $response->addCommand(new MessageCommand(
          $err, NULL, ['type' => 'error']));
      }
      $form_state->clearErrors();
    }
    else {
      $response->addCommand(new MessageCommand(
        $this->t('Your cat added successfully.'),
        NULL,
        ['type' => 'status'],
        TRUE));
      $form_state->setRebuild(TRUE);
    }
    $this->messenger()->deleteAll();
    return $response;
  }

  /**
   * Validating for email field.
   */
  public function emailValidator(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $input = $form_state->getValue('email');
    $regex = '/^[A-Za-z_\-]+@\w+(?:\.\w+)+$/';
    if (preg_match($regex, $input)) {
      $response->addCommand(new MessageCommand(
        $this->t('Email valid'),
        '.email-massage'));
    }
    else {
      $response->addCommand(new MessageCommand(
        $this->t('E-mail name can only contain latin characters, hyphens and underscores.'),
        '.email-massage', ['type' => 'error']));
    }
    if (empty($input)) {
      $response->addCommand(new RemoveCommand('.email-massage .messages--error'));
    }
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $val = $form_state->getValue('cat_name');
    if (strlen($val) < 2 || strlen($val) > 32) {
      $form_state->setErrorByName(
        'cat_name',
        $this->t("Cat name should be in the range of 2 and 32 symbols")
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = \Drupal::database();
    $picture = $form_state->getValue('img');
    $file = File::load($picture[0]);
    $file->setPermanent();
    $file->save();
    $database->insert('moliek')
      ->fields([
        'cat_img' => $picture[0],
        'cat_name' => $form_state->getValue('cat_name'),
        'email' => $form_state->getValue('email'),
        'created' => time(),
      ])
      ->execute();
    $this->messenger()->addMessage($this->t("Cat add to table"), 'status');
  }

}
