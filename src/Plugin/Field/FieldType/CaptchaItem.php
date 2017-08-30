<?php

namespace Drupal\kifiform\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Captcha verification for forms.
 *
 * @FieldType(
 *   id = "kifiform_captcha",
 *   label = @Translation("Captcha"),
 *   description = @Translation("An entity field that provides a captcha detection."),
 *   default_widget = "kifiform_captcha"
 * )
 */
class CaptchaItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['question'] = DataDefinition::create('string')
      ->setLabel(t('Question'))
      ->setComputed(TRUE);

    $properties['answers'] = DataDefinition::create('string')
      ->setLabel(t('Valid answers'))
      ->setComputed(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    return [
      'question' => 'Lorem ipsum?',
      'answers' => 'dolor|sit|amet'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'question';
  }
}
