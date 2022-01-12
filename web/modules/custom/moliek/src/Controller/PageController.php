<?php

namespace Drupal\moliek\Controller;

use Drupal\file\Entity\File;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the moliek module.
 */
class PageController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {
    $form = \Drupal::formBuilder()
      ->getForm('Drupal\moliek\Form\CatForm');
    $header_title = [
      'img' => $this->t('Photo'),
      'name' => $this->t('Name'),
      'email' => $this->t('User’s e-mail'),
      'created' => $this->t('Date Created'),
    ];
    $cats['table'] = [
      '#type' => 'table',
      '#header' => $header_title,
      '#rows' => $this->getCats(),
      '#empty' => $this->t('There are no items to display.')
    ];
    $build['content'] = [
      '#form' => $form,
      '#theme' => 'moliek_theme',
      '#text' => $this->t('Hello! You can add here a photo of your cat.'),
      '#cats' => $cats,
    ];
    return $build;
  }

  /**
   * Getting data from the moliek table.
   */
  public function getCats() {
    $database = \Drupal::database();
    $result = $database->select('moliek', 'm')
      ->fields('m', ['id', 'cat_name', 'email', 'cat_img', 'created'])
      ->orderBy('created', 'DESC')
      ->execute();
    $cats = [];
    foreach ($result as $cat) {
      $cats[] = [
        'id' => $cat->id,
        'img' => File::load($cat->cat_img)->createFileUrl(),
        'name' => $cat->cat_name,
        'email' => $cat->email,
        'created' => $cat->created,
      ];
    }
    return $cats;
  }

}
