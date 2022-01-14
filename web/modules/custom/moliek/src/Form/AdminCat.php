<?php

namespace Drupal\moliek\Form;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements administration page for cats.
 */
class AdminCat extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm($form, FormStateInterface $form_state): array {
    $header_title = [
      'id' => $this->t('ID'),
      'img' => $this->t('Photo'),
      'name' => $this->t('Name'),
      'email' => $this->t('User’s e-mail'),
      'created' => $this->t('Date Created'),
      'edit' => $this->t('Edit'),
      'delete' => $this->t('Delete'),
    ];
    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => $header_title,
      '#options' => $this->getCats(),
      '#empty' => $this->t('There are no items to display.'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
    ];
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() : string {
    return "moliek_admin_cat";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('moliek.adminCats');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('You really want to delete selected cat(s)?');
  }

  /**
   * Function for creating module.
   */
  public function getCats() : array {
    $database = \Drupal::database();
    $result = $database->select('moliek', 'm')
      ->fields('m', ['id', 'cat_name', 'email', 'cat_img', 'created'])
      ->orderBy('id', 'DESC')
      ->execute();
    $cats = [];
    foreach ($result as $cat) {
      $cats[] = [
        'id' => $cat->id,
        'img' => [
          'data' => [
            '#theme' => 'image_style',
            '#style_name' => 'thumbnail',
            '#uri' => File::load($cat->cat_img)->getFileUri(),
            '#attributes' => [
              'alt' => $cat->cat_name,
              'title' => $cat->cat_name,
            ],
          ],
        ],
        'name' => $cat->cat_name,
        'email' => $cat->email,
        'created' => date('d-m-y H:i:s', $cat->created),
        'edit' => [
          'data' => [
            '#type' => 'link',
            '#title' => $this->t('Edit'),
            '#url' => Url::fromRoute('moliek.catEdit', ['id' => $cat->id]),
            '#options' => [
              'attributes' => [
                'class' => 'use-ajax edit',
                'data-dialog-type' => 'modal',
                'data-dialog-options' => '{"width": 700, "title":"Edit cat information"}',
              ],
            ],
          ],
        ],
        'delete' => [
          'data' => [
            '#type' => 'link',
            '#title' => $this->t('Delete'),
            '#url' => Url::fromRoute('moliek.catDelete', ['id' => $cat->id]),
            '#options' => [
              'attributes' => [
                'class' => 'use-ajax delete',
                'data-dialog-type' => 'modal',
                'data-dialog-options' => '{"width": 500}',
              ],
            ],
          ],
        ],
      ];
    }
    return $cats;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $rows = $form_state->getCompleteForm()['table']['#value'];
    if ($rows) {
      $id = [];
      foreach ($rows as $i) {
        $id[] = $form['table']['#options'][$i]['id'];
      }
      $database = \Drupal::database();
      $database->delete('moliek')
        ->condition('id', $id, 'IN')
        ->execute();
      \Drupal::messenger()->addStatus('Successfully deleted.');
    }
    else {
      $this->messenger()->addMessage($this->t("No rows selected to delete"), 'error');
    }
  }

}
