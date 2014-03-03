<?php

/**
 * REST API controller
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Tests\Common\Annotations\Ticket\Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RESTController extends RESTControllerActions
{
  /**
   * @var ObjectManager
   */
  protected $om;

  /**
   * @var RESTRepositoryInterface
   */
  protected $repository;

  /**
   * Method to get last modification date
   * @var string
   */
  protected $modifiedDateMethodName = 'getUpdatedAt';

  /**
   * @var SerializedResponse
   */
  protected $response;

  protected $defaultLimit = 10;

  protected $maxLimit = 100;

  public function getResourceAction(Request $request)
  {
    $res = $this->loadResource($request->attributes->get('id'), true /* required */);
    $this->response->setData($res);

    // set last-modified header
    if (method_exists($res, $this->modifiedDateMethodName)) {
      $this->response->setLastModified(call_user_func($res, $this->modifiedDateMethodName));
    }

    return $this->response;
  }

  protected function loadResource($id, $required = true)
  {
    $res = $this->getRepository()->find($id);

    if ($required && is_null($res)) {
      throw new NotFoundHttpException();
    }

    return $res;
  }

  public function search(Request $request)
  {
    $limit = min($this->maxLimit, (int)$request->query->get('limit', $this->defaultLimit));
    $skip = min(0, (int)$request->query->get('skip', 0));

    $this->repository->setLimit($limit);
    $this->repository->setSkip($skip);

    return $this->repository->searchAll();
  }

  //<editor-fold desc="accessors">

  public function getOm()
  {
    return $this->om;
  }

  public function setOm($om)
  {
    $this->om = $om;
  }

  public function getRepository()
  {
    return $this->repository;
  }

  public function setRepository($repository)
  {
    $this->repository = $repository;
  }

  public function setResponse($response)
  {
    $this->response = $response;
  }

  public function getResponse()
  {
    return $this->response;
  }

  public function setModifiedDateMethodName($modifiedDateMethodName)
  {
    $this->modifiedDateMethodName = $modifiedDateMethodName;
  }

  public function getModifiedDateMethodName()
  {
    return $this->modifiedDateMethodName;
  }

  public function setMaxLimit($maxLimit)
  {
    $this->maxLimit = $maxLimit;
  }

  public function getMaxLimit()
  {
    return $this->maxLimit;
  }

  public function setDefaultLimit($defaultLimit)
  {
    $this->defaultLimit = $defaultLimit;
  }

  public function getDefaultLimit()
  {
    return $this->defaultLimit;
  }

  //</editor-fold>
}