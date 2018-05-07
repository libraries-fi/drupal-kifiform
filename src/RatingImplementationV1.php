<?php

namespace Drupal\kifiform;

use Drupal\Core\TypedData\TypedData;

/**
 * Calculate special rating based on item likes and dislikes.
 */
class RatingImplementationV1 extends TypedData {
  protected $rating = NULL;

  public function getValue($langcode = NULL) {
    if ($this->rating === NULL && $this->parent->votes > 0) {
      $this->rating = static::computeRating($this->parent->up, $this->parent->down);
    }

    return $this->rating;
  }

  public function setValue($value, $notify = TRUE) {
    $this->rating = $value;

    if ($notify && $parent = $this->getParent()) {
      $this->parent->onChange($this->name);
    }
  }

  /**
   * @param $votes Total number of votes cast.
   * @param $points Sum of points. (Each vote can be worth of many points, positive or negative.)
   */
  public static function computeRating($likes, $dislikes) {
    if ($likes + $dislikes > 0) {
      $likes = $likes * 1.2 + 1;
      $dislikes += 1;
      $total = $likes + $dislikes;
      $rating = 50 + ($likes - $dislikes) / $total * 50;
    } else {
      $rating = 50;
    }

    return $rating;
  }
}
