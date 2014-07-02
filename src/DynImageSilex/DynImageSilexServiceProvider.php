<?php

namespace DynImageSilex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use DynImageSilex\PackageService;
use DynImageSilex\ModuleService;
use DynImageSilex\ControllerProvider;

class DynImageSilexServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {


        $app['module.service'] = $app->share(function () use ($app) {
            $m = new ModuleService($app['dynimage.cache_dir'].'/'.$app['env'], $app['debug']);
            $m->addExtension('DynImageSilex\Extension');
            if (!empty($app['dynimage.extensions'])) {
                foreach($app['dynimage.extensions'] as $extension) {
                    $m->addExtension($extension);
                }
            }
            
            return $m;
        });

        $app['package.service'] = $app->share(function () use ($app) {
            $p = new PackageService($app['dynimage.packages_dir'],$app['dynimage.cache_dir'].'/'.$app['env'], $app['debug']);
            $p->moduleService = $app['module.service'];
            return $p;
        });
       
    }

    public function boot(Application $app) {
        define('APP_DIR', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
        define('ENV', $app['env']);
        
        
        $app->mount('/' . $app['dynimage.routes_prefix'], new ControllerProvider());
    }

}
