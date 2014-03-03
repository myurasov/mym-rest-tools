<?php

/**
 * REST Utillities for Silex framework
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\Silex;

use Silex\Application;
use Silex\ControllerCollection;

class SilexRESTUtils
{
  /**
   * Define routes for RESTful actions on a controller defined as a service
   *
   * @param $app Application|ControllerCollection
   * @param $controllerService string
   * @param $path string
   */
  public static function addRESTRoutes($app, $controllerService, $path)
  {
    // collection
    $app->get($path, $controllerService . ':getCollectionAction');
    $app->put($path, $controllerService . ':replaceCollectionAction');
    $app->post($path, $controllerService . ':createResourceAction');
    $app->delete($path, $controllerService . ':deleteCollectionAction');

    // resource

    $app->get($path . '/{id}', $controllerService . ':getResourceAction');
    $app->put($path . '/{id}', $controllerService . ':updateResourceAction');
    $app->delete($path . '/{id}', $controllerService . ':deleteResourceAction');
  }

  /**
   * Adds JSON representation of exceptions
   * @param Application $app
   */
  public static function addJSONExceptionHandler(Application $app)
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