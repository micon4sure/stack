<?php
namespace enork;

class Exception extends \lean\Exception {}

class Exception_UserNotFound extends Exception {

}
class Exception_UserExists extends Exception {

}
class Exception_PermissionDenied extends Exception {

}

class Exception_MissingContext extends Exception {

}