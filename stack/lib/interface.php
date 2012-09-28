<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * I know how to decide if a file may be accessed or not.
 */
interface Interface_Security {
    /**
     * Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param \stack\filesystem\File  $file
     * @param string                  $priviledge
     * @return bool
     */
    public function checkFilePermission(\stack\filesystem\File $file, $priviledge);
}

/**
 * I know how to hold module factories.
 */
interface Interface_ModuleRegistry {
    /**
     * Register a module factory callable
     *
     * @param string $name
     * @param callable $factory
     * @throws Exception_ModuleConflict|Exception_ModuleFactoryNotCallable
     * @throws Exception_ModuleConflict
     */
    public function registerModule($name, $factory);
}

/**
 * I know how to adapt couchDB documents to File objects and back.
 */
interface Interface_Adapter {
    public function fromDatabase($doc);

    public function toDatabase(\stack\filesystem\File $doc);
}

/**
 * I know how to handle a security stack.
 */
interface Interface_SecurityAccess {
    /**
     * @abstract
     * @param Interface_Security $security
     * @return mixed
     */
    public function pushSecurity(Interface_Security $security);
    /**
     * @abstract
     * @return mixed
     */
    public function pullSecurity();
}

/**
 * I know how to handle files.
 */
interface Interface_FileAccess {
    /**
     * @param string $path
     * @return \stdClass
     * @throws Exception_FileNotFound
     */
    public function readFile($path);
    /**
     * @abstract
     * @param filesystem\File $file
     * @return mixed
     */
    public function writeFile(\stack\filesystem\File $file);
    /**
     * @abstract
     * @param filesystem\File $file
     * @return mixed
     */
    public function deleteFile(\stack\filesystem\File $file);
    /**
     * Factory reset method
     * @return void
     */
    public function nuke();
}

/**
 * I know how to convert objects of my type from and to json
 */
interface Interface_JSONizable {
    /**
     * @abstract
     * @return mixed
     */
    public function fromJSON();
    /**
     * @abstract
     * @return string
     */
    public function toJSON();
}

/**
 * I have assets and know where to find them
 */
interface Interface_WebAssetProvider {
    /**
     * Get a complete absolute (web) path
     *
     * @param $asset
     *
     * @return mixed
     */
    public function getAssetWebPath($asset);

    /**
     * Get a complete absolute filesystem path
     *
     * @param $asset
     *
     * @return mixed
     */
    public function getAssetPath($asset);
}