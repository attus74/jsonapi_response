# JSON:API Response

A Drupal 9 module for custom JSON:API Entity responses.

[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg)](https://GitHub.com/attus74/jsonapi_response/graphs/commit-activity)
[![GitHub license](https://img.shields.io/github/license/attus74/jsonapi_response.svg)](https://github.com/attus74/jsonapi_response/blob/master/LICENSE)
[![GitHub release](https://img.shields.io/github/release/attus74/jsonapi_response.svg)](https://GitHub.com/attus74/jsonapi_response/releases/)
[![GitHub issues](https://img.shields.io/github/issues/attus74/jsonapi_response.svg)](https://GitHub.com/attus74/jsonapi_response/issues/)

JSON:API module of Drupal 9 uses different classes, so that it is unfortunately no more possible 
to use the same module for both Drupal Core versions. 

Version 2.0 of this module supports only Drupal 9.

## Usage

```php

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponseInterface;

class MyController extends ControllerBase {

  /**
   * A single entity in JSON:API Format
   */
  public function getMyEntity(): CacheableResponseInterface
  {
    $entity = $this->getEntityForResponse();
    $response = \Drupal::service('jsonapi_response.entity')->entityIndividualResponse($entity);
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
    return \Drupal::service('jsonapi_response.entity')->entityCollectionResponse($entities);
  }
 
  /**
   * An entity collection in JSON:API Format with includes
   */
  public function getMyEntityCollectionWithIncludes(): CacheableResponseInterface
  {
    $entities = $this->getEntitiesForResponse();
    return \Drupal::service('jsonapi_response.entity')->entityCollectionResponse($entities, [$fieldName1, $fieldName2]);
  }

}

```

You are free to use this module without any restriction but without any warranty. 
