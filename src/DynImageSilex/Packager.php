<?php

namespace DynImageSilex;

class Packager {
    
    private $packages = array();
    
    public function addPackage($key,$package) {
        $this->packages[$key] = $package;
    }
    
    public function getPackages() {
        return $this->packages;
    }
    
    public function hasPackage($key) {
        return isset($this->packages[$key]);
    }
    
    public function getPackage($key) {
        return $this->packages[$key];
    }
}
