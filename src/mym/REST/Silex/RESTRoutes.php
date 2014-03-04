<?php

/**
 * Registers routes to RESTful actions on a controller
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST\Silex;

use Silex\ControllerCollection;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RESTRoutes
{
  /**
   * Registers routes to RESTful actions on a controller defined as a service
   *
   * @param $app Application|ControllerCollection
   * @param $service string Controller service
   * @param $path string
   */
  public static function register($app, $service, $path)
  {
    // action handler
    $actionHandler = function(Request $request, $action) use ($app, $service) {

      $action = $action . 'Action';

      if (is_callable(array($app[$service], $action))) {

        if ($request->getMethod() !== 'POST') {
          throw new MethodNotAllowedHttpException(array('POST'));
        }

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
} 