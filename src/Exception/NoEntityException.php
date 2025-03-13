<?php

namespace Drupal\jsonapi_response\Exception;

use Exception;

/**
 * No Entity Exception
 *
 * @author Attila Németh
 * 26.02.2021
 */
class NoEntityException extends Exception {

  public function __construct(string $message = "The oobject is not an entity", int $code = 416, \Throwable $previous = NULL) {
    parent::__construct($message, $code, $previous);
  }

}
