<?php
/*
* Copyright (C) 2012 Michael Saller
* Licensed under MIT License, see /path/to/stack/LICENSE
*/
namespace stack {

    date_default_timezone_set('Europe/Berlin');

    define('STACK_ROOT', realpath(__DIR__ . '/..'));
    define('STACK_APPLICATION_ROOT', __DIR__);

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // initialize lean
    require '../external/lean/lean/init.php';
    $autoload = new \lean\Autoload();
    $autoload->loadLean();
    $autoload->register('stack', STACK_APPLICATION_ROOT . '/../stack/lib');

    require_once STACK_ROOT . '/external/PHP-on-Couch/lib/couch.php';
    require_once STACK_ROOT . '/external/PHP-on-Couch/lib/couchClient.php';
    require_once STACK_ROOT . '/external/PHP-on-Couch/lib/couchDocument.php';

    class TestContext extends Context {
        /**
         * Expose fs in test context
         *
         * @return FileSystem
         */
        public function getFileSystem() {
            return parent::getFileSystem();
        }
    }

    // create environment
    $env = new Environment('test_dev');
    // nuke database
    $context = new TestContext($env);
    $context->getFileSystem()->nuke();
    $registry = \lean\Registry::instance();
    $registry->set('stack.environment', $env);

    class StackOSTest extends \PHPUnit_Framework_TestCase {
        /**
         * @var TestContext
         */
        protected $context;
        /**
         * @var \lean\Migration_Manager
         */
        protected static $migration;

        /**
         * @var Application
         */
        protected $application;

        /**
         * @static
         * @return \lean\Migration_Manager
         */
        private static function getMigrationManager() {
            // need to make sure there's only one instance for migration manager; reads the files outright
            return self::$migration ?: self::$migration = new \lean\Migration_Manager(STACK_ROOT . '/stack/migration');
        }

        /**
         * @return FileSystem
         */
        protected function getFileSystem() {
            return $this->context->getFileSystem();
        }

        public function setUp() {
            // create test environment
            $env = new Environment('test_dev');
            $this->context = new TestContext($env);
            $this->application = new Application($this->context);
            (new Bundle_Web($this->application))->registerModules($this->context->getShell());

            // nuke and reset shell back into clean state
            self::getMigrationManager()->reset();
            self::getMigrationManager()->upgrade();
        }
    }
}

namespace stack\fileSystem {
    class StackOSTest extends \stack\StackosTest {
    }
}
