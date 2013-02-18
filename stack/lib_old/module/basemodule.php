<?php
namespace stack\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */


/**
 * Module to be saved in a file
 */
abstract class BaseModule {

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
     * @param $data
     * @return \stack\module\BaseModule
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

    /**
     * Create an instance of the called class with data as the arguments
     *
     * @param $data
     * @return BaseModule
     */
    public static function create($data) {
        return new static($data);
    }

    /**
     * Create JSONizable data
     *
     * @abstract
     * @param $data
     * @return \stdClass
     */
    protected function export($data) {
        return $data;
    }
}