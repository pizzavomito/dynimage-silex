<?php

namespace DynImageSilex;

use DynImageSilex\Extension;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Config\FileLocator;

/**
 *  Load and dump config file in the cache directory
 * 
 */
class PackagerLoader {

    static public function load($file, $cache_dir, $debug) {

        $filename = pathinfo($file, PATHINFO_FILENAME);
        $config = new ConfigCache($cache_dir . '/' . $filename . '.php', $debug);

        $className = str_replace('.', '', $filename);

        if (!$config->isFresh()) {
            $container = new ContainerBuilder();

            if (!file_exists($file)) {
                throw new \InvalidArgumentException(
                sprintf("The packager file '%s' does not exist.", $file));
            }

            try {

                $loader = new XmlFileLoader($container, new FileLocator(dirname($file)));

                $loader->load($file);

                $extension = new Extension();
                $container->registerExtension($extension);
                $container->loadFromExtension($extension->getAlias());

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
