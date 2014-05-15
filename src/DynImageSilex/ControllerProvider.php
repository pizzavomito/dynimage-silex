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
