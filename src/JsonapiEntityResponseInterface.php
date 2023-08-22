<?php

namespace Drupal\jsonapi_response;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Cache\CacheableResponseInterface;;

/**
 * JSON:API Response Entity Interface 
 * 
 * @author Attila Németh
 * @date 22.08.2023
 */
interface JsonapiEntityResponseInterface {
  
  /**
   * An individual Entity in JSON:API format
   * @param EntityInterface $entity
   *  The original Entity
   * @return CacheableResponseInterface
   */
  public function entityIndividualResponse(EntityInterface $entity): CacheableResponseInterface;
  
  /**
   * A collection of entities as JSON:API Response
   * @param array $entities
   *  Data entities
   * @param array $includeFields
   *  Included Fields
   * @return CacheableResponseInterface
   * @throws NoEntityException
   */
  public function entityCollectionResponse(array $entities, array $includeFields = NULL): CacheableResponseInterface;
  
}
