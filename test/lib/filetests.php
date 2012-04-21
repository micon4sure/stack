<?php
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

namespace test;

class FileTests extends \PHPUnit_Framework_TestCase {
    /**
     * @var \stackos\Kernel
     */
    private static $kernel;

    /**
     * Reset the kernel before each test.
     */
    public function setUp() {
        self::resetKernel();
    }

    /**
     * @static
     * Destroy the underlying couchDB and rebuild it.
     */
    private static function resetKernel() {
        self::$kernel = new \stackos\Kernel('http://root:root@127.0.0.1:5984', 'stackos');
        self::$kernel->destroy();
        self::$kernel->init();
    }

    public function testGetRootUser() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $this->assertTrue(self::$kernel->getRootUser() === self::$kernel->getRootUser());
    }

    public function testGetRootFile() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $this->assertTrue(self::$kernel->getRootFile() === self::$kernel->getRootFile());
    }

    public function testGetRootHome() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $root = self::$kernel->getFile(self::$kernel->getRootUser(), '/root');
        $this->assertTrue($root instanceof \stackos\File);
        $this->assertEquals('root', $root->getOwner());
        $this->assertEquals('/root', $root->getPath());
    }

    public function testCreateFile() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        // TODO
    }

    public function testGetFileFailedPermissionDenied() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\UserStrategy(self::$kernel, self::getNoname()));
        try {
            self::$kernel->getFile(self::getNoname(), '/');
            $this->fail('Expecting Exception_PermissionDenied');
        }
        catch(\stackos\Exception_PermissionDenied $e) {
            // pass
        }
    }


    protected static function getNoname() {
        return new \stackos\User(self::$kernel, 'noname');
    }
}