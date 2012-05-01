<?php
namespace stackos;
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

define('STOS_TEST_ROOT', __DIR__);

// initialize lean
require '../external/lean/lean/init.php';
$autoload = new \lean\Autoload();
$autoload->loadLean();
$autoload->register('stackos', STOS_TEST_ROOT . '/../lib');
$autoload->register('sotest', STOS_TEST_ROOT);


require_once STOS_TEST_ROOT . '/../external/PHP-on-Couch/lib/couch.php';
require_once STOS_TEST_ROOT . '/../external/PHP-on-Couch/lib/couchClient.php';
require_once STOS_TEST_ROOT . '/../external/PHP-on-Couch/lib/couchDocument.php';

class StackOSTest extends \PHPUnit_Framework_TestCase {
    private $manager;

    public function setUp() {
        $this->manager = new \stackos\DocumentManager('http://root:root@127.0.0.1:5984', 'stackos');
        $this->getManager()->destroy();
        // user, group, plain factory
        $this->getManager()->registerModule('\stackos\module\UserModule');
        $this->getManager()->registerModule('\stackos\module\GroupModule');
        $this->manager->registerModuleFactory(\stackos\module\PlainModule::NAME, function($data) {
            return new \stackos\module\PlainModule($data);
        });
        $this->getManager()->init();
    }

    /**
     * @return \stackos\DocumentManager
     */
    protected function getManager() {
        return $this->manager;
    }
}