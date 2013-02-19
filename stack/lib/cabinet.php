<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Class Cabinet provides abstracted access to the underlying couch database
 *
 * @package stack
 */
class Cabinet {
    /**
     * @var \couchClient
     */
    private $client;
    /**
     * @var ModuleFactory
     */
    private $moduleFactory;

    /**
     * @param \couchClient $client
     */
    public function __construct(\couchClient $client, ModuleFactory $moduleFactory) {
        $this->client = $client;
        $this->moduleFactory = $moduleFactory;
    }

    /**
     * @param $path
     *
     * @return File
     */
    public function createFile($path) {
        $document = new \stdClass();
        $document->_id = 'stack:/' . $path;
        $file = new File($document, new Module_Default( new \stdClass()));
        $this->storeFile($file);
        return $file;
    }

    /**
     * Save a file
     *
     * @param File $file
     */
    public function storeFile(File $file) {
        $document = $file->getDocument();

        // save module data and id in document
        $module = $file->getModule();
        $document->data = $module->getData();
        $document->module = $module::TYPE_ID;

        // store document in db
        $this->client->storeDoc($document);
    }

    /**
     * Read a file
     *
     * @param string $path
     *
     * @return File
     */
    public function fetchFile($path) {
        $document = $this->client->getDoc('stack:/' . $path);

        // create module via factory
        $module = $this->moduleFactory->createModule($document->module, $document->data);

        return new File($document, $module);
    }

    /**
     * Delete a file
     *
     * @param File $file
     */
    public function deleteFile(File $file) {
        $this->client->deleteDoc($file->getDocument());
    }

    /**
     * Check if a file exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path) {
        try {
            $this->fetchFile($path);
            return true;
        } catch(\couchNotFoundException $e) {
            return false;
        }
    }
}