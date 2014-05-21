<?php

namespace DynImageSilex;

class PackageService {

    private $packager;
    private $container;
    public $moduleService;
    public $availableDir;
    public $cacheDir;
    public $debug;

    public function __construct($availableDir, $cacheDir, $debug = true) {

        $this->availableDir = $availableDir;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }
    
    public function compile($dir=null) {
        if (!is_null($dir)) {
            $this->cacheDir = $dir;
        }
        $this->loadContainer(true);
    }

    public function getContainer($reload = false) {
       
        $this->loadContainer($reload);
  
        return $this->container;
    }

    private function loadContainer($reload) {
        try {
            if ($reload) {
                $this->container = null;
            }
            if (!is_object($this->container)) {
                $this->container = PackageContainerLoader::load(glob($this->availableDir . "/*.xml"), $this->cacheDir, $this->debug, $reload);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function loadPackager($reload) {
        $this->loadContainer($reload);

        $this->packager = $this->container->get('dynimage.packager');
    }

    public function getPackager($reload = false) {
        if (!is_object($this->packager)) {
            $this->loadPackager($reload);
        }

        return $this->packager;
    }

    public function getPackages() {
        $packages = $this->getPackager()->getPackages();

        if (!isset($packages)) {
            throw new \Exception('Failed to load packages.');
        }
        return $packages;
    }
    
    public function getPackagesFilename() {
        $tmp = array();
        foreach(glob($this->availableDir . "/*.xml") as $filename) {
            $tmp[] = basename($filename);
        }
            
        return $tmp;
    }

    public function getModuleFilename($id_package, $id_module) {

        $packager = $this->getPackager();

        if (!$packager->hasPackage($id_package)) {
            throw new \InvalidArgumentException(
            sprintf("The package '%s' does not exist.", $id_package));
        }

        $package = $packager->getPackage($id_package);
        if (!$package->hasModule($id_module)) {
            throw new \InvalidArgumentException(
            sprintf("The module '%s' does not exist.", $id_module));
        }

        if (!$package->isEnabled()) {
            throw new \InvalidArgumentException(
            sprintf("The package '%s' is disabled.", $id_package));
        }

        return $package->getModule($id_module);
    }

    public function getModule($package, $module) {
        return $this->moduleService->getModule($this->getModuleFilename($package, $module), $package);
    }

}
