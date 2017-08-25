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
      $this->rating = $this->computeRating($this->parent->up, $this->parent->down);
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
  protected function computeRating($likes, $dislikes) {
    // Using algorithm picked from this post:
    // http://www.evanmiller.org/how-not-to-sort-by-average-rating.html

    $total = $likes + $dislikes;
    $rating = (($likes + 1.9208) / $total - 1.96 * sqrt(($likes * $dislikes) / $total + 0.9604) / $total) / (1 + 3.8416 / $total) * 100;
    return $rating;
  }
}
