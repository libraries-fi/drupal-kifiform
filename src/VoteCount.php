<?php

namespace Drupal\kifiform;

use Drupal\Core\TypedData\TypedData;

class VoteCount extends TypedData {
  protected $value = NULL;

  public function getValue($langcode = NULL) {
    if ($this->value === NULL) {
      $this->value = $this->parent->up + $this->parent->down;
    }

    return $this->value;
  }

  public function setValue($value, $notify = TRUE) {
    $this->value = $value;

    if ($notify && $parent = $this->getParent()) {
      $this->parent->onChange($this->name);
    }
  }
}
