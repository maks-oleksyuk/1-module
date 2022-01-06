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
    $build['content'] = [
      '#form' => $form,
      '#theme' => 'moliek_theme',
      '#text' => $this
        ->t('Hello! You can add here a photo of your cat.'),
    ];
    $header_title = [
      'name' => t('Name'),
      'email' => t('Email'),
      'img' => t('Photo'),
      'created' => t('Date Created'),
    ];
    $cats['table'] = [
      '#type' => 'table',
      '#header' => $header_title,
      '#rows' => $this->getCats(),
    ];
    return [$form, $cats];
  }

  /**
   * Getting data from the moliek table.
   */
  public function getCats() {
    $database = \Drupal::database();
    $result = $database->select('moliek', 'm')
      ->fields('m', ['cat_name', 'email', 'cat_img', 'created'])
      ->orderBy('created', 'DESC')
      ->execute();
    $cats = [];
    foreach ($result as $cat) {
      $cats[] = [
        'name' => $cat->cat_name,
        'email' => $cat->email,
        'img' => File::load($cat->cat_img)->getFileUri(),
        'created' => $cat->created,
      ];
    }
    return $cats;
  }

}
