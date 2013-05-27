<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Class ModuleFactory creates modules by provided factory functions (workers)
 *
 * @package stack
 */
use lean\util\Dump;

class ModuleFactory {

    const DEFAULT_MODULE_TYPE = 'stack.module.default';

    private $mapping = [];

    public function __construct() {
        $this->addMapping(self::DEFAULT_MODULE_TYPE, 'stack\Module_Default');
    }

    public function addMapping($identifier, $moduleType) {
        $this->mapping[$identifier] = $moduleType;
    }

    public function addMappings(array $mapping) {
        foreach($mapping as $id => $type) {
            $this->addMapping($id, $type);
        }
    }

    /**
     * Create a module using a worker indicated by $id
     *
     * @param string $id
     * @param \stdClass $data
     *
     * @return mixed
     * @throws Exception
     */
    public function createModule($id, $data) {
        if(!array_key_exists($id, $this->mapping)) {
            throw new Exception("Type identifier '$id' is not registered");
        }

        $class = $this->mapping[$id];
        return new $class($data);
    }

    public function getMappedIdentifier(Module $module) {
        $flipped = array_flip($this->mapping);
        $class = get_class($module);
        if(!array_key_exists($class, $flipped)) {
            throw new Exception("No identifier mapped for module of type '$class'");
        }

        return $flipped[$class];
    }
}