<?php

namespace Drupal\kifiform\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;

trait EntityLinkFormatterTrait {

  public static function defaultSettings() {
    $options = parent::defaultSettings();
    $options['route_override'] = '';
    $options['query_variables'] = '';
    return $options;
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['route_override'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Override default route'),
      '#description' => $this->t('Custom route must accept same parameters as the original.'),
      '#default_value' => $this->getSetting('route_override'),
      '#attributes' => [
        'placeholder' => $this->t('e.g. entity.%type.canonical', ['%type' => $this->fieldDefinition->getTargetEntityTypeId()]),
      ],
    ];

    $form['route_parameters'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Add route parameters'),
      '#description' => $this->t('Values are defined as key=value pairs. One definition per row.'),
      '#default_value' => $this->getSetting('route_parameters'),
    ];

    $form['query_variables'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Add query variables'),
      '#description' => $this->t('Values are defined as key=value pairs. One definition per row.'),
      '#default_value' => $this->getSetting('query_variables'),
    ];

    // This formatter is intended for outputting links only.
    unset($form['link_to_entity']);

    return $form;
  }

  public function settingsSummary() {
    $summary = [];

    if ($route = $this->getSetting('route_override')) {
      $summary[] = $this->t('Linked to route @route', ['@route' => $route]);
    }

    if ($this->getSetting('route_parameters')) {
      $summary[] = $this->t('Using custom route parameters');
    }

    if ($this->getSetting('query_variables')) {
      $summary[] = $this->t('Using custom query variables');
    }
    return $summary;
  }
}
