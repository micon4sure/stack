<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
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