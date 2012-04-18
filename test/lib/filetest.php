<?php
/*
 * Copyright (C) <2012> <Michael Saller>
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

namespace test;

class FileTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \enork\Kernel
     */
    private static $kernel;

    public function setUp() {
        self::resetKernel();
    }

    private static function resetKernel() {
        self::$kernel = new \enork\Kernel('http://root:root@127.0.0.1:5984', 'enork');
        self::$kernel->destroy();
        self::$kernel->init();
    }

    public function testNoContextOnStack() {
        try {
            self::$kernel->getFile('/');
            $this->fail('Expecting Exception_MissingContext');
        }
        Catch(\enork\Exception_MissingContext $e) {
            // pass
        }
    }

    public function testGetRootFile() {
        // provoke no context on stack exception
        self::$kernel->pushContext(new \enork\kernel\RootContext());
        $root = self::$kernel->getFile('/');
        $this->assertTrue($root instanceof \enork\File);
        $this->assertEquals('root', $root->getOwner());
        $this->assertEquals($root, self::$kernel->getRootFile());
    }

    public function testGetRootHome() {
        self::$kernel->pushContext(new \enork\kernel\RootContext());
        $root = self::$kernel->getFile('/root');
        $this->assertTrue($root instanceof \enork\File);
        $this->assertEquals('root', $root->getOwner());
    }

    public function testCreateFile() {
        self::$kernel->pushContext(new \enork\kernel\RootContext());
    }

    public function testGetFileFailedPermissionDenied() {
        self::$kernel->pushContext(new \enork\kernel\UnprivilegedContext());
        try {
            self::$kernel->getFile('/');
            $this->fail('Expecting Exception_PermissionDenied');
        }
        catch(\enork\Exception_PermissionDenied $e) {
            // pass
        }
    }
}