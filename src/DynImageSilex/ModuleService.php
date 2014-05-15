<?php

namespace DynImageSilex;

use DynImageContainerLoader\ContainerLoader;
use DynImageSilex\Extension;

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

    public function loadModule($file, $package = '') {
        $this->file = $file;
        try {
            $extensions = array();
            if (!empty($this->extensions)) {
                foreach ($this->extensions as $extension) {
                    $extensions[] = new $extension;
                }
            }
            $this->compiledFilename = $this->cacheDir . '/' . $package . '/'. pathinfo($this->file, PATHINFO_FILENAME) . '.php';
            
            $this->module = ContainerLoader::load($this->file, $this->cacheDir . '/' . $package, $this->debug, $extensions);
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
