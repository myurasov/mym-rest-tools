<?php

/**
 * REST API controller for use with Doctrine ORM
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class RESTController extends RESTControllerActions
{
  /**
   * @var ObjectManager
   */
  protected $om;

  /**
   * @var ObjectRepository
   */
  protected $repository;

  //<editor-fold desc="accessors">

  public function setOm($om)
  {
    $this->om = $om;
  }

  public function getOm()
  {
    return $this->om;
  }

  public function setRepository($repository)
  {
    $this->repository = $repository;
  }

  public function getRepository()
  {
    return $this->repository;
  }

  //</editor-fold>
} 