<?php

namespace DynImageSilex;

class Package {
    
    private $modules = array();
    private $options;
    
    public function __construct(array $options=array()) {
        $this->options = $options;
    }
    public function addModule($key,$file) {

        $this->modules[$key] = $file;
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
        if (isset($this->options['enabled'])) {
            return $this->options['enabled'];
        }
        
        return false;
    }
}
