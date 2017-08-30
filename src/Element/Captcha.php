<?php

namespace Drupal\kifiform\Element;

use Drupal\Core\Render\Element\FormElement;

/**
 * Simple captcha element
 *
 * @FormElement("kifiform_captcha")
 */
class Captcha extends FormElement {
  public function getInfo() {
    return [
      '#theme' => 'kifiform_captcha',
      '#input' => TRUE,
      '#multiple' => FALSE,
      '#extended' => FALSE,
      '#attached' => [
        'library' => ['kifiform_captcha/captcha']
      ],
    ];
  }
}
