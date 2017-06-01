<?php

namespace Drupal\kifiform\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'kifiform_entity_link' formatter.
 *
 * @FieldFormatter(
 *   id = "kifiform_entity_reference_link",
 *   label = @Translation("Link to entity (2)"),
 *   field_types = {
 *     "entity_reference",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class EntityReferenceLinkFormatter extends EntityReferenceFormatterBase {
  use EntityLinkFormatterTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $this->routeMatch = \Drupal::service('current_route_match');

    $elements = array();

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $url = $entity->toUrl('revision');

      if ($route = $this->getSetting('route_override')) {
        $url = new Url($route, $url->getRouteParameters());
      }

      if ($variables = $this->getSetting('query_variables')) {
        $route_parameters = [];
        foreach ($this->routeMatch->getRawParameters() as $key => $value) {
          $route_parameters[':' . $key] = $value;
        }
        $variables = $this->t($variables, $route_parameters);
        $query = $url->getOption('query') ?: [];
        foreach (explode("\n", $variables) as $row) {
          list($key, $value) = explode('=', trim($row));
          $query[$key] = $value;
        }
        $url->setOption('query', $query);
      }
      if ($url) {
        $elements[$delta] = [
          '#type' => 'link',
          '#title' => $entity->label(),
          '#url' => $url,
        ];
      }
      else {
        $elements[$delta] = $view_value;
      }
    }
    return $elements;
  }
}
