<?php
namespace stack\fileSystem;
/*
 * Copyright (C) 2012 Michael Saller
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
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