<?php

/**
 * JSON handling for Silex
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\Silex;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JSONUtils
{
  /**
   * Parses JSON request body
   * @param Application $app
   */
  public static function registerJSONRequestHandling(Application $app)
  {
    $app->before(function (Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
          $data = json_decode($request->getContent(), true);
          $request->request->replace(is_array($data) ? $data : array());
        }
      });
  }

  /**
   * Adds JSON representation of exceptions
   * @param Application $app
   */
  public static function registerJSONExceptionHandler(Application $app)
  {
    $app->error(function (\Exception $e) use ($app) {

        /** @var Request $request */ $request =  $app['request'];

        if ($request->headers->has('accept')
          && 0 === strpos($request->headers->get('accept'),
            'application/json')) {

          $response = new JsonResponse();

          if ($e instanceof HttpException) {
            $response->setStatusCode($e->getStatusCode());
          } else {
            $response->setStatusCode(500);
          }

          $response->setData(array(
              'error' => $e->getCode(),
              'message' => $e->getMessage()
            ));

          return $response;
        }

        return null;

      });
  }
} 