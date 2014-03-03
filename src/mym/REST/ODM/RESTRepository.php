<?php

/**
 * MongoDB ODM REST Repository
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\ODM;

use Doctrine\ODM\MongoDB\DocumentRepository;
use mym\REST\RESTRepositoryInterface;

class RESTRepository extends DocumentRepository implements RESTRepositoryInterface
{
  protected $skip = 0;
  protected $limit = 100;

  public function searchAll()
  {
    return $this->createQueryBuilder()
      ->skip($this->skip)
      ->limit($this->limit)
      ->getQuery()
      ->execute();
  }

  public function searchByIds($ids = array())
  {
    return $this->createQueryBuilder()
      ->limit($this->limit)
      ->skip($this->skip)
      ->field('id')->in($ids)
      ->getQuery()->execute();
  }

  public function setLimit($limit)
  {
    $this->limit = $limit;
  }

  public function setSkip($skip)
  {
    $this->skip = $skip;
  }
}