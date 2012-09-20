<?php
namespace stack\web\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, which is found under /path/to/stack/LICENSE
 */

abstract class BaseModule extends \stack\module\BaseModule {

    /**
     * @var \stack\web\Request
     */
    private $request;
    /**
     * Holds template variables
     *
     * @var \ArrayObject
     */
    protected $data;

    /**
     */
    public function __construct() {
        $this->data = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param \stack\web\Request $request
     */
    public function initRequest(\stack\web\Request $request) {
        $this->request = $request;
    }

    /**
     * @return \stack\web\Request
     */
    protected function getRequest() {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public abstract function run();
}