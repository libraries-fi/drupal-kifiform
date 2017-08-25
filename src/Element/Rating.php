<?php

namespace Drupal\kifiform\Element;

use Drupal\Core\Render\Element\FormElement;

/**
 * Interactive widget for content rating.
 *
 * @FormElement("kifiform_rating")
 */
class Rating extends FormElement {
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#theme' => 'kifiform_rating',
      '#multiple' => FALSE,
      '#extended' => FALSE
    ];
  }
}
