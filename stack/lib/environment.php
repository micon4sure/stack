<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */
use stack\Filesystem;

/**
 * Custom stack environment
 */
class Environment extends \lean\Environment {
    /**
     * @param string $environmentName
     */
    public function __construct($environmentName) {
        parent::__construct(STACK_APPLICATION_ROOT . '/config/environment.ini', $environmentName);
    }


    /**
     * Create a Shell with the passed fileSystem or a created one
     *
     * @param \stack\Context $context
     * @param Filesystem $fileSystem
     * @return Shell
     */
    public function createShell(Context $context) {
        return new Shell($context);
    }

    /**
     * @param \stack\Context $context
     * @return Filesystem
     */
    public function createFilesystem(Interface_Adapter $adapter) {
        return new \stack\FileSystem($this->get('stack.database.url'), $this->get('stack.database.name'), $adapter);
    }

    public function isDebug() {
        try {
            return (bool)$this->get('debug');
        }
        catch(\lean\Exception $e) {
            return false;
        }
    }
}