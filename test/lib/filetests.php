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

class FileTests extends \StackOSTest {

    public function testFileNotFound() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        try {
            self::$kernel->getFile(self::getNoname(), '/noname');
            $this->fail('Expecting Exception_FileNotFound');
        }
        catch(\stackos\Exception_FileNotFound $e) {
            // pass
        }
    }

    public function testCreateFile() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $file = new \stackos\File(self::$kernel, '/test', self::getNoname()->getUname());
        self::$kernel->createFile(self::getNoname(), $file);
    }

    public function testFileExists() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $file = new \stackos\File(self::$kernel, '/test', self::getNoname()->getUname());
        self::$kernel->createFile(self::getNoname(), $file);
        try {
            self::$kernel->createFile(self::getNoname(), $file);
            $this->fail('Expecting Exception_FileExists');
        }
        catch(\stackos\Exception_FileExists $e) {
            // pass
        }
    }
    public function testGetParent() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $user = self::getNoname();

        // create files
        self::$kernel->createFile(self::getNoname(), new \stackos\File(self::$kernel, '/foo', $user->getUname()), \stackos\ROOT_PATH_USERS);
        self::$kernel->createFile(self::getNoname(), new \stackos\File(self::$kernel, '/foo/bar', $user->getUname()), \stackos\ROOT_PATH_USERS);

        // read /foo/bar
        $this->assertTrue(self::$kernel->getFile($user, '/foo/bar') instanceof \stackos\File);
        $this->assertTrue(self::$kernel->getFile($user, '/foo') instanceof \stackos\File);
    }

    public function testRootHasNoParent() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $user = self::getNoname();

        // read /foo/bar
        $file = self::$kernel->getFile($user, '/');
        try {
            $file->getParent($user);
            $this->fail("Excepcting Exception_RootHasNoParent");
        } catch (\stackos\Exception_RootHasNoParent $e) {
            // pass
        }
    }

    public function testOwnerAccess() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $user = self::getNoname();

        // check get and set owner
        $file = new \stackos\File(self::$kernel, '/foo', self::getNoname('owner')->getUname());
        $this->assertEquals(
          $file->getOwner(),
            self::getNoname('owner')->getUname()
        );

        $file->setOwner($user->getUname());
        $this->assertNotEquals(
            $file->getOwner(),
            self::getNoname('owner')->getUname()
        );
        $this->assertEquals(
            $file->getOwner(),
            self::getNoname()->getUname()
        );
    }

    public function testSetPathAccess() {

    }

    // === permissions ===
    public function testCreateFilePermissionDeniedCantWriteParent() {
        $file = new \stackos\File(self::$kernel, '/foo', self::getNoname('owner')->getUname());

        // try and see if file
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\UnprivilegedStrategy());
        try {
            self::$kernel->createFile(self::getNoname('requestant'), $file);
            $this->fail('Expecting Exception_PermissionDenied');
        }
        catch(\stackos\Exception_PermissionDenied $e) {
            if($e->getCode() != \stackos\Exception_PermissionDenied::PERMISSION_CREATE_FILE_DENIED)
                throw $e;
            // pass
        }
    }

    public function testGetFilePermissionDenied() {
        self::$kernel->pushSecurityStrategy(new \stackos\kernel\security\UnprivilegedStrategy());
        try {
            self::$kernel->getFile(self::getNoname(), '/');
            $this->fail('Expecting Exception_PermissionDenied');
        }
        catch(\stackos\Exception_PermissionDenied $e) {
            // pass
        }
    }
}

class FileTests_Mock_Document extends \stackos\Document {
    public function getKernel() {
        return parent::getKernel();
    }
}