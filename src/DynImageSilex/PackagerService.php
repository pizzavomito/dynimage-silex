<?php

namespace DynImageSilex;

class PackagerService {

    private $packager;
    
    public $moduleService;
    public $file;
    public $cacheDir;
    public $debug;

    public function __construct($file, $cacheDir, $debug = true) {
        $this->file = $file;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    public function loadPackager() {
        try {
            $this->packager = PackagerLoader::load($this->file, $this->cacheDir, $this->debug);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getPackager() {
        if (!is_object($this->packager)) {
            $this->loadPackager();
        }

        return $this->packager;
    }

    public function getPackagerDir() {
        return dirname($this->file);
    }

    public function getPackages() {
        $parameters = $this->getPackager()->getParameterBag()->all();

        if (!isset($parameters['packages'])) {
            throw new \Exception('Failed to load packages.');
        }
        return $parameters['packages'];
    }

    public function isWritable() {
        $writable = false;
        if (is_writable($this->file) && is_writable(dirname($this->file))) {
            $writable = true;
        }
        
        return $writable;
    }
    public function getModuleFilename($package, $module) {

        $packages = $this->getPackages();
        
        if (!isset($packages[$package])) {
            throw new \InvalidArgumentException(
            sprintf("The package '%s' does not exist.", $package));
        }

        if (!isset($packages[$package]['modules'][$module])) {
            throw new \InvalidArgumentException(
            sprintf("The module '%s' does not exist.", $module));
        }

        if (isset($packages[$package]['enabled']) && !$packages[$package]['enabled']) {
            throw new \InvalidArgumentException(
            sprintf("The package '%s' is disabled.", $package));
        }
        
        return  $packages[$package]['modules'][$module];
    }
    
    public function getModule($package, $module) {
        return $this->moduleService->getModule($this->getModuleFilename($package, $module),$package);
    }

}
