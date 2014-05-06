<?php

namespace DynImageSilex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use DynImageSilex\ModuleService;

class ModuleServiceProvider  implements ServiceProviderInterface
{
    public function register(Application $app)
    {

         $app['module.service'] = $app->share( function () use ($app) {
            return new ModuleService($app['dynimage.cache_dir'],$app['env']);
        });
    }

    public function boot(Application $app)
    {
    }

}
