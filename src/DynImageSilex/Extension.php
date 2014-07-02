<?php

namespace DynImageSilex;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;





class Extension implements ExtensionInterface {

    public function load(array $configs, ContainerBuilder $container) {

        
        $packages_error = array();
        $packages_loaded = array();
        $packages_files = array();
        $packages_ids = array();
        
        $loader = new XmlFileLoader(
                $container, new FileLocator(__DIR__)
        );
        $loader->load('dynimage.xml');
        
        foreach($configs as $config) {
            foreach ($config as $file) {
                $packages_files[] = basename($file);
                try {
                    $loader->load($file);
                    
                    $packages = $container->findTaggedServiceIds('dynimage.package');
                   
//                    if (!isset($packages_ids[basename($file)])) {
//                        $packages_ids[basename($file)] = array();
//                    }
                    foreach ($packages as $id => $package) {
                        if (!array_key_exists($id,$packages_ids)) {
                            //$packages_ids[basename($file)][] = $id;
                            $packages_ids[$id] = basename($file);
                        }
                    }

                    $packages_loaded[] = basename($file);
                } catch (\Exception $e) {
                    $packages_error[basename($file)]=$e->getMessage();
                    continue;
                }
                
            }
        }
       
        if (!empty($packages_error)) {
            $container->setParameter('dynimage.packages_unloaded',$packages_error);
        }
        
        if (!empty($packages_error)) {
            $container->setParameter('dynimage.packages_loaded',$packages_loaded);
        }
        
        if (!empty($packages_files)) {
            $container->setParameter('dynimage.packages_files',$packages_files);
        }
        
        if (!empty($packages_ids)) {
            $container->setParameter('dynimage.packages_ids',$packages_ids);
        }
    }

    public function getAlias() {
        return 'dynimage_silex';
    }

    public function getXsdValidationBasePath() {
        return __DIR__;
    }

    public function getNamespace() {
        return false;
    }

}
