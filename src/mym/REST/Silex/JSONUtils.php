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