<?php

namespace Drupal\moliek\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class PageController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\moliek\Form\CatForm');
    $build['content'] = [
      '#form' => $form,
      '#theme' => 'moliek_theme_hook',
      '#text' => $this->t('Hello! You can add here a photo of your cat.'),
    ];
    return $build;
  }

}
