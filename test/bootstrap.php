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
    $env->createManager();
    // nuke database
    $env->createShell()->nuke();
    $registry = \lean\Registry_Stateless::instance();
    $registry->set('stack.environment', $env);

    class StackOSTest extends \PHPUnit_Framework_TestCase {
        protected $manager;
        protected static $migration;

        private static function getMigration() {
            // need to make sure there's only one instance for migration manager reads the files outright
            return self::$migration ?: self::$migration = new \lean\Migration_Manager(STACK_ROOT . '/stack/migration');
        }


        public function setUp() {
            $env = new Environment('test_dev');
            $this->manager = $env->createManager();
            $env->createShell()->nuke();
            self::getMigration()->reset();
            self::getMigration()->upgrade();

            // module factories
            $this->getManager()->registerModule('\stack\module\User');
            $this->getManager()->registerModule('\stack\module\Group');
            $this->getManager()->registerModule('\stack\module\AddUser');
            $this->getManager()->registerModule('\stack\module\AddGroup');
            $this->getManager()->registerModule('\stack\module\DelUser');
            $this->getManager()->registerModule('\stack\module\DelGroup');
            $this->manager->registerModuleFactory(\stack\module\Plain::NAME, function($data) {
                return new \stack\module\Plain($data);
            });
        }

        /**
         * @return \stack\filesystem\FileManager
         */
        protected function getManager() {
            return $this->manager;
        }
    }
}

namespace stack\filesystem {
    class StackOSTest extends \stack\StackosTest {
    }
}
