<?php

namespace Drupal\kifiform\Element;

/**
 * Interactive widget for content rating.
 *
 * @FormElement("kifiform_stars")
 */
class StarRating extends Rating {
  const VALUE_MAX = 100;
  const VALUE_MIN = 0;
  const STAR_COUNT = 5;

  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#theme' => 'kifiform_rating',
      '#multiple' => FALSE,
      '#extended' => FALSE,
      '#pre_render' => [
        [$class, 'preRenderRating'],
      ],
      '#theme' => 'kifiform_rating__stars',
    ];
  }

  public static function preRenderRating($element) {
    $stars = ceil(self::STAR_COUNT * ($element['#value'] - self::VALUE_MIN) / (self::VALUE_MAX - self::VALUE_MIN));
    $element['#stars'] = $stars;
    $element['#attached'] = ['library' => ['kifiform/rating--stars']];
    return $element;
  }
}
