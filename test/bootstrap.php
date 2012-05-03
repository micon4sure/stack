<?php
namespace stack\filesystem;
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

define('STACK_TEST_ROOT', __DIR__);

// initialize lean
require '../external/lean/lean/init.php';
$autoload = new \lean\Autoload();
$autoload->loadLean();
$autoload->register('stack', STACK_TEST_ROOT . '/../stack/lib');
$autoload->register('sotest', STACK_TEST_ROOT);


require_once STACK_TEST_ROOT . '/../external/PHP-on-Couch/lib/couch.php';
require_once STACK_TEST_ROOT . '/../external/PHP-on-Couch/lib/couchClient.php';
require_once STACK_TEST_ROOT . '/../external/PHP-on-Couch/lib/couchDocument.php';

class StackOSTest extends \PHPUnit_Framework_TestCase {
    private $manager;

    public function setUp() {
        $this->manager = new \stack\filesystem\FileManager('http://root:root@127.0.0.1:5984', 'stack');
        $this->getManager()->destroy();
        // user, group, plain factory
        $this->getManager()->registerModule('\stack\filesystem\module\User');
        $this->getManager()->registerModule('\stack\filesystem\module\Group');
        $this->manager->registerModuleFactory(\stack\filesystem\module\Plain::NAME, function($data) {
            return new \stack\filesystem\module\Plain($data);
        });
        $this->getManager()->init();
    }

    /**
     * @return \stack\filesystem\FileManager
     */
    protected function getManager() {
        return $this->manager;
    }
}