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

class PermissionTests extends \StackOSTest {
    public function setUp() {
        self::resetKernel();
    }

    private static function resetKernel() {
        self::$kernel = new \stackos\Kernel('http://root:root@127.0.0.1:5984', 'stackos');
        self::$kernel->destroy();
        self::$kernel->init();
    }

    /** Test if $uber has access to $file owned by $user
     */
    public function testCheckUber() {
        $uber = new \stackos\User(self::$kernel, 'uber', array());
        $uber->setUber(true);
        $joe = new \stackos\User(self::$kernel, 'user', array());
        $file = new \stackos\File(self::$kernel, '/ubertest', $joe->getUname(), array());

        $this->assertTrue($this->checkPermissions($uber, $file));
    }

    /** Test if $owner has access to file with empty groups and empty permissions
     */
    public function testCheckOwner() {
        // create user and file,
        $owner = new \stackos\User(self::$kernel, 'owner', array());
        $file = new \stackos\File(self::$kernel, '/ownertest', $owner->getUname(), array());
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, $owner);
        $check = $strategy->checkDocumentPermission($owner, $file, \stackos\kernel\security\Strategy::PERMISSION_TYPE_READ)
              && $strategy->checkDocumentPermission($owner, $file, \stackos\kernel\security\Strategy::PERMISSION_TYPE_WRITE);

        // test implicit owner file permissions
        $this->assertTrue($check);

        // test owner no implicit execute permission
        $check = $strategy->checkDocumentPermission($owner, $file, \stackos\kernel\security\Strategy::PERMISSION_TYPE_EXECUTE);
        $this->assertFalse($check);
    }

    public function testGroupPermission() {
        $noname = self::getNoname();
        $file = new \stackos\File(self::$kernel, '/group_permission_test', $noname->getUname());

        $this->assertFalse($this->checkPermissions($noname, $file));

        // add user to group share
        $noname->addToGroup('share');
        $file->addPermission(new \stackos\kernel\security\Permission_Group('share', \stackos\kernel\security\Strategy::PERMISSION_TYPE_READ));
        // and assert that they have permission
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, $noname);
        $check = $strategy->checkDocumentPermission($noname, $file, \stackos\kernel\security\Strategy::PERMISSION_TYPE_READ);
        $this->assertTrue($check);
    }

    protected function checkPermissions($user, $file) {
        // create new mock context exposing the checkPermissions method
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, $user);
        return $strategy->checkDocumentPermission($user, $file, \stackos\kernel\security\Strategy::PERMISSION_TYPE_READ)
            && $strategy->checkDocumentPermission($user, $file, \stackos\kernel\security\Strategy::PERMISSION_TYPE_WRITE)
            && $strategy->checkDocumentPermission($user, $file, \stackos\kernel\security\Strategy::PERMISSION_TYPE_EXECUTE);
    }

    public function testPopEmptyContext() {
        try {
            self::$kernel->popSecurityStrategy();
            $this->fail('Expecting Exception_MissingContext');
        }
        catch (\stackos\Exception_MissingSecurityStrategy $e) {
            // pass
        }
    }
}