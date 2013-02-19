<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Class Module provides functionality to a document via File
 *
 * @package stack
 */
abstract class Module {
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @param \stdClass $data
     */
    public function __construct(\stdClass $data) {
        $this->data = $data;
    }

    /**
     * @param \stdClass $data
     */
    public function setData(\stdClass $data) {
        $this->data = $data;
    }

    /**
     * @return \stdClass
     */
    public function getData() {
        return $this->data;
    }
}

class Module_Default extends Module {
    const TYPE_ID = 'stack.default';
}