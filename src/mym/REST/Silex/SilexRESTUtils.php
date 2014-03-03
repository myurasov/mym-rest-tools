<?php

/**
 * REST Utillities for Silex framework
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\Silex;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SilexRESTUtils
{
  /**
   * Define routes for RESTful actions on a controller defined as a service
   *
   * @param $app Application|ControllerCollection
   * @param $service string Controller service
   * @param $path string
   */
  public static function registerRESTRoutes($app, $service, $path)
  {
    // action handler
    $actionHandler = function(Request $request, $action) use ($app, $service) {

      $action = $action . 'Action';

      if (is_callable(array($app[$service], $action))) {
        // call action
        return call_user_func(array($app[$service], $action), $request);
      } else {
        throw new NotFoundHttpException("Action $service:$action not found");
      }

    };

    // actions
    $app->match($path . '/{action}.action',  $actionHandler);
    $app->match($path . '/{id}/{action}.action',  $actionHandler);

    // collection
    $app->get($path, $service . ':getCollectionAction');
    $app->put($path, $service . ':replaceCollectionAction');
    $app->post($path, $service . ':createResourceAction');
    $app->delete($path, $service . ':deleteCollectionAction');

    // resource
    $app->get($path . '/{id}', $service . ':getResourceAction');
    $app->put($path . '/{id}', $service . ':updateOrCreateResourceAction');
    $app->match($path . '/{id}', $service . ':updateResourceAction')->method('PATCH');
    $app->delete($path . '/{id}', $service . ':deleteResourceAction');
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