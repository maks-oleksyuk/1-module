<?php

namespace Drupal\moliek\Form;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
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
  public function buildForm(array $form, FormStateInterface $form_state, string $id = NULL) {
    $this->id = $id;
    $form = parent::buildForm($form, $form_state);
    $form['#attached']['library'][] = 'moliek/ajax-patch';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @todo: Do the deletion.
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
  public function getCancelUrl() {
    return new Url('moliek.content');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Do you want to delete %id?', ['%id' => $this->id]);
  }

}
