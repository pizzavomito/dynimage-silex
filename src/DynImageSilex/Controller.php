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

        $imageTime = filemtime($app['dynimage.imagefilename']);
        $moduleCompiledTime = filemtime($app['module.service']->getCompiledFilename());
        
        $response->setETag(md5($imageTime . $moduleCompiledTime));
        
        if ($response->isNotModified($request)) {
           
            return $response;
        }

        
        $module = $app['module.service']->getModule();
        $dynimage = $module->get('dynimage');

        
        $imageContent = file_get_contents($app['dynimage.imagefilename']);
        $image = $dynimage->apply($imageContent, $module->getParameter('dynimage.driver'));

        if (!isset($image)) {

            $app->abort(404);
        }

        if ($module->hasParameter('format')) {
            $format = $module->getParameter('format');
        } else {
            $format = pathinfo($app['dynimage.imagefilename'], PATHINFO_EXTENSION);
        }

        $response->headers->set('Content-Type', 'image/' . $format);

        ob_start();

        echo $image->get($format);

       
        $imageData = ob_get_contents();

        $imageDataLength = ob_get_length();

        ob_end_clean();

        $response->setContent($imageData);

        

        $response->headers->set("Content-Length", $imageDataLength);

        $response->setPublic();

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

        $app['dynimage.imagefilename'] = $imageFilename;
        
    }

}
