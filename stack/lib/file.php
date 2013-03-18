<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Class File provides an abstraction layer around a couch document and holds a module if provided
 *
 * @package stack
 */
class File {

    /**
     * @var \stdClass
     */
    private $document;
    /**
     * @var Module
     */
    private $module;
    /**
     * @var Cabinet
     */
    private $cabinet;

    /**
     * @param \stdClass $document the raw document
     * @param Module    $module
     *
     * @return \stack\File
     */
    public function __construct(\stdClass $document, Module $module) {
        $this->document = $document;
        $this->module = $module;
    }

    /**
     * Get the path, internally saved as the document's id
     *
     * @return mixed
     */
    public function getPath() {
        return $this->document->_id;
    }

    /**
     * Get the actual raw document
     *
     * @return \stdClass
     */
    public function getDocument() {
        return $this->document;
    }

    /**
     * @param Module $module
     */
    public function setModule(Module $module) {
        $this->module = $module;
    }

    /**
     * @return Module
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * @param Cabinet $cabinet
     */
    public function connect(Cabinet $cabinet) {
        $this->cabinet = $cabinet;
    }

    /**
     * Store this file in a cabinet, if connected
     *
     * @throws Exception
     */
    public function store() {
        if($this->cabinet === null) {
            throw new Exception('File is not connected to a cabinet');
        }
        $this->cabinet->storeFile($this);
    }
}