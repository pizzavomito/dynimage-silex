<?php

namespace DynImageSilex;

use DynImageContainerLoader\ContainerLoader;


class ModuleService {

    private $module;
    public $file;
    public $cacheDir;
    public $debug;
    private $extensions = array();
    private $compiledFilename;

    public function __construct($cacheDir, $debug = true) {

        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    public function addExtension($extension) {
        $this->extensions[] = $extension;
    }

    public function compile($file, $package) {
        $this->loadModule($file, $package, true);
    }
    
    private function loadModule($file, $package = '', $reload=false) {
        $this->file = $file;
        try {
            $extensions = array();
            if (!empty($this->extensions)) {
                foreach ($this->extensions as $extension) {
                    $extensions[] = new $extension;
                }
            }
            $this->compiledFilename = $this->cacheDir . '/' . $package . '/'. pathinfo($this->file, PATHINFO_FILENAME) . '.php';
            $className = $package.pathinfo($this->file, PATHINFO_FILENAME);
             if (!file_exists($this->file)) {
                throw new \InvalidArgumentException(
                sprintf("Module file '%s' does not exist. ",$this->file));
            }
            $this->module = ContainerLoader::load($this->file, $this->cacheDir . '/' . $package, false, $extensions,$reload,$className);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getCompiledFilename() {
        return $this->compiledFilename;
    }

    public function getModule($file = null, $package = null) {
        if (!is_object($this->module)) {
            $this->loadModule($file, $package);
        }

        return $this->module;
    }

    public function getModuleDir() {
        return dirname($this->file);
    }

    public function isWritable() {
        $writable = false;
        if (is_writable($this->file) && is_writable(dirname($this->file))) {
            $writable = true;
        }

        return $writable;
    }

}
