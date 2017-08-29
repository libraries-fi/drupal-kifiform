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
 *   default_formatter = "kifiform_rating_bar",
 * )
 */
class RatingItem extends FieldItemBase {
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['votes'] = DataDefinition::create('integer')
      ->setLabel(t('Total number of votes'));
    $properties['up'] = DataDefinition::create('integer')
      ->setLabel(t('Total likes'))
      ->setDescription('Number of up votes.')
      ->setRequired(TRUE);
    $properties['down'] = DataDefinition::create('integer')
      ->setLabel(t('Total dislikes'))
      ->setDescription('Number of down votes.')
      ->setRequired(TRUE);
    $properties['value'] = DataDefinition::create('integer')
      ->setLabel(t('Rating'))
      ->setDescription('Aggregate rating.')
      ->setComputed(TRUE)
      ->setClass(RatingImplementationV1::class);
    $properties['last_vote'] = DataDefinition::create('timestamp')
      ->setLabel('Last vote')
      ->setDescription("Timestamp for latest vote");
    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'description' => 'Computed content quality rating.',
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => TRUE,
          'not null' => FALSE,
        ],
        'votes' => [
          'description' => 'Total number of votes.',
          'type' => 'int',
          'default' => 0,
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
        'up' => [
          'description' => 'Amount of likes.',
          'type' => 'int',
          'default' => 0,
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
        'down' => [
          'description' => 'Amount of dislikes.',
          'type' => 'int',
          'default' => 0,
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
        'last_vote' => [
          'description' => 'Time of latest vote.',
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
        ]
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
    $key = sprintf('%s.%s.%s', $this->getEntity()->getEntityTypeId(), $this->getFieldDefinition()->getName(), $this->getEntity()->id());
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
      $key = sprintf('%s.%s.%s', $this->getEntity()->getEntityTypeId(), $this->getFieldDefinition()->getName(), $this->getEntity()->id());
      $store->set($key, TRUE);
    }
  }

  public function isEmpty() {
    return FALSE;
  }

  public function onChange($property_name, $notify = TRUE) {
    if ($property_name == 'up' || $property_name == 'down') {
      // Enforce re-calculation of aggregate rating.
      $this->value = NULL;

      $this->votes++;
      $this->last_vote = date();
    }
    parent::onChange($property_name, $notify);
  }
}
