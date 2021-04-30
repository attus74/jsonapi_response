<?php

namespace Drupal\jsonapi_response;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\jsonapi\Exception\EntityAccessDeniedHttpException;
use Drupal\jsonapi\Access\EntityAccessChecker;
use Drupal\jsonapi\Normalizer\NormalizerBase;
use Drupal\jsonapi\JsonApiResource\ResourceObjectData;
use Drupal\jsonapi\JsonApiResource\JsonApiDocumentTopLevel;
use Drupal\jsonapi\JsonApiResource\NullIncludedData;
use Drupal\jsonapi\JsonApiResource\LinkCollection;
use Drupal\jsonapi\JsonApiResource\TopLevelDataInterface;
use Drupal\jsonapi\CacheableResourceResponse;
use Drupal\jsonapi_response\Exception\NoEntityException;

/**
 * Entity
 *
 * @author Attila Németh
 * 26.02.2021
 */
class Entity {
  
  // JSON:APi Access Checker
  private     $_jsonApiAccessChecker;
  
  // JSON:API Normalizer
  private     $_jsonApiNormalizer;
  
  // A Drupal Entity
  private     $_entityKey;
  
  public function __construct(EntityAccessChecker $accessChecker, NormalizerBase $normalizer) {
    $this->_jsonApiAccessChecker = $accessChecker;
    $this->_jsonApiNormalizer = $normalizer;
  }
  
  public function entityIndividualResponse(EntityInterface $entity)
  {
    $resource = $this->_jsonApiAccessChecker->getAccessCheckedResourceObject($entity);
    if ($resource instanceof EntityAccessDeniedHttpException) {
      throw $resource;
    }
    $data = new ResourceObjectData([$resource], 1);
    $this->_entityKey = $entity->getEntityTypeId() . ':' . $entity->id();
    return $this->_entityDataResponse($data);
  }
  
  /**
   * A collection of entities as JSON:API Response
   * @param array $entities
   *  The entities
   * @return CacheableResponseInterface
   * @throws NoEntityException
   */
  public function entityCollectionResponse(array $entities): CacheableResponseInterface 
  {
    foreach($entities as $entity) {
      if (!$entity instanceof EntityInterface) {
        throw new NoEntityException(); 
      }
    }
    $resources = [];
    $this->_entityKey = NULL;
    foreach ($entities as $entity) {
      if (is_null($this->_entityKey)) {
        $this->_entityKey = $entity->getEntityTypeId();
      }
      $this->_entityKey .= ':' . $entity->id();
      $resources[$entity->id()] = $this->_jsonApiAccessChecker->getAccessCheckedResourceObject($entity);
    }
    $data = new ResourceObjectData($resources); 
    return $this->_entityDataResponse($data);
  }
  
  /**
   * The actual data response
   * @param TopLevelDataInterface $data
   * @return CacheableResponseInterface
   */
  private function _entityDataResponse(TopLevelDataInterface $data): CacheableResponseInterface
  {
    $document = new JsonApiDocumentTopLevel($data, new NullIncludedData(), new LinkCollection([]), []);
    $response = new CacheableResourceResponse($document, 200);
    $response->addCacheableDependency($this->_entityKey);
    return $response;
  }
  
}
