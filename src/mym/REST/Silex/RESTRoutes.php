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
    static::registerRESTfulActions($app, $service, $path, $before, $after);
    static::registerActions($app, $service, $path, $before, $after);
  }

  public static function registerRESTfulActions($app, $service, $path, $before = null, $after = null)
  {
    // collection
    $app->get($path, $service . ':getCollectionAction')->before($before)->after($after);
    $app->put($path, $service . ':replaceCollectionAction')->before($before)->after($after);
    $app->post($path, $service . ':createResourceAction')->before($before)->after($after);
    $app->delete($path, $service . ':deleteCollectionAction')->before($before)->after($after);

    // resource
    $app->get($path . '/{id}', $service . ':getResourceAction')->before($before)->after($after);
    $app->put($path . '/{id}', $service . ':updateOrCreateResourceAction')->before($before)->after($after);
    $app->match($path . '/{id}', $service . ':updateResourceAction')->method('PATCH')->before($before)->after($after);
    $app->delete($path . '/{id}', $service . ':deleteResourceAction')->before($before)->after($after);
  }

  public static function registerActions($app, $service, $path, $before = null, $after = null)
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

    // actions
    $app->match($path . '/{action}.action',  $actionHandler)->before($before)->after($after);
    $app->match($path . '/{id}/{action}.action',  $actionHandler)->before($before)->after($after);
  }
} 