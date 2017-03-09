<?php

namespace Drupal\kifiform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ViewCounterController extends ControllerBase {
  protected $entities;
  protected $store;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('user.private_tempstore')
    );
  }

  public function __construct(EntityTypeManagerInterface $entities, PrivateTempStoreFactory $store) {
    $this->entities = $entities;
    $this->store = $store;
  }

  public function view($entity_type, $entity_id, $field) {
    $session = $this->store->get('ajax_views');
    $cid = sprintf('%s.%s.%s', $entity_type, $entity_id, $field);
    $view_count = $session->get($cid);

    if (!$view_count) {
      $entity = $this->entities->getStorage($entity_type)->load($entity_id);

      if ($this->isAllowed($entity, $field)) {
        $view_count = (int)$entity->get($field)->value + 1;
        $entity->set($field, $view_count)->save();
        $session->set($cid, $view_count);
        return new JsonResponse(['status' => 'ok', 'views' => $view_count]);
      }
    } else {
      return new JsonResponse(['status' => 'noop']);
    }
  }

  protected function isAllowed(FieldableEntityInterface $entity, $field) {
    /*
     * NOTE: Do not allow writes to an unknown field!
     */
    if ($entity->access('view') && $entity->get($field)->getFieldDefinition()->getType() == 'kifiform_view_counter') {
      return TRUE;
    }

    throw new AccessDeniedHttpException;
  }
}
