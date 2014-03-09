<?php

/**
 * Registers routes to RESTful actions on a controller
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\Silex;

use mym\REST\SerializedResponse;
use Silex\ControllerCollection;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RESTRoutes
{
  /**
   * Registers routes to RESTful actions on a controller defined as a service
   *
   * @param $app Application|ControllerCollection
   * @param $service string Controller service
   * @param $path string
   * @param $before callable
   * @param $after callable
   */
  public static function register($app, $service, $path, $before = null, $after = null)
  {
    // action handler
    $actionHandler = function(Request $request, $action) use ($app, $service) {

      $action = $action . 'Action';

      if (is_callable(array($app[$service], $action))) {

        // call action
        return call_user_func(array($app[$service], $action), $request, $app);

      } else {
        throw new NotFoundHttpException("Action $service:$action not found");
      }

    };

    $beforeHandler = function (Request $request) use ($before, $app) {
      if (is_callable($before)) {
        return call_user_func($before, $request, $app);
      }
    };

    $afterHandler = function (Request $request) use ($after, $app) {
      if (is_callable($after)) {
        return call_user_func($after, $request, $app);
      }
    };

    // actions
    $app->match($path . '/{action}.action',  $actionHandler)->before($beforeHandler)->after($afterHandler);
    $app->match($path . '/{id}/{action}.action',  $actionHandler)->before($beforeHandler)->after($afterHandler);

    // collection
    $app->get($path, $service . ':getCollectionAction')->before($beforeHandler)->after($afterHandler);
    $app->put($path, $service . ':replaceCollectionAction')->before($beforeHandler)->after($afterHandler);
    $app->post($path, $service . ':createResourceAction')->before($beforeHandler)->after($afterHandler);
    $app->delete($path, $service . ':deleteCollectionAction')->before($beforeHandler)->after($afterHandler);

    // resource
    $app->get($path . '/{id}', $service . ':getResourceAction')->before($beforeHandler)->after($afterHandler);
    $app->put($path . '/{id}', $service . ':updateOrCreateResourceAction')->before($beforeHandler)->after($afterHandler);
    $app->match($path . '/{id}', $service . ':updateResourceAction')->method('PATCH')->before($beforeHandler)->after($afterHandler);
    $app->delete($path . '/{id}', $service . ':deleteResourceAction')->before($beforeHandler)->after($afterHandler);
  }
} 