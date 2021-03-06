<?php

/**
 * REST Repository interface
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

use Doctrine\Common\Persistence\ObjectRepository;

interface RESTRepositoryInterface extends ObjectRepository
{
  /**
   * @param $skip int
   */
  public function setSkip($skip);

  /**
   * @param $limit int
   */
  public function setLimit($limit);

  /**
   * Get all resources
   * Limit/skip values are used
   *
   * @return array
   */
  public function searchAll();

  /**
   * Search by list od IDs
   *
   * @param array $ids
   * @return array
   */
  public function searchByIds($ids = array());
}