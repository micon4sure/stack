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
     * @return Module
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getOwner() {
        return $this->document->owner;
    }

    /**
     * @return array
     */
    public function getPermissions() {
        return $this->document->permissions;
    }

    /**
     * @param string      $priviledge
     * @param string      $context
     * @param string|null $subject
     */
    public function addPermission($priviledge, $context, $subject = null) {
        $this->document->permissions[] = ['priviledge' => $priviledge, 'context' => $context, 'subject' => $subject];
    }
}