<?php

/**
 * REST Controller for Doctrine ORM
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\ORM;

use Doctrine\ORM\EntityManager;
use mym\REST\RESTController;

class ORMRESTController extends RESTController
{
  /**
   * @var EntityManager
   */
  protected $em;

  /**
   * @var ORMRESTRepository
   */
  protected $repository;

  public function setOm($om)
  {
    $this->em = $this->om = $om;
  }

  protected function deleteCollection()
  {
    $this->repository->createQueryBuilder('resource')
      ->delete()
      ->getQuery()
      ->execute();
  }
}