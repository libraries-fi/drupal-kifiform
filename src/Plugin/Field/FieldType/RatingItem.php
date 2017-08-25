<?php

namespace Drupal\kifiform\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\kifiform\RatingImplementationV1;
use Drupal\kifiform\VoteCount;

use Drupal\Core\Field\Plugin\Field\FieldType\IntegerItem;

/**
 * Stores a rating for the entity.
 *
 * @FieldType(
 *   id = "kifiform_rating",
 *   label = @Translation("Rating"),
 *   description = @Translation("Rating system for content"),
 *   module = "kifiform",
 *   list_class = "Drupal\kifiform\Plugin\Field\FieldType\RatingFieldItemList",
 *   default_widget = "number",
 *   default_formatter = "kifiform_rating_simple",
 * )
 */
class RatingItem extends FieldItemBase {
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['votes'] = DataDefinition::create('integer')
      ->setLabel(t('Number of votes'))
      ->setComputed(TRUE)
      ->setClass(VoteCount::class);
    $properties['up'] = DataDefinition::create('integer')
      ->setLabel(t('Total likes'))
      ->setRequired(TRUE);
    $properties['down'] = DataDefinition::create('integer')
      ->setLabel(t('Total dislikes'))
      ->setRequired(TRUE);
    $properties['value'] = DataDefinition::create('integer')
      ->setLabel(t('Rating'))
      ->setComputed(TRUE)
      ->setClass(RatingImplementationV1::class);
    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'up' => [
          'description' => 'Total amount of likes.',
          'type' => 'int',
          'default' => 0,
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
        'down' => [
          'description' => 'Total amount of dislikes.',
          'type' => 'int',
          'default' => 0,
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
        'value' => [
          'description' => 'Special rating calculated from cast votes.',
          'type' => 'int',
          'size' => 'tiny',
          'default' => 50,
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
      ],
      'indexes' => [
        'value' => ['value'],
      ]
    ];
  }

  public static function defaultFieldSettings() {
    return [
      'min' => 0,
      'max' => 100,
      // 'prefix' => '',
      // 'suffix' => '',
    ] + parent::defaultFieldSettings();
  }

  public function isUserAllowedToVote() {
    $store = \Drupal::service('user.private_tempstore')->get('kifiform');
    $key = sprintf('%s.%s', $this->getEntity()->getEntityTypeId(), $this->getFieldDefinition()->getName());

    return !$store->get($key);
  }

  public function addVote($vote, $lock_session = FALSE) {
    if ($vote == 'up') {
      $this->up++;
    } elseif ($vote == 'down') {
      $this->down++;
    }

    if ($lock_session) {
      $store = \Drupal::service('user.private_tempstore')->get('kifiform');
      $key = sprintf('%s.%s', $this->getEntity()->getEntityTypeId(), $this->getFieldDefinition()->getName());
      $store->set($key, TRUE);
    }
  }

  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    exit('sample');
  }

  public function isEmpty() {
    return FALSE;
  }

  public function onChange($property_name, $notify = TRUE) {
    // Enforce re-calculation of aggregate rating.
    if ($property_name == 'up' || $property_name == 'down') {
      $this->value = NULL;
    }
    parent::onChange($property_name, $notify);
  }
}
