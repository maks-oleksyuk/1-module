<?php

namespace Drupal\moliek\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a confirmation form to confirm deletion cat by id.
 */
class DeleteCat extends ConfirmFormBase {
  /**
   * ID of the item to delete.
   *
   * @var int
   */
  protected $id;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $id = NULL): array {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = \Drupal::database();
    $database->delete('moliek')
      ->condition('id', $this->id)
      ->execute();
    \Drupal::messenger()->addStatus('Successfully deleted.');
    $form_state->setRedirect('moliek.content');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "moliek_delete_cat";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('moliek.content');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $database = \Drupal::database();
    $result = $database->select('moliek', 'm')
      ->fields('m', ['cat_name'])
      ->condition('id', $this->id)
      ->execute()->fetch();

    return $this->t('You really want to delete «%r»?', ['%r' => $result->cat_name]);
  }

}
