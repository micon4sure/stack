<?php
namespace stack\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */


/**
 * Default implements BaseModule_Abstract
 */
abstract class BaseModule extends BaseModule_Abstract {
    protected function export($data) {
        return $data;
    }

    public static function create($data) {
        return new static($data);
    }
}

/**
 * Module to be saved in a file
 */
abstract class BaseModule_Abstract {

    /**
     * @var \stack\Application
     */
    private $application;

    /**
     * @var null|\stdClass
     */
    protected $data;

    /**
     * @param null $data
     */
    public function __construct($data = null) {
        if($data = null)
            $data = new \stdClass();
        $this->data = $data;
    }

    /**
     * Initialize the module
     */
    public function init(\stack\Application $application) {
        $this->application = $application;
    }

    /**
     * @return mixed
     */
    public function getData() {
        $data = $this->export($this->data) ?: new \stdClass;
        return $data;
    }

    /**
     * Create JSONizable data
     *
     * @abstract
     * @param $data
     * @return \stdClass
     */
    protected abstract function export($data);

    /**
     * @param $data
     * @return \stack\module\BaseModule_Abstract
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return static::NAME;
    }

    /**
     * @return \stack\Application
     */
    protected function getApplication() {
        return $this->application;
    }
}