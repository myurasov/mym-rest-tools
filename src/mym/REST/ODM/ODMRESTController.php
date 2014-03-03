<?php

/**
 * REST Controller for Doctrine MongoDB ODM
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\ODM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use mym\REST\RESTController;

class ODMRESTController extends RESTController
{
  /**
   * @var DocumentManager
   */
  protected $dm;

  /**
   * @var ODMRESTRepository
   */
  protected $repository;

  public function setOm(ObjectManager $om)
  {
    $this->dm = $this->om = $om;
  }

  protected function deleteCollection()
  {
    $this->repository->createQueryBuilder()
      ->remove()
      ->getQuery()
      ->execute();
  }
}