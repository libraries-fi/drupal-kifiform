<?php

namespace Drupal\kifiform\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'kifiform_entity_link' formatter.
 *
 * @FieldFormatter(
 *   id = "kifiform_entity_link",
 *   label = @Translation("Link to entity"),
 *   field_types = {
 *     "string",
 *     "uri",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class EntityLink extends StringFormatter {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $options = parent::defaultSettings();
    $options['route_override'] = '';
    $options['query_variables'] = '';
    return $options;
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    unset($form['link_to_entity']);

    $form['link_options'] = [
      '#type' => 'details',
      '#title' => $this->t('Link settings'),
      '#open' => TRUE,
      'route_override' => [
        '#type' => 'textfield',
        '#title' => $this->t('Override default route'),
        '#description' => $this->t('Custom route must accept same parameters as the original.'),
        '#default_value' => $this->getSetting('route_override'),
        '#attributes' => [
          'placeholder' => $this->t('e.g. entity.%type.canonical', ['%type' => $this->fieldDefinition->getTargetEntityTypeId()]),
        ],
      ],
      'route_parameters' => [
        '#type' => 'textarea',
        '#title' => $this->t('Add route parameters'),
        '#description' => $this->t('Values are defined as key=value pairs. One definition per row.'),
        '#default_value' => $this->getSetting('route_parameters'),
      ],
      'query_variables' => [
        '#type' => 'textarea',
        '#title' => $this->t('Add query variables'),
        '#description' => $this->t('Values are defined as key=value pairs. One definition per row.'),
        '#default_value' => $this->getSetting('query_variables'),
      ],
      'debug' => [
        '#type' => 'textarea',
        '#default_value' => implode("\n", $this->getSettings()),
      ]
    ];

    $form['route_override'] = $form['link_options']['route_override'];
    $form['route_parameters'] = $form['link_options']['route_parameters'];
    $form['query_variables'] = $form['link_options']['query_variables'];

    unset($form['link_options']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $url = $items->getEntity()->toUrl('revision');

    if ($route = $this->getSetting('route_override')) {
      $url = new Url($route, $url->getRouteParameters());
    }

    if ($variables = $this->getSetting('query_variables')) {
      $query = $url->getOption('query') ?: [];
      foreach (explode("\n", $variables) as $row) {
        list($key, $value) = explode('=', trim($row));
        $query[$key] = $value;
      }
      $url->setOption('query', $query);
    }

    foreach ($items as $delta => $item) {
      $view_value = $this->viewValue($item);
      if ($url) {
        $elements[$delta] = [
          '#type' => 'link',
          '#title' => $view_value,
          '#url' => $url,
        ];
      }
      else {
        $elements[$delta] = $view_value;
      }
    }
    return $elements;
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
