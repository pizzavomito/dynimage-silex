<?php

namespace DynImageSilex;

use DynImageSilex\Extension;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use DynImageSilex\CompilerPass;

/**
 *  Load and dump config file in the cache directory
 * 
 */
class PackageContainerLoader {

    static public function load($files, $cache_dir, $debug, $reload=false) {

        $filename = 'packagesContainer';
        $config = new ConfigCache($cache_dir . '/' . $filename . '.php', $debug);

        $className = str_replace('.', '', $filename);

        if (!$config->isFresh() || $reload) {
            $container = new ContainerBuilder();

            
            try {
                $extension = new Extension();
                $container->registerExtension($extension);

                $container->loadFromExtension($extension->getAlias(), $files);
                $container->addCompilerPass(new CompilerPass, PassConfig::TYPE_BEFORE_OPTIMIZATION);


                //$loader = new XmlFileLoader($container, new FileLocator(dirname($file)));

                //$loader->load($file);

                $container->compile();

                $dumper = new PhpDumper($container);

                $config->write($dumper->dump(array('class' => $className)), $container->getResources());
            } catch (\Exception $e) {

                throw $e;
            }
        }

        require $cache_dir . '/' . $filename . '.php';
        return new $className;
    }

}
