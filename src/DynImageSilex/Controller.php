<?php

namespace DynImageSilex;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DynImage\DynImage;

class Controller {

    public function terminateAction(Request $request, Response $response, Application $app) {
        $app['monolog']->addDebug('entering dynimage terminate');

        $module = $app['module.service']->getModule();

        if (!isset($app['dynimage.image']->image)) {

            $app->abort(404);
        }

        if ($module->hasParameter('format')) {
            $format = $module->getParameter('format');
        } else {
            $format = pathinfo($app['dynimage.image']->imagefilename, PATHINFO_EXTENSION);
        }

        $response->headers->set('Content-Type', 'image/' . $format);
        $response->setContent($app['dynimage.image']->image->get($format));

        $response->setPublic();
        //$response->setStatusCode(200);
        if ($module->hasParameter('ttl')) {

            $response->setTtl($module->getParameter('ttl'));
        }

        return $response;
    }

    public function beforeAction(Request $request, Application $app) {
        $package = $request->attributes->get('package');
        $module = $request->attributes->get('module');

        $depth = $app['dynimage.routes_depth'];

        $imageFilename = '';
        for ($index = 0; $index < $depth; $index++) {
            $dir = $request->attributes->get('dir' . $index);
            if (!is_null($dir)) {
                $imageFilename .= $request->attributes->get('dir' . $index) . '/';
            }
        }


        $module = $app['packager.service']->getModule($package, $module);


        $imageFilename .= $request->attributes->get('imageFilename');

        $imageFilename = $module->getParameter('images_root_dir') . $imageFilename;

        if ($module->hasParameter('image')) {

            $imageFilename = $module->getParameter('image');
        }

        $app['monolog']->addDebug("image : $imageFilename");
        
        if (!file_exists($imageFilename) || !is_file($imageFilename)) {
            $app['monolog']->addDebug("image not found");
            
            if (!$module->hasParameter('image_default')) {

                throw new NotFoundHttpException();
            }
     
            $imageFilename = $module->getParameter('image_default');
            $app['monolog']->addDebug("image default : $imageFilename");
        }

        if ($module->hasParameter('enabled') && !$module->getParameter('enabled')) {
            throw new NotFoundHttpException();
        }

        $app['dynimage.image'] = DynImage::createImage(
                        $module->get('transformer'), file_get_contents($imageFilename), $imageFilename, $module->getParameter('dynimage.driver'), $module->getParameterBag()->all());
    }

}
