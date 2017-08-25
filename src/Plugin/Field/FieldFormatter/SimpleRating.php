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
 * Display files in the search results
 *
 * @FieldFormatter(
 *  id = "kifiform_rating_simple",
 *  label = @Translation("Star rating with thumbs"),
 *  field_types = {
 *    "kifiform_rating"
 *  }
 * )
 */
class SimpleRating extends FormatterBase {
  public function viewElements(FieldItemListInterface $items, $langcode) {
    if (!count($items)) {
      $items->appendItem();
    }

    $elements = [];
    $current_page = \Drupal::requestStack()->getCurrentRequest()->getUri();


    foreach ($items as $delta => $item) {
      $entity = $item->getEntity();
      $field = $item->getFieldDefinition()->getName();

      $elements[$delta] = [
        'rating' => [
          '#cache' => [
            'max-age' => 0
          ],
          '#theme' => 'kifiform_rating',
          '#display_votes' => $this->getSetting('display_votes'),
          '#rating' => $item->value,
          '#up' => $item->up,
          '#down' => $item->down,
          '#attached' => [
            'library' => ['kifiform/rating']
          ],
        ]
      ];

      if ($this->getSetting('enable_voting') && $item->isUserAllowedToVote()) {
        $form_url = Url::fromRoute('kifiform.vote', [
          'entity_type' => $item->getEntity()->getEntityTypeId(),
          'entity' => $item->getEntity()->id(),
          'field' => $item->getFieldDefinition()->getName()
        ]);

        $elements[$delta]['voting'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['rating-form'],
            'data-url' => $form_url->toString(),
          ],
          '#attached' => [
            'library' => ['kifiform/rating-ajax']
          ],
          'form' => [
            '#theme' => 'kifiform_rating_thumbs',
          ]
        ];
      }
    }

    return $elements;
  }

  public function isEmpty() {
    return FALSE;
  }

  public static function defaultSettings() {
    return [
      'enable_voting' => FALSE,
      'display_votes' => FALSE,
    ] + parent::defaultSettings();
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['enable_voting'] = [
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('enable_voting'),
      '#title' => $this->t('Enable voting'),
    ];
    $form['display_votes'] = [
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('display_votes'),
      '#title' => $this->t('Display vote count'),
    ];

    return $form;
  }
}
