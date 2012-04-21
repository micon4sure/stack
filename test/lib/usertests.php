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

class UserTests extends \PHPUnit_Framework_TestCase {
    /**
     * @var \stackos\Kernel
     */
    private static $kernel;

    public function setUp() {
        self::resetKernel();
    }

    private static function resetKernel() {
        self::$kernel = new \stackos\Kernel('http://root:root@127.0.0.1:5984', 'stackos');
        self::$kernel->destroy();
        self::$kernel->init();
    }

    public function testGetRootUser() {
        $root = self::$kernel->getUser('root');
        $this->assertTrue($root instanceof \stackos\User);
        $this->assertEquals('/root', $root->getHome());
        $this->assertEquals(array('root'), $root->getGroups());
        $this->assertEquals($root, self::$kernel->getRootUser());
    }

    public function testGetUnknownUser() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        try {
            self::$kernel->getUser('unknown');
            $this->fail('Expecting Exception_UserNotFound');
        }
        catch(\stackos\Exception_UserNotFound $e) {
            // pass
        }
    }

    public function testCreateUser() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $user = new \stackos\User(self::$kernel, 'test', array(), '/home/test');
        self::$kernel->createUser($user);
    }

    public function testCreateuserFailPermissionDenied() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $user = new \stackos\User(self::$kernel, 'test', array(), '/home/test');
        self::$kernel->createUser($user);

        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\UserStrategy(self::$kernel, $user));
        try {
            self::$kernel->createUser($user);
            $this->fail('Expecting Exception_PermissionDenied');
        }
        catch(\stackos\Exception_PermissionDenied $e) {
            // pass
        }
    }

    public function testCreateUserFailUserExists() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $user = new \stackos\User(self::$kernel, 'test', array(), '/home/test');
        self::$kernel->createUser($user);

        try {
            self::$kernel->createUser($user);
            $this->fail('Expecting Exception_UserExists');
        }
        catch(\stackos\Exception_UserExists $e) {
            // pass
        }
    }

    public function testGetRootUserLazy() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $rootUser = self::$kernel->getRootUser();
        $this->assertNotNull($rootUser === self::$kernel->getRootUser());
        $this->assertTrue($rootUser === self::$kernel->getRootUser());
    }

    public function testGetRootFileLazy() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $rootFile = self::$kernel->getRootFile();
        $this->assertNotNull($rootFile === self::$kernel->getRootFile());
        $this->assertTrue($rootFile === self::$kernel->getRootFile());
    }
}