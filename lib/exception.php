<?php
namespace stackos;
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

class Exception extends \lean\Exception {
    public function __construct($message = '', $code = 100, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class Exception_UserNotFound extends Exception {
    public function __construct($message = '', $code = 200, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class Exception_FileNotFound extends Exception {
    public function __construct($message = '', $code = 300, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class Exception_FileExists extends Exception {
    public function __construct($message = '', $code = 300, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class Exception_UserExists extends Exception {
    public function __construct($message = '', $code = 400, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class Exception_PermissionDenied extends Exception {
    public function __construct($message = '', $code = 500, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    const PERMISSION_DENIED_CREDENTIALS_REVOKED = 501;

    const PERMISSION_CREATE_FILE_DENIED = 502;
    const PERMISSION_READ_FILE_DENIED = 503;
    const PERMISSION_WRITE_FILE_DENIED = 504;
    const PERMISSION_EXECUTE_FILE_DENIED = 505;
    const PERMISSION_DELETE_FILE_DENIED = 506;

    const PERMISSION_CREATE_USER_DENIED = 507;
    const PERMISSION_READ_USER_DENIED = 506;
    const PERMISSION_WRITE_USER_DENIED = 509;
    /** Permission to delete
     */
    const PERMISSION_EXECUTE_USER_DENIED = 510;
    const PERMISSION_DELETE_USER_DENIED = 511;

    const PERMISSION_DENIED_CANT_CREATE_ROOT_USER = 512;
    const PERMISSION_DENIED_CANT_CREATE_ROOT_FILE = 513;
}

class Exception_MissingSecurityStrategy extends Exception {
    public function __construct($message = '', $code = 600, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class Exception_RootHasNoParent extends Exception {
    public function __construct($message = '', $code = 700, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}