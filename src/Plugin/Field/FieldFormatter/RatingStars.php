<?php

namespace Drupal\kifiform\Plugin\Field\FieldFormatter;

use Drupal;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\kifiform\Form\RatingForm;

use Drupal\Core\Form\FormState;

/**
 * Display files in the search results.
 * Aggregate rating is an integer value between 9-100.
 *
 * @FieldFormatter(
 *  id = "kifiform_rating_stars",
 *  label = @Translation("Stars"),
 *  field_types = {
 *    "kifiform_rating"
 *  }
 * )
 */
class RatingStars extends RatingFilledBar {
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    foreach ($items as $delta => $item) {
      $entity = $item->getEntity();
      $field = $item->getFieldDefinition()->getName();

      $elements[$delta]['rating']['#theme'] = 'kifiform_rating__stars';
      $elements[$delta]['rating']['#attached'] = ['library' => ['kifiform/rating--stars']];
      $elements[$delta]['rating']['#stars'] = ceil($item->value / 20);
    }

    return $elements;
  }
}
