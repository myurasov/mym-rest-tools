<?php

/**
 * ORM REST Repository
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\ORM;

use Doctrine\ORM\EntityRepository;
use mym\REST\RESTRepositoryInterface;

class ORMRESTRepository extends EntityRepository implements RESTRepositoryInterface
{
  protected $skip = 0;
  protected $limit = 100;

  public function searchAll()
  {
    return $this->createQueryBuilder('e')
      ->setFirstResult($this->skip)
      ->setMaxResults($this->limit)
      ->getQuery()
      ->getResult();
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