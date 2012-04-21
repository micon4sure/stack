<?
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

define('APPLICATION_ROOT', __DIR__);

include '../external/lean/lean/init.php';

$autoload = new \lean\Autoload();
$autoload->loadLean();
$autoload->register('stackos', APPLICATION_ROOT . '/lib');
?>