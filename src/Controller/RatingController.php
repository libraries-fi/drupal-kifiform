<?php

namespace Drupal\kifiform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\kifiform\Form\RatingForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Drupal\Core\Form\FormState;

class RatingController extends ControllerBase {
  public static function create(ContainerInterface $container) {
    return new static;
  }

  public function __construct() {

  }

  public function vote(Request $request, $entity, $field) {
    $builder = \Drupal::service('form_builder');
    $field = $entity->get($field);

    if ($field->access('view') && $field->isUserAllowedToVote()) {
      $vote = $request->request->get('vote');
      $field->addVote($vote, TRUE);
      $entity->save();

      return new JsonResponse([
        'op' => $request->request->get('vote'),
        'up' => (int)$field->up,
        'down' => (int)$field->down,
        'value' => (int)$field->value,
      ]);
    } else {
      throw new AccessDeniedHttpException('Not allowed to vote right now.');
    }
  }
}
