<?php

namespace Drupal\moliek\Form;

use Drupal\file\Entity\File;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\CloseModalDialogCommand;

/**
 * Form for editing cats.
 */
class EditCat extends CatForm {

  /**
   * The results of the query to the table are given.
   *
   * @var object
   */
  protected object $cat;

  /**
   * {@inheritDoc}
   */
  public function getFormId(): string {
    return 'moliek_EditCat';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $id = NULL): array {
    $database = \Drupal::database();
    $result = $database->select('moliek', 'm')
      ->fields('m', ['id', 'cat_name', 'email', 'cat_img'])
      ->condition('id', $id)
      ->execute()->fetch();
    $this->cat = $result;
    $form = parent::buildForm($form, $form_state);
    $form['cat_name']['#default_value'] = $result->cat_name;
    $form['cat_name']['#prefix'] = '<div class="error-massage-modal"></div>';
    $form['email']['#default_value'] = $result->email;
    $form['email']['#suffix'] = '<div class="email-massage-modal"></div>';
    $form['img']['#default_value'][] = $result->cat_img;
    $form['submit']['#value'] = $this->t('Edit cat');
    return $form;
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
        '.email-massage-modal'));
    }
    else {
      $response->addCommand(new MessageCommand(
        $this->t('E-mail name can only contain latin characters, hyphens and underscores.'),
        '.email-massage-modal', ['type' => 'error']));
    }
    if (empty($input)) {
      $response->addCommand(new RemoveCommand('.email-massage-modal .messages--error'));
    }
    return $response;
  }

  /**
   * Output errors in the modal window of the edit form.
   */
  public function submitAjax(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    if ($form_state->getErrors()) {
      foreach ($form_state->getErrors() as $err) {
        $response->addCommand(new MessageCommand(
          $err, '.error-massage-modal', ['type' => 'error']));
      }
      $form_state->clearErrors();
    }
    else {
      $response->addCommand(new MessageCommand(
        $this->t('Cat information changed successfully.'),
        NULL,
        ['type' => 'status'],
        TRUE));
      $response->addCommand(new CloseModalDialogCommand());
    }
    $this->messenger()->deleteAll();
    return $response;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $updated = [
      'cat_name' => $form_state->getValue('cat_name'),
      'email' => $form_state->getValue('email'),
      'cat_img' => $form_state->getValue('img')[0],
    ];
    $database = \Drupal::database();
    $database->update('moliek')
      ->condition('id', $this->cat->id)
      ->fields($updated)
      ->execute();
    if ($updated['cat_img'] != $this->cat->cat_img) {
      File::load($this->cat->cat_img)->delete();
    }
  }

}
