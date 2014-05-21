<?php

namespace DynImageSilex;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


class CompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {

        if (!$container->hasDefinition('dynimage.packager')) {
            return;
        }

        $packager = $container->getDefinition('dynimage.packager');

        $packages = $container->findTaggedServiceIds('dynimage.package');

        foreach ($packages as $id => $tagAttributes) {

            $packager->addMethodCall(
                    'addPackage', array($id, new Reference($id))
            );
        }
    }

}
