<?php

namespace Drupal\jsonapi_response_test\Controller;

use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\jsonapi_response\JsonapiEntityResponseInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Node extends ControllerBase {

  private     $_entityManager;
  private     $_jsonApiResponse;

  public function __construct(EntityTypeManagerInterface $entityManager,
                                  JsonapiEntityResponseInterface $jsonApiResponse)
  {
    $this->_entityManager = $entityManager;
    $this->_jsonApiResponse = $jsonApiResponse;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('jsonapi_response.entity'),
    );
  }

  public function getIndividual(): CacheableResponseInterface
  {
    $node = $this->_entityManager->getStorage('node')->load(1);
    return $this->_jsonApiResponse->entityIndividualResponse($node, ['uid']);
  }

}
