<?php
namespace stack;
/*
* Copyright (C) 2012 Michael Saller
* Licensed under MIT License, see /path/to/stack/LICENSE
*/

date_default_timezone_set('Europe/Berlin');

ini_set('display_errors', 1);
error_reporting(E_ALL);
define('STACK_ROOT', realpath(__DIR__ . '/..'));
define('STACK_ROOT_WWW', STACK_ROOT . '/www');

// initialize lean
require STACK_ROOT . '/external/lean/lean/init.php';
$autoload = new \lean\Autoload();
$autoload->loadLean();
$autoload->register('stack', STACK_ROOT . '/stack/lib');

// load external lib PHP-on-Couch
require STACK_ROOT . '/external/PHP-on-Couch/lib/couch.php';
require STACK_ROOT . '/external/PHP-on-Couch/lib/couchClient.php';
require STACK_ROOT . '/external/PHP-on-Couch/lib/couchDocument.php';