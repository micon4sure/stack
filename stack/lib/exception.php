<?php
namespace stack;
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
 * Stack root exception
 * Every Exception inside stack should extend this or a deriving class
 */
class Exception extends \Exception {

}

/**
 * A user was not found
 */
class Exception_UserNotFound extends \Exception {

}

/**
 * A group was not found
 */
class Exception_GroupNotFound extends \Exception {

}

/**
 * A file could not be executed
 */
class Exception_ExecutionError extends \Exception {

}

/**
 * Can't perform this action without being logged in
 */
class Exception_NeedToBeLoggedIn extends \Exception {

}

/**
 * The module in a user file was corrupt
 */
class Exception_CorruptModuleInUserFile extends \Exception {

}

/**
 * The module could not be found
 */
class Exception_ModuleNotFound extends \Exception {

}