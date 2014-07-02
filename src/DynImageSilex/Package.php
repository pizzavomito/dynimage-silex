<?php

namespace DynImageSilex;

use DynImageSilex\PackageInterface;

class Package implements PackageInterface {

    private $modules = array();
    private $enabled;

    public function __construct($enabled = true) {
        $this->enabled = $enabled;
    }

    public function __call($name, $arguments) {
        if (!method_exists($this, $name)) {
            throw new \Exception("Call to undefined method " . __CLASS__ . "::" . $name);
        }
    }

    public function addModule($key, $filename) {

        
        $this->modules[$key] = $filename;
    }

    public function getModules() {
        return $this->modules;
    }

    public function hasModule($key) {
        return isset($this->modules[$key]);
    }

    public function getModule($key) {
        return $this->modules[$key];
    }

    public function isEnabled() {

        return $this->enabled;
    }

}
