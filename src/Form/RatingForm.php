<?php

namespace Drupal\kifiform\Form;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class RatingForm extends FormBase {
  protected $entity;
  protected $field;

  public function getFormId() {
    return 'kifiform_rating_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, ContentEntityInterface $answer = NULL, $field = NULL, $redirect_page = NULL) {
    $this->entity = $answer;
    $this->field = $field;
    $this->redirectPage = $redirect_page;

    $form['actions']['up'] = [
      '#type' => 'button',
      '#value' => $this->t('Up'),
      '#name' => 'vote_up',
    ];

    $form['actions']['down'] = [
      '#type' => 'button',
      '#value' => $this->t('Down'),
      '#name' => 'vote_down',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement() ?: ['#name' => null];
    $field = $this->entity->get($this->field);

    switch ($button['#name']) {
      case 'vote_up':
        $field->up++;
        break;

      case 'vote_down':
        $field->down++;
        break;
    }

    $this->entity->save();

    exit('submit form');
  }
}
