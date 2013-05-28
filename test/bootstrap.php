<?php
namespace stack\test;
/*
* Copyright (C) 2012 Michael Saller
* Licensed under MIT License, see /path/to/stack/LICENSE
*/


use lean\Registry;
use lean\util\Dump;
use stack\Environment;

date_default_timezone_set('Europe/Berlin');

define('STACK_ROOT', realpath(__DIR__ . '/..'));
define('STACK_APPLICATION_ROOT', __DIR__);

include STACK_ROOT . '/init.php';

// initialize lean
include STACK_ROOT . '/vendor/klawd-prime/lean/lean/init.php';
$autoload = new \lean\Autoload();
$autoload->loadLean();
$autoload->register('stack', STACK_APPLICATION_ROOT . '/../stack/lib');

class StackTest extends \PHPUnit_Framework_TestCase {

    protected $environment;
    protected $client;

    public function setUp() {
        // create environment
        $this->environment = new Environment('test');
        $client = $this->client = new \couchClient($this->environment->get('stack.database.dsn'), $this->environment->get('stack.database.name'));
        if($client->databaseExists()) {
            $client->deleteDatabase();
        }
        $client->createDatabase();
    }

    protected function createTestDocument() {
        $doc = new \stdClass;

        // path and owner
        $doc->_id = '/foo';
        $doc->owner = 'klawd';

        // data
        $doc->data = new \stdClass;
        $doc->data->foo = 'bar';
        return $doc;
    }
}