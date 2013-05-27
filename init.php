<?
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

include STACK_ROOT_PATH . '/vendor/klawd-prime/lean/lean/init.php';

$autoload = new \lean\Autoload();
$autoload->loadLean();
$autoload->register('stack', STACK_ROOT_PATH . '/stack/lib');

require_once STACK_ROOT_PATH . '/external/PHP-on-Couch/lib/couch.php';
require_once STACK_ROOT_PATH . '/external/PHP-on-Couch/lib/couchClient.php';
require_once STACK_ROOT_PATH . '/external/PHP-on-Couch/lib/couchDocument.php';

