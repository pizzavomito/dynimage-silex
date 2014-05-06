<?php

namespace DynImageSilex;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;


class Extension implements ExtensionInterface {

    public function load(array $configs, ContainerBuilder $container) {

        $loader = new XmlFileLoader(
                        $container,
                        new FileLocator(__DIR__)
        );
        $loader->load('dynimage.xml');
       
    }

    public function getAlias() {
        return '_DynImageSilex';
    }

    public function getXsdValidationBasePath() {
        return false;
    }

    public function getNamespace() {
        return false;
    }

}
