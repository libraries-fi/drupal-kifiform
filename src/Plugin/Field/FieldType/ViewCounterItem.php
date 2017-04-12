<?php

namespace Drupal\kifiform\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\Plugin\Field\FieldType\IntegerItem;

/**
 * Ajax-based view counter that works regardless of view caching.
 *
 * @FieldType(
 *   id = "kifiform_view_counter",
 *   label = @Translation("View counter"),
 *   description = @Translation("Provides JS-based view counter."),
 *   default_formatter = "kifiform_view_counter",
 *   default_widget = "number"
 * )
 */
class ViewCounterItem extends IntegerItem {
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);
    $properties['value']->setLabel(t('View count'));
    return $properties;
  }

  public static function defaultFieldSettings() {
    return ['min' => 0, 'suffix' => ' views'] + parent::defaultFieldSettings();
  }

  public static function defaultStorageSettings() {
    return ['unsigned' => TRUE, 'size' => 'big'] + parent::defaultStorageSettings();
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $schema['columns']['value']['default'] = 0;
    $schema['columns']['value']['null'] = FALSE;
    return $schema;
  }
}
