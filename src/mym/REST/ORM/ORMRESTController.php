<?php

/**
 * REST Controller for Doctrine ORM
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\ORM;

use Doctrine\ORM\EntityManager;
use mym\REST\RESTController;
use mym\REST\RESTRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ORMRESTController extends RESTController
{
  /**
   * @var EntityManager
   */
  protected $em;

  /**
   * @var RESTRepositoryInterface
   */
  protected $repository;

  public function setOm($om)
  {
    $this->em = $this->om = $om;
  }
}