<?php
namespace stack {
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

    date_default_timezone_set('Europe/Berlin');

    define('STACK_ROOT', realpath(__DIR__ . '/..'));
    define('STACK_TEST_ROOT', __DIR__);

    // initialize lean
    require '../external/lean/lean/init.php';
    $autoload = new \lean\Autoload();
    $autoload->loadLean();
    $autoload->register('stack', STACK_TEST_ROOT . '/../stack/lib');

    require_once STACK_TEST_ROOT . '/../external/PHP-on-Couch/lib/couch.php';
    require_once STACK_TEST_ROOT . '/../external/PHP-on-Couch/lib/couchClient.php';
    require_once STACK_TEST_ROOT . '/../external/PHP-on-Couch/lib/couchDocument.php';

    // create register environment
    $env = new Environment('test_dev');
    $env->createFileManager();
    // nuke database
    $env->createShell()->nuke();
    $registry = \lean\Registry::instance();
    $registry->set('stack.environment', $env);

    class TestContext extends Context {
        // Expose manager in test context
        /**
         * @return filesystem\FileManager
         */
        public function getFileManager() {
            return parent::getFileManager();
        }
    }

    class StackOSTest extends \PHPUnit_Framework_TestCase {
        /**
         * @var TestContext
         */
        protected $context;
        /**
         * @var \lean\Migration_Manager
         */
        protected static $migration;

        private static function getMigrationManager() {
            // need to make sure there's only one instance for migration manager reads the files outright
            return self::$migration ?: self::$migration = new \lean\Migration_Manager(STACK_ROOT . '/stack/migration');
        }

        protected function getFileManager() {
            return $this->context->getFileManager();
        }

        public function setUp() {
            // create test environment
            $env = new Environment('test_dev');
            // create context around environment
            $this->context = new TestContext($env);
            // put created context into static registry
            \lean\Registry::instance()->set('stack.context', $this->context);

            // registering module factories from the outside
            $this->context->getShell()->registerModule('stack.plain', function($data) {
                return new \stack\module\Plain($data);
            });
            $this->context->getShell()->registerModule('stack.user', function($data) {
                return \stack\module\User::create($data);
            });
            $this->context->getShell()->registerModule('stack.group', function($data) {
                return \stack\module\Group::create($data);
            });
            $this->context->getShell()->registerModule('stack.system.adduser', function($data) {
                return \stack\module\run\AddUser::create($data);
            });
            $this->context->getShell()->registerModule('stack.system.deluser', function($data) {
                return \stack\module\run\DelUser::create($data);
            });
            $this->context->getShell()->registerModule('stack.system.addgroup', function($data) {
                return \stack\module\run\AddGroup::create($data);
            });
            $this->context->getShell()->registerModule('stack.system.delgroup', function($data) {
                return \stack\module\run\DelGroup::create($data);
            });

            // nuke and reset shell back into clean state
            $this->context->getShell()->nuke();
            self::getMigrationManager()->reset();
            self::getMigrationManager()->upgrade();
        }
    }
}

namespace stack\filesystem {
    class StackOSTest extends \stack\StackosTest {
    }
}
