<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Lowest layer of abstraction looking up from the couch layer
 * Handles adaption of couch documents and files including modules
 */
class FileSystem implements \stack\Interface_FileAccess {
    /**
     * @var \couchClient
     */
    private $couchClient;

    /**
     * @var Interface_Adapter
     */
    protected $adapter;

    /**
     * @param string            $dsn
     * @param string            $dbName
     * @param Interface_Adapter $adapter
     */
    public function __construct($dsn, $dbName, Interface_Adapter $adapter) {
        $this->adapter = $adapter;
        $this->couchClient = new \couchClient($dsn, $dbName);
    }

    /**
     * @return \couchClient
     */
    public function getCouchClient() {
        return $this->couchClient;
    }

    /**
     * Lazy adapter loader.
     * Deriving classes are encouraged to overwrite this and use the protected $adapter variable if doing so
     *
     * @return Adapter_File
     */
    protected function getAdapter() {
        return $this->adapter ?: $this->adapter = new \stack\filesystem\Adapter_File($this);
    }


    /**
     * Create the database
     */
    public function init() {
        $this->couchClient->createDatabase();
    }

    /**
     * @param string $path
     * @throws filesystem\Exception_FileNotFound
     * @return File
     */
    public function readFile($path) {
        try {
            $doc = $this->couchClient->getDoc("stack:/$path");
        } catch(\couchNotFoundException $e) {
            throw new \stack\filesystem\Exception_FileNotFound("File at path '$path' could not be found", null, $e);
        }
        $file = $this->getAdapter()->fromDatabase($doc);
        return $file;
    }

    /**
     * @param \stack\filesystem\File $file
     * @return \stack\filesystem\File
     */
    public function writeFile(\stack\filesystem\File $file) {
        // write the file to the file system
        // set revision to file instance
        $doc = $this->getAdapter()->toDatabase($file);
        $response = $this->couchClient->storeDoc($doc);
        $file->setRevision($response->rev);
        return $file;
    }

    /**
     * @param \stack\filesystem\File $file
     */
    public function deleteFile(\stack\filesystem\File $file) {
        $this->couchClient->deleteDoc($this->adapter->toDatabase($file));
    }
    /**
     * @param string $path
     * @param string $owner
     * @return \stack\filesystem\File
     */
    public function createFile($path, $owner) {
        $file = new \stack\filesystem\File($path, $owner);
        $this->writeFile($file);
        return $file;
    }

    /**
     * Factory reset method
     * @return void
     */
    public function nuke() {
        if ($this->couchClient->databaseExists()) {
            $this->couchClient->deleteDatabase();
        }
    }
}