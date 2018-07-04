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
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL) {
    $element = parent::form($items, $form, $form_state, $get_delta);
    $element['#process'][] = [get_class($this), 'processCaptcha'];
    return $element;
  }

  public static function processCaptcha(array &$element, FormStateInterface $form_state) {
    if (static::isCaptchaRequired()) {
      return $element;
    }
  }

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // FIXME: Load this from a storage.
    $captcha = [
      'question' => t('How much is <b>:a plus :b</b>?', [':a' => t('five'), ':b' => t('eight') ] ),
      // 'answer' => [t('nine'), t('nine', [], ['context' => 'captcha answer #2'])],
      'answer' => [t('thirteen')],
      'description' => t('Write the number as a word.'),
    ];

    $captcha += [
      'question' => '#captcha_not_configured#',
      'answer' => '',
      'description' => '',
    ];

    $element['#element_validate'][] = [get_class($this), 'validateFormElement'];
    $element['#attached']['library'][] = 'kifiform/captcha';
    $element['#attributes']['class'][] = 'kifi-captcha';
    $element['#required'] = TRUE;
    $element['#process'][] = [get_class($this), 'processQuestionLabel'];

    $element['question'] = [
      '#type' => 'container',
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $captcha['question'],
      ],
    ];

    $element['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your answer'),
      '#description' => $captcha['description'],
      '#required' => TRUE,
    ];

    $element['answer'] = [
      '#type' => 'value',
      '#value' => $captcha['answer'],
    ];

    return $element;
  }

  public static function processQuestionLabel(array $element) {
    $element['input']['#attributes']['aria-labelledby'] = sprintf('%s-question', $element['#id']);
    return $element;
  }

  public static function validateFormElement(array &$element, FormStateInterface $form_state) {
    if (!isset($element['#access']) || $element['#access']) {
      $input = $form_state->getValue(array_merge($element['#parents'], ['value']));
      $allowed_values = $element['answer']['#value'];

      foreach ($allowed_values as $answer) {
        // Values are translatable objects so cast them to strings.
        if ((string)$answer == (string)$input) {
          static::setCaptchaDisabledForSession();
          return;
        }
      }

      $form_state->setError($element, t('Invalid answer.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return $element['input'];
  }

  public static function setCaptchaDisabledForSession($state = TRUE) {
    $session = Drupal::service('tempstore.private')->get('kifiform_captcha');
    $session->set('captcha_validated', (bool)$state);
  }

  public static function isCaptchaRequired() {
    $session = Drupal::service('tempstore.private')->get('kifiform_captcha');
    $account = Drupal::currentUser();

    if ($account->isAuthenticated()) {
      return FALSE;
    }

    return !$session->get('captcha_validated');
  }
}
