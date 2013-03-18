<?
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('STACK_ROOT', __DIR__);

// initialize lean
include STACK_ROOT . '/vendor/lean/lean/lean/init.php';
$autoload = new \lean\Autoload();
$autoload->loadLean();
$autoload->register('stack', STACK_APPLICATION_ROOT . '/../stack/lib');

// initialize php on couch
require_once STACK_ROOT . '/vendor/dready92/php-on-couch/lib/couch.php';
require_once STACK_ROOT . '/vendor/dready92/php-on-couch/lib/couchClient.php';
require_once STACK_ROOT . '/vendor/dready92/php-on-couch/lib/couchDocument.php';
