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

class AdhocStrategyTests extends \StackOSTest {

    public function testDefault() {
        $adhoc = new \stackos\kernel\security\AdhocStrategy(self::$kernel);
        $uber = $this->getNoname('uber');
        $uber->setUber(true);
        $document =  new \stackos\Document(self::$kernel, array());
        $adhoc->checkDocumentPermission($uber,$document, \stackos\kernel\security\Priviledge::WRITE);
    }

    public function testUserCreate() {
        $unprivilegedStrategy = new \stackos\kernel\security\UnprivilegedStrategy();
        $adhoc = new \stackos\kernel\security\AdhocStrategy(self::$kernel, $unprivilegedStrategy);

        // userCreatePermission
        $this->assertFalse($adhoc->checkUserCreatePermission(self::getNoname()));
        $adhoc->setCallback('checkUserCreatePermission', function(\stackos\User $user) {
            return true;
        });
        $this->assertTrue($adhoc->checkUserCreatePermission(self::getNoname()));
    }

    public function testUserDelete() {
        $unprivilegedStrategy = new \stackos\kernel\security\UnprivilegedStrategy();
        $adhoc = new \stackos\kernel\security\AdhocStrategy(self::$kernel, $unprivilegedStrategy);

        // userDeletePermission
        $this->assertFalse($adhoc->checkUserDeletePermission(self::getNoname(), self::getNoname()));
        $adhoc->setCallback('checkUserDeletePermission', function(\stackos\User $user) {
            return true;
        });
        $this->assertTrue($adhoc->checkUserDeletePermission(self::getNoname()));
    }

    public function testAdhocStrategyUserCreate() {
        $unprivilegedStrategy = new \stackos\kernel\security\UnprivilegedStrategy();
        $adhoc = new \stackos\kernel\security\AdhocStrategy(self::$kernel, $unprivilegedStrategy);
        $document = new \stackos\Document(self::$kernel);

        // documentPermission
        $this->assertFalse($adhoc->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::READ));
        $adhoc->setCallback('checkDocumentPermission', function(\stackos\User $user, \stackos\Document $document, $permission) {
            return true;
        });
        $this->assertTrue($adhoc->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::READ));
    }

}