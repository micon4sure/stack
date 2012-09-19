<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */
use stack\fileSystem\Exception_ModuleConflict;

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
     * @var \stack\module\Adapter
     */
    protected $adapter;
    /**
     * @var array
     */
    protected $factories = array();

    /**
     * @var Context
     */
    private $context;

    /**
     * @param \stack\Context $context
     * @param string $dsn
     * @param string $dbName
     */
    public function __construct(Context $context, $dsn, $dbName) {
        $this->context = $context;
        $this->couchClient = new \couchClient($dsn, $dbName);
    }

    /**
     * @param Adapter $adapter
     */
    public function setAdapter(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    /**
     * Lazy adapter loader.
     * Deriving classes are encouraged to overwrite this and use the protected $adapter variable if doing so
     *
     * @return Adapter_File
     */
    protected function getAdapter() {
        return $this->adapter ?: $this->adapter = new \stack\fileSystem\Adapter_File($this);
    }


    /**
     * Create the database
     */
    public function init() {
        $this->couchClient->createDatabase();
    }

    /**
     * Create a module instance via a registered factory callable
     *
     * @param string $name
     * @param mixed $data
     * @throws Exception_ModuleNotFound
     * @throws filesystem\Exception_InvalidModule
     * @return \stack\module\BaseModule_Abstract
     */
    public function createModule($name, $data) {
        // call module factory to create module
        if(!isset($this->factories[$name]))
            throw new Exception_ModuleNotFound("The module of name '$name' could not be found.");
        $module = call_user_func($this->factories[$name], $data);
        // check for validity
        if(!$module instanceof \stack\module\BaseModule) {
            throw new \stack\filesystem\Exception_InvalidModule($name, $module, $data);
        }
        return $module;
    }

    /**
     * Register a module factory callable
     *
     * @implements Interface_ModuleRegistry
     * @param string $name
     * @param \Closure $factory
     * @throws Exception_ModuleConflict|Exception_ModuleFactoryNotCallable
     * @throws Exception_ModuleConflict
     */
      public function registerModule($name, $factory) {
        if(!is_callable($factory))
            throw new Exception("The module factory '$name' is not callable.");
        if(array_key_exists($name, $this->factories))
            throw new Exception_ModuleConflict("Module with name '$name' is already registered");
        $this->factories[$name] = $factory;
    }

    /**
     * @param string $path
     * @return File
     * @throws Exception_FileNotFound
     */
    public function readFile($path) {
        try {
            $doc = $this->couchClient->getDoc("stack:/$path");
        } catch(\couchNotFoundException $e) {
            throw new \stack\fileSystem\Exception_FileNotFound("File at path '$path' could not be found", null, $e);
        }
        $file = $this->getAdapter()->fromDatabase($doc);
        return $file;
    }

    /**
     * @param \stack\fileSystem\File $file
     * @return \stack\fileSystem\File
     */
    public function writeFile(\stack\fileSystem\File $file) {
        // write the file to the file system
        // set revision to file instance
        $doc = $this->getAdapter()->toDatabase($file);
        $response = $this->couchClient->storeDoc($doc);
        $file->setRevision($response->rev);
        return $file;
    }

    /**
     * @param \stack\fileSystem\File $file
     */
    public function deleteFile(\stack\fileSystem\File $file) {
        $this->couchClient->deleteDoc($this->adapter->toDatabase($file));
    }
    /**
     * @param string $path
     * @param string $owner
     * @return \stack\fileSystem\File
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