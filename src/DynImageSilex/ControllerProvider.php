<?php

namespace DynImageSilex;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {
        $app->get('/' . $app['dynimage.routes_prefix'], new ControllerProvider());
        $controllers = $app['controllers_factory'];

        $controllers->before('DynImageSilex\Controller::beforeAction');
        $controllers->after('DynImageSilex\Controller::terminateAction');

        $controllers->get('/{package}/{module}/', function (Request $request) use ($app) {
                $response = new Response;

                return $response;
            });
        /**/
        if (isset($app['dynimage.routes'])) {
            

            foreach ($app['dynimage.routes'] as $key => $route) {

                $controllers->get($route, function (Request $request) use ($app) {
                    return new Response;
                })->bind('dynimage.' . $key);
            }
        }
        /* */
        $depth = $app['dynimage.routes_depth'];
        $dir = '';
        for ($index = 0; $index < $depth; $index++) {
            $dir .= '/{dir' . $index . '}';

            $controllers->get('/{package}/{module}' . $dir . '/{imageFilename}', function (Request $request) use ($app) {
                $response = new Response;

                return $response;
            });
        }

        $controllers->get('/{package}/{module}/{imageFilename}', function (Request $request) use ($app) {

            $response = new Response;

            return $response;
        });

        return $controllers;
    }

}
