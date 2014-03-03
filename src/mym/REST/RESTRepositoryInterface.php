<?php

/**
 * REST Repository interface
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

interface RESTRepositoryInterface
{
  public function setSkip($skip);
  public function setLimit($limit);
  public function searchAll();
}