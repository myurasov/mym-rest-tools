<?php

/**
 * REST API controller
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

use mym\Util\Arrays;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractRESTController extends RESTControllerActions
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
  protected $modificationDateMethodName = 'getUpdatedAt';

  /**
   * @var SerializedResponse
   */
  protected $response;

  /**
   * Default collection output limit
   * @var int
   */
  protected $defaultLimit = 10;

  /**
   * Maximum collection output limit
   * @var int
   */
  protected $maxLimit = 100;

  /**
   * @var PropertyAccessor
   */
  protected $propertyAccessor;

  //<editor-fold desc="actions">

  /**
   * @inheritdoc
   */
  public function getResource(Request $request)
  {
    // load resource
    $resource = $this->load($request->attributes->get('id'), true /* required */);

    // set last-modified header
    if (method_exists($resource, $this->modificationDateMethodName)) {
      $this->response->setLastModified(call_user_func(array($resource, $this->modificationDateMethodName)));
    }

    // return resource
    $this->response->setData($resource);
    return $this->response;
  }

  public function createResource(Request $request)
  {
    // create new resource
    $resource = $this->create();
    $this->om->persist($resource);

    // update with poseted data
    $this->update($resource, $request->request->all());

    // save
    $this->om->flush($resource);

    // return resource
    $this->response->setData($resource);
    return $this->response;
  }

  public function updateOrCreateResource(Request $request)
  {
    // load existing resource
    $resource = $this->load($request->attributes->get('id'), false /* not required */);

    if (!$resource) {
      // create new if not found
      $resource = $this->create();
      $this->om->persist($resource);
    }

    // update with request
    $this->update($resource, $request->request->all());

    // save
    $this->om->flush($resource);

    // return resource
    $this->response->setData($resource);
    return $this->response;
  }

  public function updateResource(Request $request)
  {
    // load existing resource
    $resource = $this->load($request->attributes->get('id'), true /* required */);

    // update with request
    $this->update($resource, $request->request->all());

    // save
    $this->om->flush($resource);

    // return resource
    $this->response->setData($resource);
    return $this->response;
  }

  /**
   * Get collection
   * Limit/skip are set on repository
   *
   * ?limit
   * ?skip
   *
   * @param Request $request
   * @return SerializedResponse
   */
  public function getCollection(Request $request)
  {
    $this->setLimits($request);
    $this->response->setData($this->search($request));
    return $this->response;
  }

  public function deleteResource(Request $request)
  {
    // load existing resource
    $resource = $this->load($request->attributes->get('id'), true /* required */);

    // remove
    $this->om->remove($resource);

    // save
    $this->om->flush($resource);

    //

    $this->response->setData(array(
        'message' => 'ok'
      ));

    return $this->response;
  }

  public function replaceCollection(Request $request)
  {
    // insert new resources

    $collectionData = $request->request->all();

    if (is_array($collectionData)) {
      foreach ($collectionData as $resourceData) {
        // create
        $resource = $this->create();
        $this->om->persist($resource);

        // update
        $this->update($resource, $resourceData);
      }
    }

    // delete all
    $this->deleteAll();

    // save new
    $this->om->flush();

    //

    $this->response->setData(array(
        'message' => 'ok'
      ));

    return $this->response;
  }

  public function deleteCollection(Request $request)
  {
    $this->deleteAll();

    //

    $this->response->setData(array(
        'message' => 'ok'
      ));

    return $this->response;
  }

  //</editor-fold>

  /**
   * Delete all items in collection
   * Changes saved to database immideately
   */
  abstract protected function deleteAll();

  /**
   * Create new instance of the resource
   *
   * @return object
   */
  protected function create()
  {
    $className = $this->getRepository()->getClassName();
    return new $className;
  }

  /**
   * Load resource
   *
   * @param $id
   * @param bool $required
   * @return mixed
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  protected function load($id, $required = true)
  {
    $resource = $this->getRepository()->find($id);

    if ($required && is_null($resource)) {
      throw new NotFoundHttpException();
    }

    return $resource;
  }

  /**
   * Sets skip/limit on repository from query parameters
   * @param Request $request
   */
  protected function setLimits(Request $request)
  {
    $limit = min($this->maxLimit, $request->query->getInt('limit', $this->defaultLimit));
    $skip = min(0, $request->query->getInt('skip', 0));

    $this->repository->setLimit($limit);
    $this->repository->setSkip($skip);
  }

  /**
   * Search resources
   *
   * @param Request $request
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   * @return array
   */
  protected function search(Request $request)
  {
    if ($request->query->has('id')) {

      $ids = $request->query->get('id');

      if (!is_array($ids)) {
        throw new BadRequestHttpException();
      }

      return $this->repository->searchByIds($ids);
    }

    return $this->repository->searchAll();
  }

  /**
   * Update resource
   *
   * @param $resource object
   * @param $input array
   */
  protected function update(&$resource, array $input)
  {
    $self = $this; // php 5.3 can't use $this inside closures

    if (is_array($input)) {
      Arrays::walkArray($input, function ($path, $value) use ($resource, $self) {
            $self->updateField($resource, $path, $value);
        });
    }
  }

  /**
   * Update field
   *
   * @param $resource object
   * @param $path string
   * @param $value
   */
  protected function updateField(&$resource, $path, $value)
  {
    // create property accessor
    if (!$this->propertyAccessor) {
      $this->propertyAccessor = new PropertyAccessor();
    }

    $this->propertyAccessor->setValue($resource, $path, $value);
  }

  //<editor-fold desc="accessors">

  public function getOm()
  {
    return $this->om;
  }

  public function setOm(ObjectManager $om)
  {
    $this->om = $om;
  }

  public function getRepository()
  {
    return $this->repository;
  }

  public function setRepository(RESTRepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  public function setResponse(SerializedResponse $response)
  {
    $this->response = $response;
  }

  public function getResponse()
  {
    return $this->response;
  }

  public function setModificationDateMethodName($modificationDateMethodName)
  {
    $this->modificationDateMethodName = $modificationDateMethodName;
  }

  public function getModificationDateMethodName()
  {
    return $this->modificationDateMethodName;
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