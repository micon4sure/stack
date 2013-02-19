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
class ModuleFactory {
    /**
     * @var callable[]
     */
    private $workers = array();

    public function __construct() {
        $this->registerWorker(Module_Default::TYPE_ID, function(\stdClass $data) {
            return new Module_Default($data);
        });
    }

    /**
     * Register a module worker
     *
     * @param string   $id
     * @param callable $worker
     */
    public function registerWorker($id, callable $worker) {
        $this->workers[$id] = $worker;
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
        if(!array_key_exists($id, $this->workers)) {
            throw new Exception("No worker registered for id '$id'");
        }

        $worker = $this->workers[$id];
        return $worker($data);
    }
}