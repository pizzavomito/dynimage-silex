<?php

namespace DynImageSilex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use DynImageSilex\PackagerService;

class PackagerServiceProvider  implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['packager.service'] = $app->share( function () use ($app) {
            return new PackagerService($app['dynimage.packager'],$app['dynimage.cache_dir'],$app['env']);
        });

        if (isset($app['module.service'])) {
            $app['packager.service']->moduleService = $app['module.service'];
        }
    }

    public function boot(Application $app)
    {
    }

}
