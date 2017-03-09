<?php

namespace Drupal\kifiform\Element;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
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
      '#element_validate' => [
        [get_class($this), 'validateCaptcha'],
      ],
      '#attached' => [
        'library' => ['kifiform_captcha/captcha']
      ],
    ];
  }

  public static function validateCaptcha(&$element, FormStateInterface $form_state, &$form) {

  }
}
