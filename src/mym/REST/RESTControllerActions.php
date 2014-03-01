<?php

/**
 * REST API actions
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class RESTControllerActions
{
  /**
   * GET /collection/id
   *
   * @param Request $request
   */
  public function getResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * PUT /collection/id
   *
   * @param Request $request
   * @throws HttpNotImplementedException
   */
  public function updateOrCreateResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * DELETE /collection/id
   *
   * @param Request $request
   * @throws HttpNotImplementedException
   */
  public function deleteResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * GET /collection
   *
   * @param Request $request
   * @throws HttpNotImplementedException
   */
  public function getCollectionAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * PUT /collection
   *
   * @param Request $request
   * @throws HttpNotImplementedException
   */
  public function replaceCollectionAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * POST /collection
   *
   * @param Request $request
   * @throws HttpNotImplementedException
   */
  public function createResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * DELETE /collection
   *
   * @param Request $request
   * @throws HttpNotImplementedException
   */
  public function deleteCollectionAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }
} 