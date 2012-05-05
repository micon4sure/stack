<?php
namespace stack\filesystem;
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
class Exception_ModuleNotFound extends Exception_Couch {
}

/**
 * A database file had a module name in meta but no data under module
 */
class Exception_ModuleDataNotFound extends Exception_Couch {
}

/**
 * A module in a file was not of the expected type
 */
class Exception_UnexpectedModuleType extends Exception_Couch {
}

/**
 * A file could not be found
 */
class Exception_ModuleConflict extends Exception_Couch {
    public function __construct($message, $code = self::MODULE_WITH_NAME_ALREADY_REGISTERED, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    const MODULE_WITH_NAME_ALREADY_REGISTERED = 200;
}


class Exception_ModuleFactoryNotCallable extends Exception_Couch {
}

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

class Exception_PermissionDenied extends Exception {
}

class Exception_NeedAccess extends Exception {

}

/**
 * Indicates that access to security was requested while there is none
 */
class Exception_NoSecurity extends Exception {

}

class Exception_UserNotFound extends Exception {

}