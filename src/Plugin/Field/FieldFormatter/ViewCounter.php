<?php

namespace Drupal\kifiform\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;
use Drupal\Core\Field\Plugin\Field\FieldWidget;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\IntegerFormatter;


/**
 * Plugin implementation of the 'number_unformatted' formatter.
 *
 * @FieldFormatter(
 *   id = "kifiform_view_counter",
 *   label = @Translation("View counter"),
 *   field_types = {
 *     "kifiform_view_counter"
 *   }
 * )
 */
class ViewCounter extends IntegerFormatter {
  public function viewElements(FieldItemListInterface $items, $langcode) {
    if (!count($items)) {
      $items->appendItem(0);
    }

    $type_id = $items->getEntity()->getEntityTypeId();
    $entity_id = $items->getEntity()->id();
    $field_id = $items->getFieldDefinition()->getName();

    $url = Url::fromRoute('kifiform.view_counter', [
      'entity_type' => $type_id,
      'entity_id' => $entity_id,
      'field' => $field_id,
    ]);

    $elements = parent::viewElements($items, $langcode);
    $elements['#attached']['library'][] = 'kifiform/view-counter';
    $elements['#attributes']['data-view-counter-path'] = $url->toString();

    return $elements;
  }

  protected function numberFormat($number) {
    $number = parent::numberFormat($number);
    return sprintf('<span>%s</span>', $number);
  }
}
