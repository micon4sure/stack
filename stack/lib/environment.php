<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */
use stack\Filesystem;

/**
 * Class Environment
 *
 * @package stack
 */
class Environment extends \lean\Environment {
    /**
     * @param string $environmentName
     */
    public function __construct($environmentName) {
        parent::__construct(STACK_APPLICATION_ROOT . '/config/environment.ini', $environmentName);
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