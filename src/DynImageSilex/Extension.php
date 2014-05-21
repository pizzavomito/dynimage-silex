<?php

namespace DynImageSilex;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;





class Extension implements ExtensionInterface {

    public function load(array $configs, ContainerBuilder $container) {

        
        $error = array();
        $loaded = array();
        $packages = array();
        $loader = new XmlFileLoader(
                $container, new FileLocator(__DIR__)
        );
        $loader->load('dynimage.xml');
        
        foreach($configs as $config) {
            foreach ($config as $file) {
                $packages[] = basename($file);
                try {
                    $loader->load($file);
                    $loaded[] = basename($file);
                } catch (\Exception $e) {
                    $error[basename($file)]=$e->getMessage();
                    continue;
                }
                
            }
        }
        
        if (!empty($error)) {
            $container->setParameter('dynimage.packages_unloaded',$error);
        }
        
        if (!empty($loaded)) {
            $container->setParameter('dynimage.packages_loaded',$loaded);
        }
        
        if (!empty($packages)) {
            $container->setParameter('dynimage.packages_files',$packages);
        }
    }

    public function getAlias() {
        return 'dynimage_silex';
    }

    public function getXsdValidationBasePath() {
        return false;
    }

    public function getNamespace() {
        return false;
    }

}
