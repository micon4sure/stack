<?php
namespace stack\filesystem;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */


/**
 * Main exception class for the fileSystem namespace
 */
class Exception extends \stack\Exception {

}

/**
 * An exception in the couchDB abstraction layer occured
 */
class Exception_Couch extends Exception {
}

/**
 * A file could not be found
 */
class Exception_FileNotFound extends Exception_Couch {
}

/**
 * A module that was registered by name was not found
 */
class Exception_ModuleNotFound extends Exception {
}

/**
 * Tried to add a module under a name that was already taken
 */
class Exception_ModuleConflict extends Exception_Couch {
}

/**
 * Modules are being produced by ModuleFactorys. These need to be callables.
 */
class Exception_ModuleFactoryNotCallable extends Exception_Couch {
}

/**
 * The module was not of the expected type
 */
class Exception_InvalidModule extends \Exception {
    private $moduleName;
    private $module;
    private $data;
    public function __construct($moduleName, $module, $data) {
        $this->moduleName = $moduleName;
        $this->module = $module;
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function getModule() {
        return $this->module;
    }

    public function getModuleName() {
        return $this->moduleName;
    }
}

/**
 * The Permission to access a file was denied by the Security layer
 */
class Exception_PermissionDenied extends Exception {
}

/**
 * Indicates that access to security was requested while there is none
 */
class Exception_NoSecurity extends Exception {

}