<?php

/**
 * REST Controller for Doctrine ORM
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use mym\REST\AbstractRESTController;

class RESTController extends AbstractRESTController
{
  /**
   * @var EntityManager
   */
  protected $em;

  /**
   * @var RESTRepository
   */
  protected $repository;

  public function setOm(ObjectManager $om)
  {
    $this->em = $this->om = $om;
  }

  protected function deleteAll()
  {
    $this->repository->createQueryBuilder('resource')
      ->delete()
      ->getQuery()
      ->execute();
  }
}