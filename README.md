# JSON:API Response

A Drupal 9 module for custom JSON:API Entity responses.

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