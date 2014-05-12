<?php

namespace DynImageSilex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use DynImageSilex\PackagerService;
use DynImageSilex\ModuleService;
use DynImageSilex\ControllerProvider;

class DynImageSilexServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {


        $app['module.service'] = $app->share(function () use ($app) {
            $m = new ModuleService($app['dynimage.cache_dir'], $app['env']);
            $m->addExtension('DynImageSilex\Extension');
            if (!empty($app['dynimage.extensions'])) {
                foreach($app['dynimage.extensions'] as $extension) {
                    $m->addExtension($extension);
                }
            }
            
            return $m;
        });

        $app['packager.service'] = $app->share(function () use ($app) {
            $p = new PackagerService($app['dynimage.packager_file'], $app['dynimage.cache_dir'], $app['env']);
            $p->moduleService = $app['module.service'];
            return $p;
        });
    }

    public function boot(Application $app) {
        define('APP_DIR', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
        define('ENV', $app['env']);
        $app->get('/' . $app['dynimage.routes_prefix'], new ControllerProvider());
    }

}
