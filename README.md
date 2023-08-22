# JSON:API Response

A Drupal 10 module for custom JSON:API Entity responses.

[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg)](https://GitHub.com/attus74/jsonapi_response/graphs/commit-activity)
[![GitHub license](https://img.shields.io/github/license/attus74/jsonapi_response.svg)](https://github.com/attus74/jsonapi_response/blob/master/LICENSE)
[![GitHub release](https://img.shields.io/github/release/attus74/jsonapi_response.svg)](https://GitHub.com/attus74/jsonapi_response/releases/)
[![GitHub issues](https://img.shields.io/github/issues/attus74/jsonapi_response.svg)](https://GitHub.com/attus74/jsonapi_response/issues/)

## Usage

```php

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\jsonapi_response\JsonapiEntityResponseInterface;

class MyController extends ControllerBase {

  private     $_jsonapiResponseEntity;

  public function __construct(JsonapiEntityResponseInterface $jsonapiResponseEntity) {
    $this->_jsonapiResponseEntity = $jsonapiResponseEntity;
  }
  
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('jsonapi_response.entity'),
    );
  }

  /**
   * A single entity in JSON:API Format
   */
  public function getMyEntity(): CacheableResponseInterface
  {
    $entity = $this->getEntityForResponse();
    $response = $this->_jsonapiResponseEntity->entityIndividualResponse($entity);
    $cache = new CacheableMetadata();
    $cache->setCacheMaxAge(0);
    $response->addCacheableDependency($cache);
    return $response;
  }

  /**
   * An entity collection in JSON:API Format
   */
  public function getMyEntityCollection(): CacheableResponseInterface
  {
    $entities = $this->getEntitiesForResponse();
    return $this->_jsonapiResponseEntity->entityCollectionResponse($entities);
  }
 
  /**
   * An entity collection in JSON:API Format with includes
   */
  public function getMyEntityCollectionWithIncludes(): CacheableResponseInterface
  {
    $entities = $this->getEntitiesForResponse();
    return $this->_jsonapiResponseEntity->entityCollectionResponse($entities, [$fieldName1, $fieldName2]);
  }

}

```

You are free to use this module without any restriction but without any warranty. 