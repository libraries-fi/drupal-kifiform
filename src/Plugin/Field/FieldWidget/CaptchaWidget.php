<?php

namespace Drupal\kifiform\Plugin\Field\FieldWidget;

use Drupal;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Simple captcha
 *
 * @FieldWidget(
 *   id = "kifiform_captcha",
 *   label = @Translation("Captcha"),
 *   field_types = {
 *     "kifiform_captcha"
 *   }
 * )
 */
class CaptchaWidget extends WidgetBase {
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    // Load this from a storage.
    $captcha = [
      'question' => t('How much is <b>:a plus :b</b>?', [':a' => t('six'), ':b' => t('three') ] ),
      'answers' => implode('|', [t('nine'), t('nine', [], ['context' => 'captcha answer #2'])]),
      'description' => t('Write the number as a word.'),
    ];

    $captcha += [
      'question' => '#captcha_not_configured#',
      'answers' => '',
      'description' => '',
    ];

    $element['#element_validate'][] = [get_class($this), 'validateFormElement'];
    $element['#attached']['library'][] = 'kifiform_captcha/captcha';
    $element['#attributes']['class'][] = 'kifi-captcha';
    $element['#required'] = TRUE;

    $element['question'] = [
      '#type' => 'item',
      '#title' => $this->t('Captcha'),
      '#markup' => $captcha['question'],
    ];

    $element['answers'] = [
      '#type' => 'value',
      '#value' => $captcha['answers'],
    ];

    $element['input'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your answer'),
      '#description' => $captcha['description'],
      '#required' => TRUE,
    ];

    $element['#access'] = static::isCaptchaRequired();

    return $element;
  }

  public static function validateFormElement(array &$element, FormStateInterface $form_state) {
    if (!isset($element['#access']) || $element['#access']) {
      $input = strtolower($element['input']['#value']);
      $answers = array_filter(explode('|', $element['answers']['#value']));
      $answer = array_map('strtolower', $answers);

      if (in_array($input, $answers, TRUE)) {
        static::setCaptchaDisabledForSession();
      } else {
        $form_state->setError($element, t('Invalid answer.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return $element['input'];
  }

  public static function setCaptchaDisabledForSession($state = TRUE) {
    $session = Drupal::service('user.private_tempstore')->get('kifiform_captcha');
    $session->set('captcha_validated', (bool)$state);
  }

  public static function isCaptchaRequired() {
    $session = Drupal::service('user.private_tempstore')->get('kifiform_captcha');
    $account = Drupal::currentUser();
    return !$session->get('captcha_validated') && !$account->isAuthenticated();
  }
}
