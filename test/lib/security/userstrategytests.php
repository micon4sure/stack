<?php
namespace test\security;
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


class UserStrategyTests extends \StackOSTest {

    public function testPermissionOnRootFileDenied() {
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, self::getNoname());
        $file = $this->getFile('/', self::getRootUser());
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $file, \stackos\kernel\security\Priviledge::WRITE));
    }

    public function testPermissionToRootUserGranted() {
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, self::getNoname());
        $file = $this->getFile('/', self::getRootUser());
        $this->assertTrue($strategy->checkDocumentPermission(self::getRootUser(), $file, \stackos\kernel\security\Priviledge::WRITE));
    }

    public function testGetKernel() {
        $strategy = new BaseStrategyTests_Mock_Strategy(self::$kernel);
        $this->assertTrue(self::$kernel === $strategy->getKernel());
    }

    public function testCheckUserCreatePermission() {
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, self::getNoname());
        // test if root can create user
        $this->assertTrue($strategy->checkUserCreatePermission(self::getRootUser()));
        // test if noname can not create user
        $this->assertFalse($strategy->checkUserCreatePermission());
    }

    public function testCheckUserDeletePermission() {
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, self::getNoname());
        // test if root can delete user
        $this->assertTrue($strategy->checkUserDeletePermission(self::getRootUser()));
        // test if noname can not delete user
        $this->assertFalse($strategy->checkUserDeletePermission(self::getNoname()));
    }

    public function testCheckDocumentUserPermission() {
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, self::getNoname());
        $document = new \stackos\Document(self::$kernel);

        // make sure noname has no permissions on the document
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::EXECUTE));

        // grant read priviledge
        $document->addPermission(new \stackos\kernel\security\Permission_User(self::getNoname()->getUname(), \stackos\kernel\security\Priviledge::READ));
        $this->assertTrue($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::EXECUTE));

        // grant write priviledge
        $document->addPermission(new \stackos\kernel\security\Permission_User(self::getNoname()->getUname(), \stackos\kernel\security\Priviledge::WRITE));
        $this->assertTrue($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertTrue($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::EXECUTE));

        // grant execute priviledge
        $document->addPermission(new \stackos\kernel\security\Permission_User(self::getNoname()->getUname(), \stackos\kernel\security\Priviledge::EXECUTE));
        $this->assertTrue($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertTrue($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertTrue($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::EXECUTE));
    }

    public function testCheckDocumentGroupPermission() {
        $strategy = new \stackos\kernel\security\UserStrategy(self::$kernel, self::getNoname());
        $document = new \stackos\Document(self::$kernel);
        $user = self::getNoname();
        $user->addToGroup('test');

        // make sure noname has no permissions on the document
        $this->assertFalse($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertFalse($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertFalse($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::EXECUTE));

        // grant read priviledge
        $document->addPermission(new \stackos\kernel\security\Permission_Group('test', \stackos\kernel\security\Priviledge::READ));
        $this->assertTrue($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertFalse($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertFalse($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::EXECUTE));

        // grant write priviledge
        $document->addPermission(new \stackos\kernel\security\Permission_Group('test', \stackos\kernel\security\Priviledge::WRITE));
        $this->assertTrue($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertTrue($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertFalse($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::EXECUTE));

        // grant execute priviledge
        $document->addPermission(new \stackos\kernel\security\Permission_Group('test', \stackos\kernel\security\Priviledge::EXECUTE));
        $this->assertTrue($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertTrue($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertTrue($strategy->checkDocumentPermission($user, $document, \stackos\kernel\security\Priviledge::EXECUTE));
    }
}