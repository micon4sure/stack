<?
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

define('STACK_APPLICATION_ROOT', __DIR__);

include '../external/lean/lean/init.php';

$autoload = new \lean\Autoload();
$autoload->loadLean();
$autoload->register('stack', STACK_APPLICATION_ROOT . '/lib');
?>