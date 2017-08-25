<?php

namespace Drupal\kifiform\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\TypedData\TypedDataInterface;

class RatingFieldItemList extends FieldItemList {
  public function isUserAllowedToVote() {
    return $this->first()->isUserAllowedToVote() && $this->access('view');
  }

  public function addVote($vote, $lock_session = FALSE) {
    $this->first()->addVote($vote, $lock_session);
  }

  public function defaultValuesForm(array &$form, FormStateInterface $form_state) {

  }

  public function __construct($definition, $name = NULL, TypedDataInterface $parent = NULL) {
    parent::__construct($definition, $name, $parent);
    $this->appendItem();
  }
}
