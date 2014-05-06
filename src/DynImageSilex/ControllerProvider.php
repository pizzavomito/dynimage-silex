<?php

namespace DynImageSilex;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers->before('DynImageSilex\Controller::beforeAction');
        $controllers->after('DynImageSilex\Controller::terminateAction');

        
        if (!isset($app['dynimage.routes'])) {
            $app['dynimage.routes'] = array(
                'main' => '/{package}/{module}'
            );
        }

        foreach ($app['dynimage.routes'] as $key => $route) {

            $controllers->get($route, function (Request $request) use ($app) {
                return new Response;
            })->bind('dynimage.' . $key);
        }

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
