<?php

namespace DynImageSilex;

use DynImageContainerLoader\ContainerLoader;
use DynImageSilex\Extension;

class ModuleService {

    private $module;
    public $file;
    public $cacheDir;
    public $debug;

    public function __construct($cacheDir, $debug = true) {
        
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    public function loadModule($file,$package='') {
        $this->file = $file;
        try {
            $extension = new Extension();
            $this->module = ContainerLoader::load($this->file, $this->cacheDir.'/'.$package, $this->debug, array($extension));
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getModule($file=null,$package=null) {
        if (!is_object($this->module)) {
            $this->loadModule($file,$package);
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
