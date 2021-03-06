<?php

/**
 * REST Controller for Doctrine MongoDB ODM
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\ODM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use mym\REST\AbstractRESTController;

class RESTController extends AbstractRESTController
{
  /**
   * @var DocumentManager
   */
  protected $dm;

  /**
   * @var RESTRepository
   */
  protected $repository;

  public function setOm(ObjectManager $om)
  {
    $this->dm = $this->om = $om;
  }

  protected function deleteAll()
  {
    $this->repository->createQueryBuilder()
      ->remove()
      ->getQuery()
      ->execute();
  }
}