<?php

namespace Drupal\moliek\Controller;

use Drupal\Core\Controller\ControllerBase;


class PageController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build(){

    $build['content'] = [
      '#theme' => 'moliek_theme_hook',
      '#text' => $this->t('Hello! You can add here a photo of your cat.'),
    ];

    return $build;
  }

}
