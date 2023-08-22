<?php

namespace Drupal\jsonapi_response;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\jsonapi\Exception\EntityAccessDeniedHttpException;
use Drupal\jsonapi\Access\EntityAccessChecker;
use Drupal\jsonapi\IncludeResolver;
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
class Entity implements JsonapiEntityResponseInterface {
  
  // JSON:APi Access Checker
  private     $_jsonApiAccessChecker;
  
  // JSON:API Normalizer
  private     $_jsonApiNormalizer;
  
  // JSON:API Include Resolver
  private     $_includeResolver;
  
  // A unique key built of the entities
  private     $_entityKey;
  
  public function __construct(EntityAccessChecker $accessChecker, NormalizerInterface $normalizer, IncludeResolver $includeResolver) {
    $this->_jsonApiAccessChecker = $accessChecker;
    $this->_jsonApiNormalizer = $normalizer;
    $this->_includeResolver = $includeResolver;
  }
  
  /**
   * {@inheritDoc}
   * @param EntityInterface $entity
   * @return CacheableResponseInterface
   * @throws @var:$this@fld:_jsonApiAccessChecker@mtd:getAccessCheckedResourceObject
   */
  public function entityIndividualResponse(EntityInterface $entity): CacheableResponseInterface 
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
   *  Data entities
   * @param array $includeFields
   *  Included Fields
   * @return CacheableResponseInterface
   * @throws NoEntityException
   */
  public function entityCollectionResponse(array $entities, array $includeFields = NULL): CacheableResponseInterface 
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
    if (!is_null($includeFields)) {
      $includes = $this->_includeResolver->resolve($data, implode(',', $includeFields));
    }
    else {
      $includes = NULL;
    }
    return $this->_entityDataResponse($data, $includes);
  }
  
  /**
   * The actual data response
   * @param TopLevelDataInterface $data
   * @return CacheableResponseInterface
   */
  private function _entityDataResponse(TopLevelDataInterface $data, ResourceObjectData $includes = NULL): CacheableResponseInterface
  {
    if (is_null($includes)) {
      $includes = new NullIncludedData();
    }
    $document = new JsonApiDocumentTopLevel($data, $includes, new LinkCollection([]), []);
    $response = new CacheableResourceResponse($document, 200);
    $response->addCacheableDependency($this->_entityKey);
    return $response;
  }
  
}