<?php

namespace Drupal\jsonapi_response\Exception;

/**
 * No EntityE xception
 *
 * @author Attila Németh
 * 26.02.2021
 */
class NoEntityException extends \Exception {

  public function __construct(string $message = "The oobject is not an entity", int $code = 416, \Throwable $previous = NULL): \Exception {
    return parent::__construct($message, $code, $previous);
  }
  
}
